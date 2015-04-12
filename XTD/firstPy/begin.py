#!/usr/bin/python

import sys

try:
	import humansize
except:
	humansize = None

if humansize:
	print("Yes, humansize is present!")
else:
	print("No, humansize is not imported! Most likely doesnt exist.")


if __name__ == '__main__':
	print("Poustime primo program")
else:
	print("Nepoustime primo program")

print(__name__)


a_list = ['a', 'b', 'mpilgrim', 'z', 'example', 'z']
a_list.append('c')
a_list.append('z')

a_tuple = ('a', 'b', 'c')
a_list = list(a_tuple)
#print('a' in a_list)

#print(a_list.index('z'))

print(a_list)

#def approxSize(size, a_kilobyte_is_1024_bytes=True):
