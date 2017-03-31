import requests
# simple script for listing data sets

base_url = 'http://catalog.data.gov/api/3'

def action(act, params={}):
    return requests.get('/'.join((base_url, 'action', act)), params).json()

# get 10 results at a time
limit = 10

# get 10 pages of results
for page in range(10):
    # empty query lists packages
    for result in action('package_search', params={'rows': limit, 'start': page * limit})['result']['results']:
        print(result['name'])
        # print each resource depending on what fields it has
        for resource in result['resources']:
            if 'no_real_name' in resource:
                print("\t({})".format(resource['format']))
            elif not resource['format']:
                print("\t{}".format(resource['name']))
            else:
                print("\t({}) {}".format(resource['format'], resource['name']))
