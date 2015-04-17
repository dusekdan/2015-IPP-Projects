#!/usr/bin/python

#XTD:xdusek21

import os, sys, getopt

######################################################################################################################################################

# kontrola parametru

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

# Datové typy


def makeValueNumber(data):
	if data == 'True' or data == 'False' or data == '0' or data == '1':
		return 1;

	try:
		int(data);
		return 2;
	except:
		pass;

	try:
		float(data);
		return 3;
	except:
		pass;

	return 4;

def makeValueNumberAttr(data):
	if data == 'True' or data == 'False' or data == '0' or data == '1' or data == '':
		return 1;

	try:
		int(data);
		return 2;
	except:
		pass;

	try:
		float(data);
		return 3;
	except:
		pass;

	return 4;

def returnTypeNameText(data):
	if data == 1:
		return 	"BIT";
	elif data == 2:
		return "INT";
	elif data == 3:
		return "FLOAT";
	elif data == 4:
		return "NTEXT";

def returnTypeNameAttr(data):
	if data == 1:
		return 	"BIT";
	elif data == 2:
		return "INT";
	elif data == 3:
		return "FLOAT";
	elif data == 4:
		return "NVARCHAR";

####

temporaryCounter = {};
tableNames = [];
tableValues = defaultdict(list);
tableAttributes = {};
#tableAttributes = {} # já neviem uš. zidan.

for elem in xmlTree.iter():
	if elem.tag == xmlTree.getroot().tag:
		continue;

	if elem.text:
		if not elem.text.isspace():
			tableValues[elem.tag].append(makeValueNumber(elem.text));

	

	"""if elem.attrib:
		for eat in elem.attrib:
			if elem.tag in tableAttributes:
				if eat in tableAttributes[elem.tag]:
					if makeValueNumberAttr(tableAttributes[elem.tag][eat]) < makeValueNumberAttr(elem.attrib[eat]):
						tableAttributes[elem.tag][eat] = makeValueNumberAttr(elem.attrib[eat]);
			else:
				tableAttributes[elem.tag] = elem.attrib;
				#tableAttributes[elem.tag] = {key:makeValueNumber(value) for (key,value) in elem.attrib};
				for key in tableAttributes[elem.tag]:
					tableAttributes[elem.tag][key] = makeValueNumberAttr(elem.attrib[key]);
				print("now\n", tableAttributes);
	else:
		print(elem.tag," nema attributy");"""

	if elem.attrib and not optParsed[0]:
		# what happens if element has an attribute (or more)
		if elem.tag in tableAttributes:
			for eat in elem.attrib:
				if eat in tableAttributes[elem.tag]:
					# existuje-li prvek eat už v tableAttributes, zkontroluji, jestli jeho priorita náhodou není nižší než toho aktuálního
					if tableAttributes[elem.tag][eat] < makeValueNumberAttr(elem.attrib[eat]):
						# pokud opravdu ta priorita je nižší, použiju novou hodnotu
						tableAttributes[elem.tag][eat] = makeValueNumberAttr(elem.attrib[eat]);
				else:
					# pokud prvek neexistuje, opět natvrdo vložím
					tableAttributes[elem.tag][eat] = makeValueNumberAttr(elem.attrib[eat]);
		else:
			# když neexistuje záznam v tableAttributes pro daný tag, vytvoříme ho s těmi aktuálními (je to poprvé, takže nic neřeším(e))
			tableAttributes[elem.tag] = elem.attrib;
			for key in tableAttributes[elem.tag]:
				tableAttributes[elem.tag][key] = makeValueNumberAttr(tableAttributes[elem.tag][key]);


	# browsing the subelements
	for child in elem:
		
		# počítač výskytů
		if child.tag in temporaryCounter:
			temporaryCounter[child.tag] += 1;
		else:
			temporaryCounter[child.tag] = 1;

	# položky které se opakovaly nebo neopakovaly (resp. všichni bezprostřední potomci každé tabulky získané z atributů)
	tmpCntKeys = temporaryCounter.keys();


	for item in tmpCntKeys:
		i = 0;
		while i < temporaryCounter[item]:
			if temporaryCounter[item] == 1:

				outTagName = str(item)+"_id INT";
				if outTagName not in d[elem.tag]:
					d[elem.tag].append(outTagName);
			else:
				outTagName = str(item)+""+str(i+1)+"_id INT";
				if outTagName not in d[elem.tag]:
					d[elem.tag].append(outTagName);
			i+=1;

	temporaryCounter.clear();

	tableNames.append(elem.tag);

for tItem in tableNames:
	dKeys = d.keys();
	if tItem not in dKeys:
		d[tItem] = None;


# Accumulation of output string and its formating

# String accumulator

outAcc = "";

if optParsed[6] != False:
	outAcc += "--"+ optParsed[6] + "\n\n";


for tabName in d.items():
	# Table creation part, primary key attached here
	outAcc += "CREATE TABLE ";
	outAcc += tabName[0];	# table name
	outAcc += "(\n";
	outAcc += "   prk_" + tabName[0] + "_id INT PRIMARY KEY,\n"; 

	# Filling with standardly generated foreign keys in here
	if tabName[1] != None:
		for fkColumnName in tabName[1]:
			outAcc += "   " + fkColumnName + ",\n";

	# Filling with attribute defined columns

	attKeys = tableAttributes.keys();

	if tabName[0] in attKeys:
		for attrCol in tableAttributes[tabName[0]]:
			outAcc += "   " + attrCol + " " + returnTypeNameAttr(tableAttributes[tabName[0]][attrCol]) + ",\n";

	# Complementing with value columns

	valKeys = tableValues.keys();

	if tabName[0] in valKeys:
		outAcc += "   value " + returnTypeNameText(max(tableValues[tabName[0]])) + ",\n";


	# Stripping the last coma
	outAcc = outAcc[0:-2];	

	# Table enclosure
	outAcc += "\n);\n\n";

######################################################################################################################################################
#   __      _____ ___ _____ ___    ___  _  _   ___ _____ ___   ___  _   _ _____                                                                      #
#   \ \    / / _ \_ _|_   _| __|  / _ \| \| | / __|_   _|   \ / _ \| | | |_   _|                                                                     #
#    \ \/\/ /|   /| |  | | | _|  | (_) | .` | \__ \ | | | |) | (_) | |_| | | |                                                                       #
#     \_/\_/ |_|_\___| |_| |___|  \___/|_|\_| |___/ |_| |___/ \___/ \___/  |_|                                                                       #
#                                                                                                                                                    #
######################################################################################################################################################

# Output is set to sys.stdout
if optParsed[5] == sys.stdout:
	try:
		# Standard writting to sys.stdout()
		optParsed[5].write(outAcc);
	except:
		try:
			# Pretty much redudant. Could save someone's bottom.
			sys.stdout.write(outAcc);
		except:
			# Returning EXIT_CODE 3 on failure
			sys.exit(3);
else:
	# Ouput is not set to sys.stdout, meaning we are trying to write to a real file
	try:
		# File descriptor set
		f = open(optParsed[5], 'w');
		# Write complished
		f.write(outAcc);
	except:
		# Returning EXIT_CODE 3 on failure
		sys.exit(3);
	finally:
		# One way or another, file descriptor should be closed
		f.close();


#print(outAcc);
"""print(tableAttributes);
print(tableValues);
"""