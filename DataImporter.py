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

                    school_1 = row[1]
                    if(row[1][-3:] == " ST"):
                        school_1 = row[1] + "ATE"
                    
                    school_2 = row[2]
                    if(row[2][-2:] == "ST"):
                        school_2 = row[2] + "ATE"

                    if(row[6] == "HOME"):
                        home_team = school_1
                        away_team = school_2
                    else:
                        home_team = school_2
                        away_team = school_1

                    if(row[7] == "MADE"):
                        make_bool = True
                    else:
                        make_bool = False

                    points = 2
                    if("THREE" in row[8]):
                        points = 3
                        lama_bool = True
                    

                    if(row[8] == "DUNK" or row[8] == "LAYUP" or row[8] == "TWO POINT TIP SHOT"):
                        lama_bool = True
                    else:
                        lama_bool = False

                    if(row[5] in drafted_list):
                        drafted_bool = True
                    else:
                        drafted_bool = False

                    game_obj = game.find_one({
                            "$and" : [
                                {"date": row[0]}, {"home": home_team}, {"away": away_team}]})
    
                    if(game_obj == None):
        
                        game_doc = {
                            "date" : row[0],
                            "home" : home_team,
                            "away" : away_team,
                            "shots" : [ {
                                "xloc" :row[3],
                                "yloc" : row[4],
                                "player_name" : row[5],
                                "player_drafted": drafted_bool,
                                "made" : make_bool,
                                "type": row[8],
                                "assist" : row[9],
                                "points": points,
                                "LAMA": lama_bool
                             } ],
                            }

                        game_id = game.insert_one(game_doc).inserted_id

                    else:
                        game_id = game_obj.get("_id")
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
                        newvals = { "$push": { "shots": shot_doc } }
                        
                        game.update_one(query, newvals)

         
                    team_obj = team.find_one({"school" : school_1})

                    print school_1

                    if(team_obj == None):
              
                        if(school_1 == home_team):
                            home_array = [game_id]
                            away_array = []
                        else:
                            home_array = []
                            away_array = [game_id]

                        team_doc = {
                            "school" : school_1,
                            
                           str(i) + "-" + str(i+1): {
                            "tournament" : False,
                            "home" : home_array,
                            "away": away_array

                           }
                                   
                        }
                        team_id = team.insert_one(team_doc)
                    
                    else:

                        season = str(i) + "-" + str(i+1)


                        home_array = team_obj.get(season).get("home")
                        away_array = team_obj.get(season).get("away")

                        if(school_1 == home_team):
                            home_array.append(game_id)
                            home_or_away = "home"
                        else:
                           away_array.append(game_id)
                           home_or_away = "away"

                        new_season = {

                            "tournament" : team_obj.get(season).get("tournament"),
                            "home" : home_array,
                            "away": away_array

                        }
                        str =  season+".$."+home_or_away
                        query = {"_id": team_id}
                        newvals = { "$push" : { str : game_id} }
                        
                        team.update_one(query, newvals)

    
    filename = "tournament-teams.csv"
    with open(filename) as csv_file:
        csv_reader = csv.reader(csv_file, delimiter=',')

        for row in csv_reader:

            if any (row):
                
                season_obj = team.find_one({"school" : row[1]}).get(row[0])
                
                new_season = {
                                "tournament" : True,
                                "home" : season_obj.get("home"),
                                "away": season_obj.get("away"),
                            }

                myquery = { "school" : row[1] }
                newvals = {"$set": { row[0]: new_season }}

                team.update_one(myquery, newvals)


            
def main():

    importData()


if __name__ == '__main__':
    main()
