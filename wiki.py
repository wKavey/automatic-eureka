import wikipedia
import multiprocessing
from multiprocessing import Manager


def download_wiki(nes,ne_wiki):
    for ne in nes:
        try:
            wikipage = wikipedia.page(ne)
            ne_wiki[ne] = wikipage.content
            print(ne)
        except Exception as e:
            print("error")
            pass

def multi_dl_wikis(nes,n_proc):
	'''
	download wikipedia pages using multiprocess
	nes: a list of name entities
	n_proc: number of processes
	'''
	manager = Manager()
	ne_wiki = manager.dict()
	jobs = []
	avg = int(len(nes)/n_proc)
	for i in range(n_proc):
	    p = multiprocessing.Process(target=download_wiki, args=(nes[i*avg:(i+1)*avg],ne_wiki))
	    jobs.append(p)
	    p.start()
	for proc in jobs:
	    proc.join()
	wikis = dict()
	for ne,content in ne_wiki.items():
		wikis[ne] = list(filter(lambda x:x.isalpha(),content.split()))
	return wikis


if __name__ == '__main__':
	nes = ['Obama','New York']
	ne_wiki = multi_dl_wikis(nes,1)

