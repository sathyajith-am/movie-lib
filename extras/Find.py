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





extensions = ('.mp4','.mkv','.flv','.avi','.mpeg')

filewriter = open("moviespath.txt", "w");
filewriter.truncate();
filewriter.close();

for root, dirs, files in os.walk(directory_g):
	for filename in files:
		if filename.lower().endswith((extensions)):
		#print(filename)
			with open("upload.txt","a") as filewriter:
				filewriter.write("{};{}\n".format(root,filename))
		#print("{}/{}".format(root,filename))
