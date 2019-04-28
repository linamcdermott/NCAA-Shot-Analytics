import pymongo
from pymongo import MongoClient
import csv

def importData():

    client = MongoClient()

    db = client.baskets

    player = db["player"]
    team = db["team"]
    game = db["game"]
    shot = db["shot"]
    roster = db["roster"]
    '''
    x = player.delete_many({})
    x = team.delete_many({})
    x = game.delete_many({})
    x = shot.delete_many({})
    x = roster.delete_many({})

    player.drop_indexes()
    team.drop_indexes()
    game.drop_indexes()
    shot.drop_indexes()
    roster.drop_indexes()


    player.create_index([('name', pymongo.ASCENDING)], unique=True)
    team.create_index([('school', pymongo.ASCENDING)], unique=True)
    game.create_index([('date', pymongo.ASCENDING), ("home", pymongo.ASCENDING ), ('away', pymongo.ASCENDING)], unique=True)
    #shot.create_index([('date', pymongo.ASCENDING)], unique=True)
    roster.create_index([('player_id', pymongo.ASCENDING), ("season", pymongo.ASCENDING )], unique=True)


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
                    # school_1 = row[1].replace(" ST", " STATE")
                  

                    school_2 = row[2]
                    if(row[2][-2:] == "ST"):
                        school_2 = row[2] + "ATE"
                    
                    
                    #add team first
                    team_doc_1 = {
                        "school" : school_1
                    }

                    team_doc_2 = {
                        "school" : school_2
                    }

                    try:
                        team_id = team.insert_one(team_doc_1).inserted_id
                    except:
                        team_id = team.find_one({"school" : school_1}).get('_id')
                    try:
                        opponent_id = team.insert_one(team_doc_2).inserted_id
                    except:
                        opponent_id = team.find_one({"school" : school_2}).get('_id')

                    #insert player
                    player_doc = {
                        "name" : row[5],
                        "drafted": False
                    }
                    try:
                        player_id = player.insert_one(player_doc).inserted_id
                    except:
                        player_id = player.find_one({"name" : row[5]}).get('_id')

                    #assisting player
                    if(row[9] != "n/a"):
                        assist_doc = {
                            "name" : row[9],
                            "drafted": False
                        }
                        try:
                            assist_id = player.insert_one(assist_doc).inserted_id
                        except:
                            assist_id = player.find_one({"name" : row[9]}).get('_id')

                        roster_doc_assist = {
                            "season" : "20" + str(i) + "-" + "20" + str(i+1),
                            "player_id": assist_id,
                            "team_id": team_id,
                            "tournament": False
                        }
                        try:
                            roster_id_assist = roster.insert_one(roster_doc_assist).inserted_id
                        except:
                            pass
                    else:
                        assist_id = None

                    #insert roster
                    roster_doc = {
                        "season" : "20" + str(i) + "-" + "20" + str(i+1),
                        "player_id": player_id,
                        "team_id": team_id,
                        "tournament": False
                    }
                    try:
                        roster_id = roster.insert_one(roster_doc).inserted_id
                    except:
                        pass


                    #insert game
                    if(row[6] == "HOME"):
                        home_team = team_id
                        away_team = opponent_id
                    else:
                        home_team = opponent_id
                        away_team = team_id

                    game_doc = {
                        "date" : row[0],
                        "home" : home_team,
                        "away" : away_team,

                    }
                    try:
                        game_id = game.insert_one(game_doc).inserted_id
                    except:
                        game_id = game.find_one({
                            "$and" : [
                                {"date": row[0]}, {"home": home_team}, {"away": away_team}
                            ]
                        }).get('_id')

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



                    #insert shots
                    shot_doc = {
                        "xloc" :row[3],
                        "yloc" : row[4],
                        "game_id": game_id,
                        "player_id" : player_id,
                        "made" : make_bool,
                        "type": row[8],
                        "assist" : assist_id,
                        "points": points,
                        "LAMA": lama_bool
                    }

                    try:
                        shot_id = shot.insert_one(shot_doc).inserted_id
                    except:
                        pass
    
    filename = "tournament-teams.csv"
    with open(filename) as csv_file:
        csv_reader = csv.reader(csv_file, delimiter=',')

        for row in csv_reader:

            if any (row):
                
                team_id = team.find_one({"school" : row[1]}).get('_id')

               
                myquery = { "$and": [ { "season": row[0] },{"team_id": team_id} ] }
                newvalues = {"$set": { "tournament": True }}

                roster.update_one(myquery, newvalues)
    '''
    filename = "drafted.txt"
    in_file = open(filename, 'r')
    for line in in_file:
        line = line.strip('\n')
        query = {"name": line}
        newvals = { "$set": { "drafted": True } }

        player.update_one(query, newvals)

    in_file.close()


            
def main():

    importData()


if __name__ == '__main__':
    main()
