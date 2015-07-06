#!/usr/bin/python

#XTD:xdusek21

import os, sys, getopt

######################################################################################################################################################
#    ___  _   ___    _   __  __   ___ ___  ___   ___ ___ ___ ___ ___ _  _  ___                                                                       #
#   | _ \/_\ | _ \  /_\ |  \/  | | _ \ _ \/ _ \ / __| __/ __/ __|_ _| \| |/ __|                                                                      #
#   |  _/ _ \|   / / _ \| |\/| | |  _/   / (_) | (__| _|\__ \__ \| || .` | (_ |                                                                      #
#   |_|/_/ \_\_|_\/_/ \_\_|  |_| |_| |_|_\\___/ \___|___|___/___/___|_|\_|\___|                                                                      #
#                                                                                                                                                    #
######################################################################################################################################################

# Getopt calling
try:
	options, remainder = getopt.getopt(sys.argv[1:], ':abg', ['help', 'input=', 'output=', 'header=', 'etc=']);
except:
	# EXIT_CODE 1 on incorrect param input
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

# Functions for data types determination


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

########################################################################################################################################################
#    _  _  ___    ___ _____ ___  __   _____ ___  ___ ___ ___  _  _                                                                                     #
#   | \| |/ _ \  | __|_   _/ __| \ \ / / __| _ \/ __|_ _/ _ \| \| |                                                                                    #
#   | .` | (_) | | _|  | || (__   \ V /| _||   /\__ \| | (_) | .` |                                                                                    #
#   |_|\_|\___/  |___| |_| \___|   \_/ |___|_|_\|___/___\___/|_|\_|                                                                                    #
#                                                                                                                                                      #
########################################################################################################################################################


# Used for counting occurances of foreign key columns
temporaryCounter = {};

# In every iteration the name of table is stored in this list. At the end of the iteration I use it to complement columns from value/foreignkeys
tableNames = [];

# Dictionary of lists. Values of tables stored in here.
tableValues = defaultdict(list);

# Dictionary of dictionaries, attributes of each table stored in here.
tableAttributes = {};


# Iterating from the root
for elem in xmlTree.iter():
	# Skipping root element
	if elem.tag == xmlTree.getroot().tag:
		continue;

	# Making it lower
	elem.tag = elem.tag.lower();

	# Storing text values
	if elem.text:
		# Only those that are not empty (whitespaces)
		if not elem.text.isspace():
			tableValues[elem.tag].append(makeValueNumber(elem.text));

	# If "-a" is not set, storing attribute values
	if elem.attrib and not optParsed[0]:
		# Element has attribute already
		if elem.tag in tableAttributes:
			for eat in elem.attrib:
				lceat = eat.lower();
				if lceat in tableAttributes[elem.tag]:
					# If element eat exists in tableAttributes, I check if his priority is still the highest
					if tableAttributes[elem.tag][lceat] < makeValueNumberAttr(elem.attrib[eat]):
						# If not, I update the value
						tableAttributes[elem.tag][lceat] = makeValueNumberAttr(elem.attrib[eat]);
				else:
					# Element doesn't exist, therefore I create it
					tableAttributes[elem.tag][lceat] = makeValueNumberAttr(elem.attrib[eat]);
		else:
			# Element doesn't have attributes assigned yet - I assign it actual
			#tableAttributes[elem.tag] = elem.attrib;
			taKeys = elem.attrib.keys();
			tatmpdict = {};
			for tk in taKeys:
				ltk = tk.lower();
				tatmpdict[ltk] = elem.attrib[tk];

			tableAttributes[elem.tag] = tatmpdict;
			
			for key in tableAttributes[elem.tag]:
				tableAttributes[elem.tag][key] = makeValueNumberAttr(tableAttributes[elem.tag][key]);

			#print(tableAttributes[elem.tag])


	# browsing the subelements
	for child in elem:
		child.tag = child.tag.lower();
		# počítač výskytů
		if child.tag in temporaryCounter:
			temporaryCounter[child.tag] += 1;
		else:
			temporaryCounter[child.tag] = 1;

	if optParsed[7] == False:
		# položky které se opakovaly nebo neopakovaly (resp. všichni bezprostřední potomci každé tabulky získané z atributů)
		tmpCntKeys = temporaryCounter.keys();


		for item in tmpCntKeys:
			i = 0;
			while i < temporaryCounter[item]:
				if temporaryCounter[item] == 1:
					outTagName = str(item)+"_id INT";
					if outTagName not in d[elem.tag]:
						d[elem.tag].append(outTagName);
					if str(item)+"1_id INT" in d[elem.tag]:
						try:
							d[elem.tag].remove(str(item)+"_id INT");
						except ValueError:
							i = i;
				else:
					if optParsed[1] == False:
						outTagName = str(item)+""+str(i+1)+"_id INT";
						if outTagName not in d[elem.tag]:
							d[elem.tag].append(outTagName);
					else:
						i+=1;
						continue;
				i+=1;

		temporaryCounter.clear();

		tableNames.append(elem.tag);
	else:

		# B set handler
		if optParsed[1] != False:
			sys.exit(1);

		listOfEtc = []
		tmpCntKeys = temporaryCounter.keys();

		for key in tmpCntKeys:
			occurences = temporaryCounter[key]
			if occurences > int(optParsed[7]):
				listOfEtc.append([key, elem.tag])
				if elem.tag+"_id INT" not in d[key]:
					d[key].append(elem.tag+"_id INT")
				maxOcc = occurences 
				while maxOcc:
					try:
						d[elem.tag].remove(key+str(maxOcc)+"_id INT")
					except ValueError:
						maxOcc = maxOcc;
					if maxOcc == 1:
						try:
							d[elem.tag].remove(key+"_id INT")
						except ValueError:
							maxOcc = maxOcc
					int(maxOcc)
					maxOcc -= 1
				# vetev, kde jde rodic do tabulky ditete
			else:
				if listOfEtc.count([key, elem.tag]) == 0:
					i = 0;
					while i < temporaryCounter[key]:
						if temporaryCounter[key] == 1:
							outTagName = str(key)+"_id INT";
							if outTagName not in d[elem.tag]:
								d[elem.tag].append(outTagName);
							if str(key)+"1_id INT" in d[elem.tag]:
								try:
									d[elem.tag].remove(str(key)+"_id INT")
								except ValueError:
									key = key
						else:
							outTagName = str(key)+""+str(i+1)+"_id INT";
							if outTagName not in d[elem.tag]:
								d[elem.tag].append(outTagName);
						i+=1;

	temporaryCounter.clear();

	tableNames.append(elem.tag);

for tItem in tableNames:
	dKeys = d.keys();
	if tItem not in dKeys:
		d[tItem] = None;

########################################################################################################################################################
#    ___ _____ ___  __   _____ ___  ___ ___ ___  _  _                                                                                                  #
#   | __|_   _/ __| \ \ / / __| _ \/ __|_ _/ _ \| \| |                                                                                                 #
#   | _|  | || (__   \ V /| _||   /\__ \| | (_) | .` |                                                                                                 #
#   |___| |_| \___|   \_/ |___|_|_\|___/___\___/|_|\_|                                                                                                 #
#                                                                                                                                                      #
########################################################################################################################################################




######################################################################################################################################################
#    ___ _____ ___ ___ _  _  ___     _   ___ ___ _   _ __  __ _   _ _      _ _____ ___ ___  _  _                                                     #
#   / __|_   _| _ \_ _| \| |/ __|   /_\ / __/ __| | | |  \/  | | | | |    /_\_   _|_ _/ _ \| \| |                                                    #
#   \__ \ | | |   /| || .` | (_ |  / _ \ (_| (__| |_| | |\/| | |_| | |__ / _ \| |  | | (_) | .` |                                                    #
#   |___/ |_| |_|_\___|_|\_|\___| /_/ \_\___\___|\___/|_|  |_|\___/|____/_/ \_\_| |___\___/|_|\_|                                                    #
#                                                                                                                                                    #
######################################################################################################################################################                                                                                                

#print("\ntemporaryCounter",temporaryCounter,"\nd.items",d.items(),"\ntableValues", tableValues, "\ntableAttributes", tableAttributes);


# String accumulator
outAcc = "";

# Adding a header if set
if optParsed[6] != False:
	outAcc += "--"+ optParsed[6] + "\n\n";

# Iteration over all of the tables
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
#print(outAcc);

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