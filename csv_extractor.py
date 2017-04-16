import requests
import csv
import json
import sys
from nltk import word_tokenize
from functools import wraps

def per_row(func):
    """A decorator for methods of csv_extractor that do per-row metadata
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

def metadata(func):
    """A decorator for methods of csv_extractor that do metadata exraction from
    the CKAN metadata.

    The method must have the signature (self, dataset, resource) and be called
    "something"_extractor. The method will be called with the CKAN resource and
    the dataset that resource is part of. Its return value will be
    automatically stored in self.rt_dict["something"].
    """
    # bit of a hack to get class variables
    class_attrs = sys._getframe(1).f_locals
    suff = class_attrs.get('extractor_suffix')
    exs = class_attrs.get('metadata_extractors')

    # check name
    name = func.__name__
    if not name.endswith(suff):
        raise NameError(name + ' does not end with "' + suff + '"')

    # update list of extractors
    exs.append(name)

    # wrap to store return value
    @wraps(func)
    def wrapper(self, *args):
        val = func(self, *args)
        if val is not None:
            self.rt_dict[name[0:-len(suff)]] = val

    return wrapper

class csv_extractor(object):
    # keep a list of extractors to call on init
    per_row_extractors = []
    metadata_extractors = []
    extractor_suffix = '_extractor'

    def __init__(self, dataset, resource, iter_reader):
        self.rt_dict = dict()

        # pass each row to each row extractor
        for row in iter_reader:
            for ex in self.__class__.per_row_extractors:
                getattr(self, ex)(row)

        # then pass the CKAN data to each metadata extractor
        for ex in self.__class__.metadata_extractors:
            getattr(self, ex)(dataset, resource)

    def result(self):
        return self.rt_dict

    @per_row
    def row_count_extractor(self, key, row):
        """extract row count"""
        self.rt_dict[key] = self.rt_dict.get(key, 0) + 1

    @per_row
    def table_text_extractor(self, key, row):
        """extract set of unique tokens"""
        if key not in self.rt_dict:
            self.rt_dict[key] = set()

        for cell in row:
            tokens = word_tokenize(cell)
            for token in tokens:
                if token.isalpha():
                    self.rt_dict[key].add(token)

    @metadata
    def NERs_extractor(self, dataset, resource):
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

    @metadata
    def url_extractor(self, dataset, resource):
        """extract the CSV URL"""
        return resource['url']

    @metadata
    def tag_extractor(self, dataset, resource):
        try:
            return list(map(lambda t: t['display_name'], dataset['tags']))
        except KeyError:
            return None

    @metadata
    def title_extractor(self, dataset, resource):
        return dataset.get('title', None)

    @metadata
    def notes_extractor(self, dataset, resource):
        return dataset.get('notes', None)

    @metadata
    def name_extractor(self, dataset, resource):
        return resource.get('name', None)

    @metadata
    def date_extractor(self, dataset, resource):
        # lots of possible dates, and they can be None
        if resource.get('last_modified', None) is not None: return resource['last_modified']
        if resource.get('created', None) is not None: return resource['created']
        if dataset.get('metadata_modified', None) is not None: return dataset['metadata_modified']
        if dataset.get('metadata_created', None) is not None: return dataset['metadata_created']
        return None

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
