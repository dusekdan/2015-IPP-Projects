#!/usr/bin/python

#XTD:xdusek21

import os, sys, getopt


# zadne parametry nejsou povolenou moznosti
if len(sys.argv) == 1:
	sys.exit(1)

try:
	options, remainder = getopt.getopt(sys.argv[1:], ':abg', ['help', 'input=', 'output=', 'header=', 'etc='])
except:
	#print("D: Wrong parameters!")
	sys.exit(1)

#print("D: Options : ", options)

## LIST FORMAT: 0Aset, 1Bset, 2Gset 3Hset, 4InputFile 5OutputFile, 6Header, 7Etc
optParsed = [False, False, False, False, False, False, False, False]
uniqCounter = [0, 0, 0, 0, 0, 0, 0, 0]

bPrintHelp, bNoMoreOutput = False, False

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

# je třeba upravit ještě test argumentů

if optParsed[3]:
	if (optParsed[0] != False or optParsed[1] != False or optParsed[2] != False or optParsed[4] != False or optParsed[5] != False or optParsed[6] != False or optParsed[7] != False):
		#print("D: Parametr Help se nesmi kombinovat s zadnym jinym parametrem!")
		sys.exit(1)
	else:
		bPrintHelp = True;


if (uniqCounter[0] > 1 or uniqCounter[1] > 1 or uniqCounter[2] >1 or uniqCounter[3] > 1 or uniqCounter[4] > 1 or uniqCounter[5] > 1 or uniqCounter[6] > 1 or uniqCounter[7] > 1):
	#print("D: Opakovane zadani kterehokoliv z prepinacu neni povoleno.")
	sys.exit(1)

# Je-li kontrola všech parametrů ukončena, můžeme poskytnout nějaký výstup
if bPrintHelp:
	print("Vyvojova verze testovaci napovedy programu XTD.PY, napsal Daniel Dusek, aka xdusek21, ten co se citi byt ukrivden skupinou D letosni neskutecne vydarene, nefer pulsemestralky. ")
	bNoMoreOutput = True

# Nyní otestujeme, jestli se po nás očekává nějaký další výstup - pakliže ano, pustíme se do těla celého programu
if bNoMoreOutput == False:
	#print("D: Starting a program")


	try:
		import xml.etree.ElementTree as ET 
		#print("D: xml.etree.ElementTree imported!")
	except:
		#print("D: Nepodařilo se naimportovat xml.etree.ElementTree jako ET proměnnou!")
		#print("E: 101")
		sys.exit(101)

	# Otevřu soubor
	try:
		fullFilePath = os.path.realpath(optParsed[4])
	except:
		#print("D: Unable to use os.path.relapath on bool.")
		#print("E: 102")
		sys.exit(102)

	try:
		xmlTree = ET.parse(fullFilePath)
		#print("D: Soubor ", fullFilePath, " úspěšně otevřen")
	except:
		#print("D: Soubor ", fullFilePath, " se nepodařilo otevřít")
		#print("E: 2")
		sys.exit(2)
	


	#print(tableNameList)

	#try:
	#open file and write output file
	#print(optParsed[5])
	f = open(optParsed[5], 'w')

	if optParsed[6] != False:
		f.write('-- ' + str(optParsed[6]) + '\n\n')


	# Vytvoření seznamu s názvem vytvořených tabulek

	xmlRoot = xmlTree.getroot();

	tableNameList = []
	tableList = []
	
	for elem in xmlRoot.iter():

		# přeskok root tagu
		if elem.tag == xmlRoot.tag:
			continue;

		# Pokud je zadán -a přepínač, odstraním všechny atributy
		#if optParsed[0] == True:


		#if elem.text != None:
			#print("tag:" + elem.tag + " má text")
		#else:
			#print("tag:" + elem.tag + " nemá text")

		#print(elem.attrib)


		tableNameList.append(elem.tag);

	tableNameList = list(set(tableNameList))

	for tableName in tableNameList:
		f.write("CREATE TABLE " + tableName + "(\nprk_"+tableName+"_id INT PRIMARY KEY,\n);\n\n")

		#print("CREATE TABLE " + tableName + "(\nprk_" + tableName + "_id INT PRIMARY KEY,\n);\n\n")


	#f.write('writen')
	f.close()
	#except:
	#	sys.exit(3)

else:
	sys.exit(0)




