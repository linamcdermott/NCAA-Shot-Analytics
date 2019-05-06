"""
Data importer for NCAA Divsion I basketball shots taken from the 
2013-2014 season to the 2018-2019 season.

Authors: Ana Hayne, Alex Hazan, Lina McDermott, and Matt Wang
"""
import pymongo
from pymongo import MongoClient
import csv

def importData():
    """
    Imports the shot data, drafted player data, and tournament team data.
    """
    client = MongoClient()

    db = client.baskets

    team = db["team"]
    shot = db["shot"]
    
    x = team.delete_many({})
    y = shot.delete_many({})

    team.drop_indexes()
    shot.drop_indexes()

    team.create_index([('school', pymongo.ASCENDING),('season',pymongo.ASCENDING)], unique=True)

    # Get list of players drafted into the NBA.
    drafted_list = []

    filename = "drafted.txt"
    in_file = open(filename, 'r')
    for line in in_file:
        line = line.strip('\n')
        drafted_list.append(line)

    in_file.close()

    # Get shot data. 
    for i in range(13,19):
        season = "20"+ str(i) + "-20" + str(i+1)

        # FOR TRACKING PROGRESS IMPORTING DATA
        print season

        filename = "ncaa-shots-" + str(i) + "-" + str(i+1) + ".csv"
        with open(filename) as csv_file:
            csv_reader = csv.reader(csv_file, delimiter=',')

            #read col headers
            csv_reader.next()

            season = "20"+ str(i) + "-20" + str(i+1)

            for row in csv_reader:

                if any (row):
                    # Get school names
                    school_1 = row[1]

                    # Adjust for inconsistencies in school names.
                    if(row[1][-3:] == " ST"):
                        school_1 = row[1] + "ATE"

                    school_2 = row[2]
                    if(row[2][-2:] == "ST"):
                        school_2 = row[2] + "ATE"
                    
                    school = row[1]
                    if(row[1][-3:] == " ST"):
                       school = row[1] + "ATE"

                    # Check who is home and who is away.
                    if(row[6] == "HOME"):
                        home_bool = True
                        home_or_away = "home"
                        home_team = school_1
                        away_team = school_2
                    else:
                        home_or_away = "away"
                        home_bool = False
                        home_team = school_2
                        away_team = school_1

                    # Check if shot was made.
                    if(row[7] == "MADE"):
                        make_bool = True
                    else:
                        make_bool = False

                    # Update LAMA.
                    shot_type = row[8]
                    if (shot_type == "THREE POINT JUMPER" or shot_type == "DUNK" or shot_type == "LAYUP"
                        or shot_type == "TWO POINT TIP SHOT"):
                        lama_bool = True
                    
                    # Update points.
                    if (make_bool):
                        if (shot_type == "THREE POINT JUMPER"):
                            points = 3
                        else:
                            points = 2
                    else:
                        points = 0

                    # Check if player was drafted
                    if(row[5] in drafted_list):
                        drafted_bool = True
                    else:
                        drafted_bool = False

                    # Check if team exists
                    team_obj = team.find_one({
                            "$and" : [
                                {"season": season}, {"school": school}]})

                    # If the team doesn't exist, create a document for it.
                    if not team_obj:
                        team_doc = {
                        "school" : school,
                        "season" : season,
                        "tournament": False
                        }
                        team_id = team.insert_one(team_doc).inserted_id
                    else:
                        team_id = team_obj.get("_id")
                    
                    # Only include shots that are not beyond half-court.
                    if (float(row[3]) <= 50.0):
                        # Create shot doc.
                        shot_doc = {
                            "team_id": team_id,
                            "date": row[0],
                            "season": season,
                            "xloc" :row[3],
                            "yloc" : row[4],
                            "player_name" : row[5],
                            "player_drafted": drafted_bool,
                            "made" : make_bool,
                            "type": row[8],
                            "assist" : row[9],
                            "points": points,
                            "LAMA": lama_bool
                        }
                        shot_id = shot.insert_one(shot_doc)

    # Set tournament booleans.
    filename = "tournament-teams.csv"
    with open(filename) as csv_file:
        csv_reader = csv.reader(csv_file, delimiter=',')

        for row in csv_reader:
            if any (row):
                myquery = {"$and" : [
                                {"season": row[0]}, {"school": row[1]}]}
                newvals = {"$set": {"tournament": True }}

                team.update_one(myquery, newvals)
 
def main():
    importData()

if __name__ == '__main__':
    main()
