import requests
import os.path
from urllib.parse import urlparse
from contextlib import closing
import csv
import json

# simple script for getting/parsing CSVs

base_url = 'http://catalog.data.gov/api/3'

def action(act, params={}):
    return requests.get('/'.join((base_url, 'action', act)), params=params).json()

# get 10 results at a time
limit = 10
pages = 1

index = 1

def process(reader):
    return {}

# get 10 pages of results
for page in range(pages):
    # empty query lists packages
    for result in action('package_search', params={'q': 'groups:education2168', 'rows': limit, 'start': page * limit})['result']['results']:
        # print each resource depending on what fields it has
        for resource in result['resources']:
            if resource['format'] == 'CSV' and resource['url'].endswith('.csv'):
                name = os.path.basename(urlparse(resource['url']).path)

                # parse the CSV
                meta = {'name': name, 'rows': 0, 'cols': 0}

                with closing(requests.get(resource['url'], stream=True)) as request:
                    reader = csv.reader(request.iter_lines(decode_unicode=True), delimiter=',', quotechar='"')

                    print(json.dumps({'index': {'_id': index}}))
                    print(json.dumps(process(reader)))
                    index += 1

                # or write it to a file
                #print(name)
                #f = open(name, 'wb')
                #f.write(requests.get(resource['url']).content)
                #f.close()
