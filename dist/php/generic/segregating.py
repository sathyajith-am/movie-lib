import os

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



def group_by_genre():
	if not os.path.exists(directory_g + '/Genre' ):
		os.makedirs(directory_g + '/Genre')

	lines = [line.rstrip('\n') for line in open('movies.txt', 'r')]
	for line in lines:
		root, filename, genre_list, year = line.split(";")
		#Making symlinks to all the movies w.r.t. the genre
		genre_list = genre_list.split(",")
		for genre in genre_list:
			if not os.path.exists(directory_g + '/Genre/' + genre):
				os.makedirs(directory_g + '/Genre/' + genre)

			src = root + '/' + filename
			dest = directory_g + '/Genre/' + genre + '/' + filename
			os.symlink(src, dest)

def group_by_year():
	if not os.path.exists(directory_g + '/Year' ):
		os.makedirs(directory_g + '/Year')
		lines = [line.rstrip('\n') for line in open('movies.txt', 'r')]
		for line in lines:
			root, filename, genre_list, year = line.split(";")
			#Making symlinks to all the movies w.r.t. the year
			if not os.path.exists(directory_g + '/Year/' + year):
				os.makedirs(directory_g + '/Year/' + year)
			src = root + '/' + filename
			dest = directory_g + '/Year/' + year + '/' + filename
			os.symlink(src, dest)

group_by_year()
group_by_genre()
