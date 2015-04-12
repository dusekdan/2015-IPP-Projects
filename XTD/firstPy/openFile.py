#!/usr/bin/python

import sys, os, glob


pathname = os.path.join(os.getcwd(), 'openFile.py')

print(os.path.split(pathname))


(dirname, filename) = os.path.split(pathname)
print(dirname)
print(filename)


(actualname, extension) = os.path.splitext(filename)
print(actualname)
print(extension)


# get path to all the xml files in xmltest folder
os.chdir("xmltest/")
file_list = glob.glob('*.xml')

print(file_list)

#strippedFileList = [ os.path.splitext(x) for x in file_list]
#print(strippedFileList)
#strippedFileList = [ x[0] for x in strippedFileList ]
#print(strippedFileList)
if '1.xml' in file_list:
	print("1.xml is in the folder")
else:
	print("1.xml is not in the folder")

print(os.getcwd())

for x in file_list:
	print(os.path.realpath(x))



os.chdir("../")
print(os.getcwd())