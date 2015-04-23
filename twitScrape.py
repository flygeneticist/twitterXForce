#!/usr/bin/env python

"""

Use Twitter API to grab user information from list of organizations;
export text file

Uses Twython module to access Twitter API

"""

import sys
import string
import simplejson
from twython import Twython

#WE WILL USE THE VARIABLES DAY, MONTH, AND YEAR FOR OUR OUTPUT FILE NAME
import datetime
now = datetime.datetime.now()
day=int(now.day)
month=int(now.month)
year=int(now.year)


#FOR OAUTH AUTHENTICATION -- NEEDED TO ACCESS THE TWITTER API
t = Twython(
    app_key='GD72FRhnYJH5XpniR3mQ24iAj', #REPLACE 'APP_KEY' WITH YOUR APP KEY, ETC., IN THE NEXT 4 LINES
    app_secret='G0FphevqXWbN3kMC9afhIAesIDtwMXt7nRDdnvXZyDN8qLhvVq''APP_SECRET',
    oauth_token='613432796-2vVuIBIg9tzmxy4TezQJzP899siM7uFkrwPi6S1K',
    oauth_token_secret='kaMhYFXNEHZ46GNhQdwmrXfDSiCqLNG9S3Cn1Kf5rrATy')

#REPLACE WITH YOUR LIST OF TWITTER USER IDS
ids = "xero, xeroapi, salesforce, partnerforce,"

#ACCESS THE LOOKUP_USER METHOD OF THE TWITTER API -- GRAB INFO ON UP TO 100 IDS WITH EACH API CALL
#THE VARIABLE USERS IS A JSON FILE WITH DATA ON THE 32 TWITTER USERS LISTED ABOVE
users = t.lookup_user(screen_name = ids)

#NAME OUR OUTPUT FILE - %i WILL BE REPLACED BY CURRENT MONTH, DAY, AND YEAR
outfn = "twitter_user_data_%i.%i.%i.txt" % (now.month, now.day, now.year)

#NAMES FOR HEADER ROW IN OUTPUT FILE
fields = "id screen_name name created_at url followers_count friends_count statuses_count \
    favourites_count listed_count \
    contributors_enabled description protected location lang expanded_url".split()

#INITIALIZE OUTPUT FILE AND WRITE HEADER ROW
outfp = open(outfn, "w")
outfp.write(string.join(fields, "\t") + "\n")  # header

#THE VARIABLE 'USERS' CONTAINS INFORMATION OF THE 32 TWITTER USER IDS LISTED ABOVE
#THIS BLOCK WILL LOOP OVER EACH OF THESE IDS, CREATE VARIABLES, AND OUTPUT TO FILE
for entry in users:
    #CREATE EMPTY DICTIONARY
    r = {}
    for f in fields:
        r[f] = ""
    #ASSIGN VALUE OF 'ID' FIELD IN JSON TO 'ID' FIELD IN OUR DICTIONARY
    r['id'] = entry['id']
    r['screen_name'] = entry['screen_name']
    r['followers_count'] = entry['followers_count']
    r['friends_count'] = entry['friends_count']

    #CREATE EMPTY LIST
    lst = []
    #ADD DATA FOR EACH VARIABLE
    for f in fields:
        lst.append(unicode(r[f]).replace("\/", "/"))
    #WRITE ROW WITH DATA IN LIST
    outfp.write(string.join(lst, "\t").encode("utf-8") + "\n")

outfp.close()
