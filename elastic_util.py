'''
elasticsearch-py:  https://www.elastic.co/guide/en/elasticsearch/client/python-api/current/index.html
Elasticsearch DSL: http://elasticsearch-dsl.readthedocs.io/en/latest/

'''

from datetime import datetime
from elasticsearch import Elasticsearch

# by default we connect to localhost:9200
es = Elasticsearch()
es.index(index="my-index", doc_type="test-type", id=42, body={"any": "data", "timestamp": datetime.now()})
es.get(index="my-index", doc_type="test-type", id=42)['_source']

