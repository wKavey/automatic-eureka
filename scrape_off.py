import csv
import json
import os
import codecs
from csv_extractor import csv_extractor

# simple script for getting/parsing OFFLINE CSVs

class SetEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, set):
            return list(obj)
        return json.JSONEncoder.default(self, obj)

data_dir = 'data'

index = 1

for result_id in os.listdir(data_dir):
    result = None
    with open(os.path.join(data_dir, result_id, 'meta.json')) as ifile:
        result = json.load(ifile)

    resource_ids = os.listdir(os.path.join(data_dir, result_id))
    resource_ids.remove('meta.json')

    for resource_id in resource_ids:
        resource = None
        with open(os.path.join(data_dir, result_id, resource_id, 'meta.json')) as ifile:
            resource = json.load(ifile)

        with codecs.open(os.path.join(data_dir, result_id, resource_id, 'data.csv'), encoding="utf-8", errors="replace") as ifile:
            reader = csv.reader(ifile, delimiter=',', quotechar='"')

            ex = csv_extractor(result, resource, reader)

            print(json.dumps({'index': {'_id': index}}))
            print(json.dumps(ex.result(), cls=SetEncoder))
            index += 1
