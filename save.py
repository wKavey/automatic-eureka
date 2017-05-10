import requests
from contextlib import closing
import csv
import json
import os

def action(base, act, params={}):
    return requests.get('/'.join((base, 'api/3/action', act)), params=params).json()

class SetEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, set):
            return list(obj)
        return json.JSONEncoder.default(self, obj)

data_dir = "data"
os.makedirs(data_dir, exist_ok=True)

def get_group(base, g):
    remaining = None
    per = 10
    page = 0

    while remaining is None or remaining > 0:
        resp = action(base, 'package_search', params={'q': 'groups:' + g, 'rows': min(per, (remaining or per)), 'start': page * per})

        if remaining is None:
            remaining = resp['result']['count']

        remaining -= per
        page += 1

        for result in resp['result']['results']:
            # remove resources from result so we can just look at the dataset info on its own
            resources = result['resources']
            del result['resources']

            parent = False

            path = os.path.join(data_dir,result['id'])

            for resource in resources:
                if resource['format'] == 'CSV' and resource['url'].endswith('.csv'):
                    if not parent:
                        parent = True
                        os.makedirs(path, exist_ok=True)
                        with open(os.path.join(path, "meta.json"), "w") as ofile:
                            json.dump(result, ofile)

                    fpath = os.path.join(path, resource['id'])
                    os.makedirs(fpath, exist_ok=True)
                    with open(os.path.join(fpath, "meta.json"), "w") as ofile:
                        json.dump(resource, ofile)

                    with open(os.path.join(fpath, "data.csv"), "wb") as ofile:
                        with closing(requests.get(resource['url'], stream=True)) as request:
                            if request.ok:
                                for block in request.iter_content(1024):
                                    ofile.write(block)

get_group('http://catalog.data.gov', 'education2168')
get_group('http://catalog.data.gov', 'safety3175')
get_group('https://www.opendataphilly.org', 'public-safety-group')
