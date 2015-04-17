#!/usr/bin/python

#XTD:xdusek21

import os, sys, getopt

######################################################################################################################################################

# kontrola parametru

if len(sys.argv) == 1:
	sys.exit(1);

try:
	options, remainder = getopt.getopt(sys.argv[1:], ':abg', ['help', 'input=', 'output=', 'header=', 'etc=']);
except:
	# Spatne parametry
	sys.exit(1)

## LIST FORMAT: 0Aset, 1Bset, 2Gset 3Hset, 4InputFile 5OutputFile, 6Header, 7Etc
optParsed = [False, False, False, False, False, False, False, False]
uniqCounter = [0, 0, 0, 0, 0, 0, 0, 0]

bPrintHelp = False;

for opt, arg in options:
	if opt == '-a':
		optParsed[0] = True;
		uniqCounter[0] += 1

	if opt == '-b':
		optParsed[1] = True
		uniqCounter[1] += 1

	if opt == '-g':
		optParsed[2] = True
		uniqCounter[2] += 1

	if opt == '--help':
		optParsed[3] = True
		uniqCounter[3] += 1

	if opt == '--input':
		optParsed[4] = arg
		uniqCounter[4] += 1

	if opt == '--output':
		optParsed[5] = arg
		uniqCounter[5] += 1

	if opt == '--header':
		optParsed[6] = arg
		uniqCounter[6] += 1

	if opt == '--etc':
		optParsed[7] = arg
		uniqCounter[7] += 1



# HELP TEST
if optParsed[3]:
	if (optParsed[0] != False or optParsed[1] != False or optParsed[2] != False or optParsed[4] != False or optParsed[5] != False or optParsed[6] != False or optParsed[7] != False):
		#print("D: Parametr Help se nesmi kombinovat s zadnym jinym parametrem!")
		sys.exit(1)
	else:
		bPrintHelp = True;

# OPAKOVANE ZADANI PREPINACE
if (uniqCounter[0] > 1 or uniqCounter[1] > 1 or uniqCounter[2] >1 or uniqCounter[3] > 1 or uniqCounter[4] > 1 or uniqCounter[5] > 1 or uniqCounter[6] > 1 or uniqCounter[7] > 1):
	#print("D: Opakovane zadani kterehokoliv z prepinacu neni povoleno.")
	sys.exit(1)

# Je-li kontrola všech parametrů ukončena, můžeme poskytnout nějaký výstup
if bPrintHelp:
	print("Vyvojova verze testovaci napovedy programu XTD.PY, napsal Daniel Dusek, aka xdusek21, ten co se citi byt ukrivden skupinou D letosni neskutecne vydarene, nefer pulsemestralky. ")
	sys.exit(0);

# Ošetření nezadaných --input a --output

if(optParsed[4] == False):
	optParsed[4] = sys.stdin;

if(optParsed[5] == False):
	optParsed[5] = sys.stdout;


######################################################################################################################################################

# IMPORTy a příprava na proparsování se skrz

# IMPORT - etree - chyba 101 když neúspěch
try:
	import xml.etree.ElementTree as ET 
	#print("D: xml.etree.ElementTree imported!")
except:
	#print("D: Nepodařilo se naimportovat xml.etree.ElementTree jako ET proměnnou!")
	#print("E: 101")
	sys.exit(101);

# IMPORT - collections - chyba 101 když neúspěch
try:
	from collections import defaultdict;
	d = defaultdict(list);
except:
	#print("D: Nepodařilo se naimportovat defaultdict z collections.");
	sys.exit(101);

if optParsed[4] != sys.stdin:
	fullFilePath = os.path.realpath(optParsed[4])
else:
	fullFilePath = sys.stdin;

# Otevření a načtení souboru - neb je validita zaručena, jakýkoliv chybný pokus vyhodnocuji jako chybu otevření souboru
try:
	xmlTree = ET.parse(fullFilePath)
	#print("D: Soubor ", fullFilePath, " úspěšně otevřen")
except:
	#print("D: Soubor ", fullFilePath, " se nepodařilo otevřít")
	#print("E: 2")
	sys.exit(2)

######################################################################################################################################################