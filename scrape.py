import requests
from contextlib import closing
import csv
import json
from csv_extractor import csv_extractor

'''
1. how to get the title ?
'''
# simple script for getting/parsing CSVs

base_url = 'http://catalog.data.gov/api/3'

def action(act, params={}):
    return requests.get('/'.join((base_url, 'action', act)), params=params).json()

# get 10 results at a time
limit = 10
pages = 1

index = 1

# get results
for page in range(pages):
    for result in action('package_search', params={'q': 'groups:education2168', 'rows': limit, 'start': page * limit})['result']['results']:
        for resource in result['resources']:
            if resource['format'] == 'CSV' and resource['url'].endswith('.csv'):
                with closing(requests.get(resource['url'], stream=True)) as request:
                    # TODO might get BOM in the first column header
                    reader = csv.reader(request.iter_lines(decode_unicode=True), delimiter=',', quotechar='"')
                    ex = csv_extractor(resource, reader)
                    
                    print(json.dumps({'index': {'_id': index}}))
                    print(json.dumps(ex.extract_pipeline()))
                    index += 1
                # or write it to a file
                #print(name)
                #f = open(name, 'wb')
                #f.write(requests.get(resource['url']).content)
                #f.close()
