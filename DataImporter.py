import pymongo
from pymongo import MongoClient
import csv

def importData():

    client = MongoClient()

    db = client.baskets

    team = db["team"]
    game = db["game"]
    
    x = team.delete_many({})
    x = game.delete_many({})

    team.drop_indexes()
    game.drop_indexes()

    team.create_index([('school', pymongo.ASCENDING)], unique=True)
    game.create_index([('date', pymongo.ASCENDING), ("home", pymongo.ASCENDING ), ('away', pymongo.ASCENDING)], unique=True)

    drafted_list = []

    filename = "drafted.txt"
    in_file = open(filename, 'r')
    for line in in_file:
        line = line.strip('\n')
        drafted_list.append(line)

    in_file.close()



    for i in range(13,19):

        filename = "ncaa-shots-" + str(i) + "-" + str(i+1) + ".csv"
        with open(filename) as csv_file:
            csv_reader = csv.reader(csv_file, delimiter=',')

            #read col headers
            csv_reader.next()

            for row in csv_reader:

                if any (row):
                    """
                    (1) If both the team and the game exist, update the game object with the current shot.
                    (2) If the team exists and the game does not, create and add the game object with the current shot
                    to the team's game array.
                    (3) If the team does not exist and the game does, add the team object with the current game in
                    the game array (including the current shot).
                    (4) If neither the team nor the game exist, create the team and game objects, including the game in
                    the team's game array.
                    """
                    # Get school names
                    school_1 = row[1]
                    if(row[1][-3:] == " ST"):
                        school_1 = row[1] + "ATE"
                    

                    school_2 = row[2]
                    if(row[2][-2:] == "ST"):
                        school_2 = row[2] + "ATE"
                    
                    # Check who is home and who is away
                    if(row[6] == "HOME"):
                        home_team = school_1
                        away_team = school_2
                    else:
                        home_team = school_2
                        away_team = school_1

                    # Check if shot was made

                    if(row[7] == "MADE"):
                        make_bool = True
                    else:
                        make_bool = False

                    # Update number of points and LAMA
                    if (make_bool):
                        points = 2
                        if("THREE" in row[8]):
                            points = 3
                            lama_bool = True
                        if(row[8] == "DUNK" or row[8] == "LAYUP" or row[8] == "TWO POINT TIP SHOT"):
                            lama_bool = True
                        else:
                            lama_bool = False
                    else:
                        points = 0

                    # Check if player was drafted
                    if(row[5] in drafted_list):
                        drafted_bool = True
                    else:
                        drafted_bool = False

                    # Check if game exists
                    game_obj = game.find_one({
                            "$and" : [
                                {"date": row[0]}, {"home": home_team}, {"away": away_team}]})
    
                    # Check if the team exists
                    team_obj = team.find_one({"school" : school_1})

                    # Set game bool
                    if(game_obj == None):
                        game_exists = False
                        game_id = None
                    else:
                        game_exists = True
                        game_id = game_obj.get("_id")
                    
                    # Set team bool
                    if (team_obj == None):
                        team_exists = False
                        team_id = None
                    else:
                        team_exists = True
                        team_id = team_obj.get("_id")

                    if (game_exists and not team_exists):
                        # add team, including the game with the current shot
                        if(school_1 == home_team):
                            home_array = [game_id]
                            away_array = []
                        else:
                            home_array = []
                            away_array = [game_id]

                        team_doc = {
                            "school" : school_1,
                            
                            "20" + str(i) + "-20" + str(i+1): {
                                "tournament" : False,
                                "home" : home_array,
                                "away": away_array
                            }     
                        }
                        team_id = team.insert_one(team_doc)
                        
                    elif (not game_exists and team_exists):
                        # add game with current shot and update team's game array
                        game_doc = {
                            "date" : row[0],
                            "home" : home_team,
                            "away" : away_team,
                            "home_shots" : [],
                            "away_shots" : []
                        }
                        game_id = game.insert_one(game_doc).inserted_id

                        season = "20"+ str(i) + "-20" + str(i+1)
                        if (school_1 == home_team):
                            home_or_away = "home"
                        else:
                            home_or_away = "away"

                        ## DOESN'T WORK ##    
                        szn =  season+"."+ home_or_away
                        query = {"_id": team_id}
                        newvals = { "$push" : { szn : game_id} }
                            
                        team.update_one(query, newvals)

                    elif (not game_exists and not team_exists):
                        # add team and add game, then add game to team

                        # add team
                        if(school_1 == home_team):
                            home_array = [game_id]
                            away_array = []
                        else:
                            home_array = []
                            away_array = [game_id]

                        team_doc = {
                            "school" : school_1,
                            
                            "20" + str(i) + "-20" + str(i+1): {
                                "tournament" : False,
                                "home" : home_array,
                                "away": away_array
                            }     
                        }
                        team_id = team.insert_one(team_doc)

                        # add game
                        game_doc = {
                            "date" : row[0],
                            "home" : home_team,
                            "away" : away_team,
                            "home_shots" : [],
                            "away_shots" : []
                        }
                        game_id = game.insert_one(game_doc).inserted_id
                    
                    ## UPDATE SHOT REGARDLESS ##
                    shot_doc = {
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
                    
                    query = {"_id" : game_id}

                    if (school_1 == home_team):
                        newvals = { "$push": { "home_shots": shot_doc } }
                    else:
                        newvals = { "$push": { "away_shots": shot_doc } }
                
                    game.update_one(query, newvals)

    
    filename = "tournament-teams.csv"
    with open(filename) as csv_file:
        csv_reader = csv.reader(csv_file, delimiter=',')

        for row in csv_reader:

            if any (row):
                
                myquery = { "school" : row[1] }
                newvals = {"$set": { str(row[0]) + ".tournament": True }}

                team.update_one(myquery, newvals)

 
def main():
    importData()


if __name__ == '__main__':
    main()
