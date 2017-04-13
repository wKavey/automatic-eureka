
API pattern: <REST Verb> /<Index>/<Type>/<ID>

# Basic (show, create, delete)

## List All Indices

    curl -XGET 'localhost:9200/_cat/indices?v&pretty'

## Create an Index  

creates the index named "customer" :

    curl -XPUT 'localhost:9200/customer?pretty&pretty'
    curl -XGET 'localhost:9200/_cat/indices?v&pretty'

## Index and Query a Document

index a document into the customer index, "external" type, with an ID of 1:

    curl -XPUT 'localhost:9200/customer/external/1?pretty&pretty' -H 'Content-Type: application/json' -d'
    {
      "name": "John Doe"
    }
    '


retrieve that document that we just indexed:

    curl -XGET 'localhost:9200/customer/external/1?pretty&pretty'


## Delete an Index 

    curl -XDELETE 'localhost:9200/customer?pretty&pretty'
    curl -XGET 'localhost:9200/_cat/indices?v&pretty'


# Modifying data 

## Indexing/Replacing Document

the same command with index command,  index another document with the same id 

## Updating Documents



curl -XPOST 'localhost:9200/customer/external/1/_update?pretty&pretty' -H 'Content-Type: application/json' -d'
{
  "doc": { "name": "Jane Doe" }
}
'


