#!/usr/bin/python

#XTD:xdusek21

import os, sys, getopt

try:
	options, remainder = getopt.getopt(sys.argv[1:], ':abg', ['help', 'input=', 'output=', 'header=', 'etc='])
except:
	print("Wrong parameters!")
	sys.exit(2)

print("Options : ", options)

aSet, bSet, gSet, inputFile, outputFile, header, etcSet, hSet = False, False, False, False, False, False, False, False

for opt, arg in options:
	
	if opt == '-a':
		aSet = True

	if opt == '-b':
		bSet = True
	
	if opt == '-g':
		gSet = True

	if opt == '--help':
		hSet = True

	if opt == '--input':
		inputFile = arg

	if opt == '--output':
		outputFile = arg

	if opt == '--header':
		header = arg

	if opt == '--etc':
		etcSet = arg



if hSet and (aSet != False or bSet != False or gSet != False or inputFile != False or outputFile != False or header != False or etcSet != False):
	print("--help parametr se nesmi kombinovat s zadnymi jinymi")
	sys.exit(2);
else:
	print("napoveda: zde!")

	

print("A : ", aSet, "\nB :", bSet, "\nG :", gSet, "\nInput :", inputFile, "\nOutput :", outputFile, "\nHeader :", header, "\nEtcSet :", etcSet)

