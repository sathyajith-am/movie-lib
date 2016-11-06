import os
import json
directory_g = os.getcwd()

def get_dir():
	global directory_g
	directory_g = raw_input("Specify the Directory: ")
	if not os.path.isdir(directory_g):
		print("Enter Valid Directory!")
		get_dir()


try:
	import tkFileDialog
except ImportError:
	get_dir()
else:
	directory_g = tkFileDialog.askdirectory()



def group_by_list(foldername, mapkey):
	if not os.path.exists(directory_g + '/' + foldername ):
		os.makedirs(directory_g + '/' + foldername)

	for movie in data:
		for listitem in movie[mapkey]:
			if not os.path.exists(directory_g + '/' + foldername + '/' + listitem["name"]):
				os.makedirs(directory_g + '/' + foldername + '/' + listitem["name"])

				src = movie["root"] + '/' + movie["filename"]
				dest = directory_g + '/' + foldername + '/' + listitem["name"] + '/' + movie["filename"]
				os.symlink(src, dest)

def group_by_item(foldername, mapkey):
	if not os.path.exists(directory_g + '/' + foldername ):
		os.makedirs(directory_g + '/' + foldername)

	for movie in data:
		if not os.path.exists(directory_g + '/' + foldername + '/' + movie[mapkey]):
			os.makedirs(directory_g + '/' + foldername + '/' + movie[mapkey])

			src = movie["root"] + '/' + movie["filename"]
			print(src)
			dest = directory_g + '/' + foldername + '/' + movie[mapkey] + '/' + movie["filename"]
			print(dest)
			os.symlink(src, dest)

#group_by_year()

with open('output.json') as json_file:
	data = json.load(json_file)

group_by_list('Genre', 'genres')
group_by_item('Type', 'type')
#group_by_lists()
