import requests
import os.path
from urllib.parse import urlparse
from contextlib import closing
import csv
import json


class csv_extractor(object):
	def __init__(self,iter_reader):
		self.reader = iter_reader
		self.rt_dict = dict()
		self.row_counter = 0
		self.rows = []
		for row in self.reader:
		    self.rows.append(json.dumps(row))
		    self.row_counter += 1

	
	def size_extractor(self):
		pass

	def entity_extractor(self):
		pass


