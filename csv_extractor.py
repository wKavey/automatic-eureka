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

	def entity_extractor(self):
		pass

	def metadata_extractor(self):
		self.rt_dict['url'] = self.resource['url']

	def extract_pipeline(self):
		self.size_extractor()
		self.entity_extractor()
		self.metadata_extractor()
		return self.rt_dict
