import requests
from bs4 import BeautifulSoup

'''
Get players drafted from basketball reference
'''

URL = "https://www.basketball-reference.com/draft/NBA_2014.html"

def main():
    r = requests.get(URL)
    soup = BeautifulSoup(r.content, "html.parser")

    table = soup.find("table")

    rows = table.find_all("tr")

    for row in rows[2:]:
        if "PER GAME" in row.text.encode("ascii","ignore").upper():
            pass
        elif "VORP" in row.text.encode("ascii","ignore").upper():
            pass
        else:
            name = row.find_all("td")[2].text.encode("ascii","ignore")
            print name.upper()


if __name__ == '__main__':
    main()

    print "\n*done*\n"