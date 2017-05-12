import requests
from contextlib import closing
import json
import os

data_dir = "data"
os.makedirs(data_dir, exist_ok=True)

def action(base, act, params={}):
    r = requests.get('/'.join((base, 'api/3/action', act)), params=params)
    return r.json()

def get_group(base, g):
    print(g)
    remaining = None
    per = 10
    page = 0

    while remaining is None or remaining > 0:
        resp = action(base, 'package_search', params={'rows': min(per, (remaining or per)), 'start': page * per})

        if remaining is None:
            remaining = resp['result']['count']
            print("total datasets: ", remaining)

        print("page:", page)

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

                    csv = os.path.join(fpath, "data.csv")
                    if not os.path.isfile(csv):
                        try:
                            with open(csv, "wb") as ofile:
                                    with closing(requests.get(resource['url'], stream=True, verify=False)) as request:
                                        if request.ok:
                                            for block in request.iter_content(1024):
                                                ofile.write(block)
                        except Exception as e:
                            print(resource['url'], e)
                            os.remove(csv)

#get_group('http://catalog.data.gov', 'education2168')
#get_group('http://catalog.data.gov', 'safety3175')
#get_group('https://www.opendataphilly.org', 'public-safety-group')
get_group('http://catalog.data.gov', 'agriculture8571')
#get_group('http://catalog.data.gov', 'businessusa4208')
#get_group('http://catalog.data.gov', 'states6394')
#get_group('http://catalog.data.gov', 'research9385')
