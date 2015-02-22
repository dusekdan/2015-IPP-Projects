<?php
final class cstFilter
{
	private $iohandler;

	public function __construct($IOHandlerPtr)
	{
		$this->iohandler = $IOHandlerPtr;
	}

	public function Run()
	{
		echo "Starting a filter...\n";


		$checkParams = $this->iohandler->checkParams();
		print "Errorcode from checkParams(): ". $checkParams ."\n";

		if($checkParams == errOk)
		{
			$options = $this->iohandler->getOptions();
			$this->processOptions($options);
		}

		echo "Filter work done...\n";
	}

	private function processOptions($options)
	{

		if(array_key_exists("help", $options))
		{
			$this->showHelp();
			return errOk;
		}

		// if input does not exist (is not given), filtr assumes that input will come from stdin
		if(!(array_key_exists("input", $options))
		{
			$options["input"] = "php://stdin";
		}

		// similarly for output
		if(!(array_key_exists("ouput", $options)))
		{
			$options["output"] = "php://stdout";
		}

		// here will come work for each switch separatively



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

	private function showHelp()
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
	
}
?>