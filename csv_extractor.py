import requests
import csv
import json
import sys
from nltk import word_tokenize
from functools import wraps

def per_row_extractor(func):
    """A method decorator for methods of csv_extractor that do per-row metadata
    extractions.

    The method must have the signature (self, key, row) and be called
    "something"_extractor. The method will be called with key="something" and a
    row of the CSV file. It should process the row in some way and store the
    result in self.rt_dict[key].
    """
    # bit of a hack to get class variables
    class_attrs = sys._getframe(1).f_locals
    suff = class_attrs.get('extractor_suffix')
    exs = class_attrs.get('per_row_extractors')

    # check method name
    name = func.__name__
    if not name.endswith(suff):
        raise NameError(name + ' does not end with "' + suff + '"')

    # update list of extractors
    exs.append(name)
    name = name[0:-len(suff)]

    # wrap it to automatically pass rt_dict key
    @wraps(func)
    def wrapper(self, row):
        func(self, name, row)

    return wrapper

def resource_extractor(func):
    """A method decorator for methods of csv_extractor that do metadata
    exraction from the CKAN resource.

    The method must have the signature (self, resource) and be called
    "something"_extractor. The method will be called with the CKAN resource of
    the data set. Its return value will be automatically stored in
    self.rt_dict["something"].
    """
    # bit of a hack to get class variables
    class_attrs = sys._getframe(1).f_locals
    suff = class_attrs.get('extractor_suffix')
    exs = class_attrs.get('resource_extractors')

    # check name
    name = func.__name__
    if not name.endswith(suff):
        raise NameError(name + ' does not end with "' + suff + '"')

    # update list of extractors
    exs.append(name)

    # no need to wrap, just return original
    return func

class csv_extractor(object):
    # keep a list of extractors to call on init
    per_row_extractors = []
    resource_extractors = []
    extractor_suffix = '_extractor'

    def __init__(self, resource, iter_reader):
        self.rt_dict = dict()

        # pass each row to each row extractor
        for row in iter_reader:
            for ex in self.__class__.per_row_extractors:
                getattr(self, ex)(row)

        # then pass the CKAN resource to each resource extractor
        for ex in self.__class__.resource_extractors:
            # TODO don't store if return value is None?
            self.rt_dict[ex[0:-len(self.__class__.extractor_suffix)]] = getattr(self, ex)(resource)

    def result(self):
        return self.rt_dict

    @per_row_extractor
    def row_count_extractor(self, key, row):
        """extract row count"""
        self.rt_dict[key] = self.rt_dict.get(key, 0) + 1

    @per_row_extractor
    def table_text_extractor(self, key, row):
        """extract set of unique tokens"""
        if key not in self.rt_dict:
            self.rt_dict[key] = set()

        for cell in row:
            tokens = word_tokenize(cell)
            for token in tokens:
                if token.isalpha():
                    self.rt_dict[key].add(token)

    @resource_extractor
    def NERs_extractor(self, resource):
        """extract named entities from the description

        this function require Corenlp in server mode: https://stanfordnlp.github.io/CoreNLP/corenlp-server.html
        """
        if not 'description' in resource:
            return None
        desc = resource['description']
        try:
            NERs = get_NERs(desc)
        except Exception as e:
            print("NER extraction failed: " + str(e), file=sys.stderr)
            return None
        ner_text = []
        for en_type in NERs:
            ner_text.extend(NERs[en_type])
        return ner_text

    @resource_extractor
    def url_extractor(self, resource):
        """extract the CSV URL"""
        return resource['url']

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
