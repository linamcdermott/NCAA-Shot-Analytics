import requests
from bs4 import BeautifulSoup
import pandas as pd

SEASON = "2013-14"
URL = "https://en.wikipedia.org/wiki/2014_NCAA_Division_I_Men%27s_Basketball_Tournament#Automatic_qualifiers"

def main():
    r = requests.get(URL)
    soup = BeautifulSoup(r.content, "html.parser")

    table_holder = soup.find("div",{"id":"mw-content-text"})

    tables = table_holder.find_all("tbody")

    # counter = 0

    for table in tables[4:]:
        # print table.text.encode("ascii","ignore")

        rows = table.find_all("tr")

        for school in rows[1:]:
            # counter = counter + 1

            name = school.find_all("td")[1]
            print SEASON + "," + name.text.encode("ascii","ignore").replace("\n","").upper()
            # print counter


if __name__ == '__main__':
    main()

    print "\n*done*\n"