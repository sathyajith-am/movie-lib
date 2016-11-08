#!/usr/bin/env python
from guessit import guessit
import json
import sys

# print(sys.argv[1])

with open("upload.txt","r") as fd:
	l = []
	for lines in fd:
		root, filename =lines.split(";")
		out = guessit(filename)
		if "language" in out:
			del out['language']

		# remove those elements that are not strings
		for key in out.keys():
			if not isinstance(out[key], str):
				del out[key]

		out['root'] = root
		out['filename'] = filename.rstrip('\n')
		#print(out['filename'])
		l.append(out)
		

	#print(l)

	
	res = json.dumps(l)
	print(res)

# with open("test.json","w") as fd:
# 	fd.write(res)
