#!/usr/bin/python

#XTD:xdusek21

import sys
#import argparse
import getopt
#from xml.dom.minidom import parse

# all of the parameters are contained in sys.argv array

arguments = ""
for arg in sys.argv[1:]:
	arguments.join(arg+" ")

#arguments = "".join(sys.argv[1:]);
print(arguments)