import requests
import os.path
from urllib.parse import urlparse

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
		pass

	def entity_extractor(self):
		pass

	def metadata_extractor(self):
		filename = os.path.basename(urlparse(resource['url']).path)
