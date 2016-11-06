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
		out['root'] = root
		out['filename'] = filename.rstrip('\n')
		l.append(out)

	res = json.dumps(l)
	print(res)

# with open("test.json","w") as fd:
# 	fd.write(res)
