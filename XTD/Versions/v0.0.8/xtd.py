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

if optParsed[7] != False and optParsed[1] != False:
	sys.exit(1);

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
	# lower is used to get over TruE, FaLsE and stuff
	if data.lower() == 'true' or data.lower() == 'false' or data == '0' or data == '1': # or data.isspace()
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
	if data.lower() == 'true' or data.lower() == 'false' or data == '0' or data == '1' or data == ''  or data.isspace():
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

# ahojky
listOfEtc = []

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



				# Handling tag in for "xxx_id" -> maybe this is not the best way, depending what the guys on phorum will tell; it works in very least
				if lceat[-3:] == "_id":
					sys.exit(90);

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

	if optParsed[7] == False and optParsed[1] == False:
		
		# položky které se opakovaly nebo neopakovaly (resp. všichni bezprostřední potomci každé tabulky získané z atributů)
		tmpCntKeys = temporaryCounter.keys();


		# Looping through tmpCntKeys
		for item in tmpCntKeys:
			i = 0;
			# Preparing pseudo-while loops
			while i < temporaryCounter[item]:
				# If there's only one occurrence of the key
				if temporaryCounter[item] == 1:
					# Only one column is generated
					outTagName = str(item)+"_id INT";
					# If there's no previous occurrence in for this element, we add it
					if outTagName not in d[elem.tag]:	
						d[elem.tag].append(outTagName);
					# If there's more occurrences than one, we delete the column without number
					if str(item)+"1_id INT" in d[elem.tag]:
						try:
							# Deleting the column
							d[elem.tag].remove(str(item)+"_id INT");
						except ValueError:
							# Pretending we're doing something
							i = i;
				# More than one occurence of the current item
				else:
					# Test if -b switch is set, if not, we make numbered columns
					outTagName = str(item)+""+str(i+1)+"_id INT";
					# And we add them to the dList of actual element (if they are not already present)
					if outTagName not in d[elem.tag]:
						d[elem.tag].append(outTagName);
					# If the -b switch is on, variable i is increased and we jump to the beginning of the loop
				i+=1;

		temporaryCounter.clear();

		tableNames.append(elem.tag);

	# B set param handler
	elif optParsed[7] == False and optParsed[1] == True:

		tmpCntKeys = temporaryCounter.keys();
		# Looping, but inserting only one of those for each
		for item in tmpCntKeys:
			outTagName = str(item)+"_id INT";
			if outTagName not in d[elem.tag]:
				d[elem.tag].append(outTagName);

		# Appending table name like every time
		tableNames.append(elem.tag);

	# Etc set param handler
	else:
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


if optParsed[2] == False:

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



		attKeys = tableAttributes.keys();
		valKeys = tableValues.keys();

		tValBool = False;
		tAttrBool = False;

		"""print(tabName);
		print(attKeys, valKeys);
		print(tableAttributes, tableValues);"""

		###################################################################################################
		# Otestovat existenci
		if tabName[0] in tableAttributes and tableAttributes[tabName[0]]:
			#print("Existuje zaznam pro polozku", tabName[0], "v tableAttributes a neni prazdny");
			if 'value' in tableAttributes[tabName[0]]:
				#print("\tExistuje i polozka value");
				if tabName[0] in tableValues:
					#print("a dokonce existuje i zaznam pro", tabName[0], "v tableValue");
					if tableValues[tabName[0]]:
						#We do a comparation and take the greater one
						#print("existuje i seznam uvnitr...");
						if max(tableValues[tabName[0]]) < tableAttributes[tabName[0]]['value']:
							# We take table Attribute
							tAttrBool = True;
						else:
							tValBool = True;
					else:
						# We take tableAttribute values
						tAttrBool = True;
						print("neexistuje i seznam uvnitr...");
				else:
					tAttrBool = True;
			else:
				tValBool = True;
		else:
			tValBool = True;
		##################################################################################################

		# Filling with standardly generated foreign keys in here
		if tabName[1] != None:
			for fkColumnName in tabName[1]:
				outAcc += "   " + fkColumnName + ",\n";

		# Filling with attribute defined columns
		if tabName[0] in attKeys:
			for attrCol in tableAttributes[tabName[0]]:
				if attrCol == 'value' and tValBool:
					continue;
				outAcc += "   " + attrCol + " " + returnTypeNameAttr(tableAttributes[tabName[0]][attrCol]) + ",\n";

		# Complementing with value columns
		if tabName[0] in valKeys:
			if tValBool:
				outAcc += "   value " + returnTypeNameText(max(tableValues[tabName[0]])) + ",\n";


		# Stripping the last coma
		outAcc = outAcc[0:-2];	

		# Table enclosure
		outAcc += "\n);\n\n";
else:
	print("-g param set!\n");

	"""relationsDict = {};


	# I need relationsDict[table1][table1] = relation;

	print(d.keys());

	for tmpA in d.items():
		relationsDict[tmpA[0]] = {}
		for tmpB in d.items():
			if tmpB[0] == tmpA[0]:
				relationsDict[tmpA[0]][tmpB[0]] = '1:1';
			else:
				relationsDict[tmpA[0]][tmpB[0]] = '';
	
	# I have relationsDict[table1][table1] = 1:1 always
	# Others are empty, so far
	# This won't be changed

	# Columns are stored in:

	for A in relationsDict:
		if d[A] != None:
			print("1Sloupce pro ", A);
			print(d[A]);


		for B in relationsDict[A]:
			if A != B:
				if d[A] != None:
					print("2Sloupce pro ", B);
					#print(d[B]);
					#print("Porovnavam: ", A+"(1)_id s ", d[B]);
					# Pokud sloupec v A tabulce 
					if B+"_id INT" in d[A] or B+"1_id INT" in d[A]:
						print(A,"->",B);
						if d[B] != None:
							print("Porovnavam",A+"(1)_id INT a ",d[B]);
							if A+"_id INT" in d[B] or A+"1_id INT" in d[B]:
								print(A,"<-",B);
								relationsDict[A][B] = 'N:M';
								#continue; # pokrocime
						else:
								relationsDict[A][B] = 'N:1'; # neplati B->A
								#continue;
					else:
						if d[B] != None:
							if A+"_id INT" in d[B] or A+"1_id INT" in d[B]:
								relationsDict[A][B] = '1:N';
								#continue;

			print("Relation:", A, ":", B, " is ", relationsDict[A][B]);




	print(relationsDict);

	# I regenerate the data structure as I need it, no values, no attributes




	# G-Param string accumulator
	outAcc = "";
	outAcc += "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	outAcc += "<tables>\n";

	#print(d.items());

	# Looping through all the tables
	for tabName in d.items():
		# Every table will have at least a row where is defined + 1:1 entity; that will be implemented with algorhytm, hopefully
		outAcc += "<table name=\"" + tabName[0] + "\">\n";

		for tRel in relationsDict[tabName[0]]:
				outAcc += "<relation to=\""+tRel+"\" relation_type=\""+relationsDict[tabName[0]][tRel]+"\" />\n";

		# Terminate table tag
		outAcc += "</table>\n";
	# Filling outAcc with terminating tables tag
	outAcc += "</tables>\n"; 
	# Accu reset, delete 
	#outAcc = "";"""

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