import requests
import json
from elasticsearch import Elasticsearch
from elasticsearch import helpers

# Open index settings file
with open("index_config.json", 'r') as data_file:
    index_settings = data_file.read()

es = Elasticsearch()

# Recreate index using our settings
es.indices.delete(index='automatic-eureka', ignore=[400, 404])
es.indices.create(index='automatic-eureka', body=index_settings, ignore=[400, 404])

# Open mappings file
with open("dataset_mappings.json", 'r') as data_file:
    mappings = data_file.read()

es.indices.put_mapping(index='automatic-eureka', doc_type='dataset', body=mappings)

# Load data - contains dictionaries output from scrape
with open('data.json', 'r') as data_file:
    data = data_file.readlines()

# Construct list of json objects to insert
actions = []
index = 1
for item in data:
    action = {
                "_index": "automatic-eureka",
                "_type": "dataset",
                "_id": index,
                "_source": item
            }
    actions.append(action)
    index += 1

# Bulk insert documents
helpers.bulk(es, actions)

