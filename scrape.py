import requests
from contextlib import closing
import csv
import json
from csv_extractor import csv_extractor

# simple script for getting/parsing CSVs

base_url = 'http://catalog.data.gov/api/3'

def action(act, params={}):
    return requests.get('/'.join((base_url, 'action', act)), params=params).json()

class SetEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, set):
            return list(obj)
        return json.JSONEncoder.default(self, obj)

limit = 2
pages = 1

index = 1

# get results
for page in range(pages):
    for result in action('package_search', params={'q': 'groups:education2168', 'rows': limit, 'start': page * limit})['result']['results']:

        # remove resources from result so we can just look at the dataset info on its own
        resources = result['resources']
        del result['resources']

        for resource in resources:
            if resource['format'] == 'CSV' and resource['url'].endswith('.csv'):
                with closing(requests.get(resource['url'], stream=True)) as request:
                    # TODO might get BOM in the first column header
                    reader = csv.reader(request.iter_lines(decode_unicode=True), delimiter=',', quotechar='"')
                    ex = csv_extractor(result, resource, reader)

                    print(json.dumps(ex.result(), cls=SetEncoder))
                    index += 1
