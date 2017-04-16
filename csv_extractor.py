import requests
import csv
import json
from nltk import word_tokenize

class csv_extractor(object):
    def __init__(self,resource,iter_reader):
        self.resource = resource
        self.reader = iter_reader
        self.rt_dict = dict()
        self.row_counter = 0
        self.rows = []
        for row in self.reader:
            self.rows.append(row)
            self.row_counter += 1

    def size_extractor(self):
        self.rt_dict['row_counts'] = self.row_counter

    def table_text_extractor(self):
        text = []
        for row in self.rows:
            for cell in row:
                tokens = word_tokenize(cell)
                for token in tokens:
                    if token.isalpha():
                        text.append(token)
        self.rt_dict['text_tokens'] = list(set(text)) 



    def entity_extractor(self):
        '''
        extract named entities from resource['description']
        this function require Corenlp in server mode: https://stanfordnlp.github.io/CoreNLP/corenlp-server.html
        '''
        if not 'description' in self.resource:
            return
        desc = self.resource['description']
        NERs = get_NERs(desc)
        ner_text = []
        for en_type in NERs:
            ner_text.extend(NERs[en_type])
        self.rt_dict['ners'] = ner_text


    def metadata_extractor(self):
        self.rt_dict['url'] = self.resource['url']

    def extract_pipeline(self):
        self.size_extractor()
        self.entity_extractor()
        self.metadata_extractor()
        self.table_text_extractor()
        return self.rt_dict


def get_NERs(data = 'I love New York and California.'):
    '''
    return by {type: name entities}
    '''
    core_ners = [ u'PERSON', u'LOCATION', u'ORGANIZATION']
    url ='http://localhost:9000/?properties={%22annotators%22%3A%22tokenize%2Cssplit%2Cpos%2Cner%2Centitymentions%22%2C%22outputFormat%22%3A%22json%22}'
    headers = {'Content-type': 'application/json'}
    r = requests.post(url, data=data, headers=headers)
    rs_json = r.json(strict=False)['sentences'][0]['tokens']
    loc_count = 0
    idx = 0
    NERs = dict()
    while idx < len(rs_json):
        token = rs_json[idx]
        NE = ""
        if token['ner'] != 'O':
            NER_type = token['ner']
            if NER_type in core_ners:
                if not NER_type in NERs :
                    NERs[NER_type] = []
                NE = NE + token['word'] + ' '
                loc_count += 1
                #print token['word']
                idx += 1
                if idx < len(rs_json):
                    token = rs_json[idx]
                else:
                    NERs[NER_type].append(NE.strip())
                    break
                while idx < len(rs_json) and token['ner'] == NER_type:
                    #print token['word']
                    NE = NE + token['word'] + ' '
                    idx += 1
                    if idx < len(rs_json):
                        token = rs_json[idx]
                    else:
                        break
                #print NE
                if NER_type in core_ners:
                    NERs[NER_type].append(NE.strip())
        idx += 1
    return NERs
