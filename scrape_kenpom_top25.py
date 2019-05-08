import requests
from bs4 import BeautifulSoup
import pandas as pd

'''
Gets the top n ranked teams at the end of season according to kenpom
and prints them to the console in pre-CSV format

to run: change global N argument and hit go
'''

N = 25

def scrape():
    url_base = "https://kenpom.com/index.php?y="

    for current_year in range(2014, 2020):

        year_string = str(current_year-1) + "-" + str(current_year)

        url = url_base + str(current_year)

        # print url

        r = requests.get(url)
        soup = BeautifulSoup(r.content, "html.parser")

        ## FIND TABLE
        table = soup.find("div", {"id":"data-area"}).find("tbody")

        rows = table.find_all("tr")

        for i in range(N):
            school = rows[i].find("a").text.encode("ascii","ignore")
            print year_string + ", " + school


'''
'''
def main():
    scrape()


if __name__ == '__main__':
    main()

    print "\n*done*\n"