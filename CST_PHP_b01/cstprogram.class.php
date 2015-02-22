<?php
/**
 *	This program contains a class that is responsible for all the C stat analyses and generally the functionality of the program
 *
 *	@author Daniel Dusek (xdusek21) <dusekdan@gmail.com>
 */


/**
 *	Class cstProgram cointaining methods for functionality of the program. 
 */
class cstProgram
{

	private $showHelpB = false;
	private $options = "NULL";

	public function propertySetter($propertyName, $propertyValue)
	{
		if(isset($this->$propertyName))
		{
			$this->$propertyName = $propertyValue;
			echo "Property \$$propertyName:SET\n";
		}
		else
		{
			echo "Property \$$propertyName:NOTSET (property does not exist)\n";
		}
	}

	public function showHelp()
	{
		print("Program:\n\t cst.php\n");
		print("Author:\n\t Daniel Dusek (xdusek21) <dusekdan@gmail.com>\n");
		print(
				"Usage:\n\t php cst.php -k|-w=pattern|-c|-o|-i|--help [--input=fileordir|--nosubdir] [--output=file] [-p]\n \n" .
				"Parameters:\n\n" . 
				"\t--help \t \t \t Shows help on screen.\n\n" .
				"\t-k \t \t \t Prints out a number of keywords in file(s).\n\t\t\t\t Excluding keywords contained in commentaries\n\t\t\t\t or strings.\n\n" .
				"\t-o \t \t \t Prints out a number of simple operators.\n\t\t\t\t A simple operator stands for known and\n\t\t\t\t fixed sequence of non-alpha characters.\n\t\t\t\t Excluding simple operators contained\n\t\t\t\t in commentaries, strings and character\n\t\t\t\t literals.\n\n" .
				"\t-i \t \t \t Prints out a number of appearance of \n\t\t\t\t a identifiers, excluding keywords for all\n\t\t\t\t analysed file(s).\n\n" .
				"\t-c \t \t \t Prints out a total number of letters\n\t\t\t\t of all commentaries, including '//', '/*'\n\t\t\t\t or '*/'. End of line is counted.\n\t\t\t\t For separated file(s) and total.\n\n" .
				"\t-w=pattern \t \t Search for a precise string given by\n\t\t\t\t 'pattern', prints out total number for\n\t\t\t\t a file and for all files. This\n\t\t\t\t search is done on whole file \n\t\t\t\t commentaries, strings or char literals\n\t\t\t\t included.\n\n" .
				"\t[--input=fileordir] \t Specifies whether we work on\n\t\t\t\t a file or directory. If file is given \n\t\t\t\t no matter the extension process\n\t\t\t\t is done. If folder is given \n\t\t\t\t program works only on *.c, *.h. \n\t\t\t\t If parameter is not given at all,\n\t\t\t\t program works on current directory,\n\t\t\t\t on *.c, *.h files only! Input file \n\t\t\t\t is in ISO-8859-2.\n\n" .
				"\t[--output=] \t \t Specifies output file the program\n\t\t\t\t is supposed to write in the result. \n\t\t\t\t If parameters is not given stdout\n\t\t\t\t is used instead.\n\n" .
				"\t[--nosubdir] \t \t If directory is given by 'input'\n\t\t\t\t this says we do not look for subdirs.\n\t\t\t\t Cannot be combined with direct\n\t\t\t\t file selection by 'input'.\n\n" .
				"\t[-p] \t \t \t In combination with previous switches\n\t\t\t\t except for '--help', causes that file(s)\n\t\t\t\t on output will not be put\n\t\t\t\t with absolute URL.\n\n"
		);
	}


	public function Run()
	{

		/*if($this->options == "NULL")
		{
			print("RUNTIME ERROR!\n");
			exit(666);
		}*/

		echo $this->showHelpB;
		if($this->showHelpB)
		{
			$this->showHelp();
			exit(0);
		}

		// order of parameter processing is given by task assignment
		
		if(is_file($this->options["input"]))
		{

			echo "YAAA\n";
		}else if($this->options["input"] == 'php://stdin')
		{
			$fileContents = $this->readStdin();
			print($fileContents);
		}else if(is_dir($this->options["input"]))
		{
			echo "God damn it, folder, i should cycle it through\n";
		
			$this->recDirLookup($this->options["input"]);

			/*$scan = scandir($this->options["input"]);
			var_dump($scan);*/

		}

		//$inputFileContent = file_get_contents($this->options["input"]);

		//echo $inputFileContent;



	}


	public function recDirLookup($dir)
	{

    	$folder = scandir($dir);
    	echo "\n";
    
    	foreach($folder as $ff){
        	if($ff == '.' || $ff == '..') continue;
        	else
        	{

        		$isItDirOrFile = (is_dir($dir.'/'.$ff))?"DIR":"FILE";

           	 echo "\t".$ff." ($isItDirOrFile)";
           	 if(is_dir($dir.'/'.$ff)) $this->recDirLookup($dir.'/'.$ff);
           	 echo "\n";
        	}
    	}
    
    	echo "\n";
	
	}

	/**
	 * Takes care of returning return value, using function exit()
	 * @param int|$errno
	 * @return void
	 */
	public function terminateProgram($errno)
	{
		exit($errno);
	}

	/**
	 *	Returns a line from standard input
	 *	@return string|$return
	 */
	public function readStdin()
	{
		$fd = fopen("php://stdin", "r");	// "r" opens stdin in read-only mode
		$return = fgets($fd); // no length specified - intention is to take everything that comes
		fclose($fd);
		return $return;
	}

	/**
	 *	Returns boolean if writting to standard error was successful
	 *	@return boolean
	 */
	public function writeStderr($message)
	{
		$fd = fopen("php://stderr", "w");	// "w" opens for write, because we apparently need to write the message out
		fputs($fd, $message);
		fclose($fd);
		return true;
	}

	/**
	 *	Writes on standard output, then returns boolean true
	 *	@return boolean
	 */
	public function writeStdout($message)
	{
		print($message);	// printing out a message in a safe way straight to stdout
		return true;
	}

}





?>