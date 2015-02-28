<?php
final class cstFilter
{
	private $iohandler;
	private $inputFileArray = array();

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
		if(!array_key_exists("input", $options))
		{
			$options["input"] = ".";
		}

		// similarly for output
		if(!(array_key_exists("ouput", $options)))
		{
			$options["output"] = "php://stdout";
		}

		// here will come work for each switch separatively

		// input & nosubdir
		if(is_dir($options["input"]))
		{
			if(!array_key_exists("nosubdir", $options))
			{
				$this->recDirLookup($options["input"]);
			}
			else
			{
				$this->dirLookup($options["input"]);
			}
		}


		$bufferCount = 0;
		$outCount = array();
		$outFiles = array();

		// handling "w param"
		if(array_key_exists("w", $options))	// thanks to briliant getopts, if it's passed like "-w --nosubdir" - which is incorect format by the way, it is considered -w with value "--nosubdir" which happens to be a problem
		{
			$i = 0;

			if(!empty($options["w"]))
			{
				$pattern = $options["w"];
				foreach($this->inputFileArray as $item)
				{
					$fileContent = $this->iohandler->safelyGetFileContents($item);
					$outCount[$i] = $this->preg_match_count("/$options[w]/", $fileContent);
					$outFiles[$i] = realpath($item);	// transforms the relative url (which is most likely to be given by majority of testscripts) to absolute; when url already absolute nothing changes
					$bufferCount += $outCount[$i];
					++$i;
				}

			}
			else
			{
				$this->iohandler->writeStderr("Empty value for -w switch!\n");
				$this->iohandler->terminateProgram(errParams);
			}
		}



		// handling "p" param - simple stripping full file path to name only version; this method have to be called after all previous filtering is done
		if(array_key_exists("p", $options))
		{
			$outFiles = $this->getNamesOnlyArray($outFiles);		
		}



		

		for($i=0;$i<count($outCount);$i++)
		{
			echo "$outFiles[$i] \t $outCount[$i]\n";
		}

	}

	private function getNamesOnlyArray($files)
	{
		$i = 0;
		foreach($files as $file)
		{
			$files[$i] = basename($file);
			$i++;
		}
		return $files;
	}

	private function preg_match_count($pattern, $formule)
	{
		if(preg_match_all($pattern, $formule, $matches, PREG_PATTERN_ORDER))
		{
			return count($matches[0]);
		}
		else
		{
			return 0;
		}
	}

	private function recDirLookup($dir)
	{

    	$folder = scandir($dir);
    
    	foreach($folder as $ff){
        	if($ff == '.' || $ff == '..')
        	{
        		continue;
        	}
        	else
        	{

        		$isItDirOrFile = (is_dir($dir.'/'.$ff))?"DIR":"FILE";
				
				if( $this->getFileExtension($dir.'/'.$ff) == 'c' || $this->getFileExtension($dir.'/'.$ff) == 'h')
				{        	
        			$this->inputFileArray[] = $dir.'/'.$ff;
           	 	}
           	
           		if(is_dir($dir.'/'.$ff)) 
           		{
           			$this->recDirLookup($dir.'/'.$ff);
           		}
        	}
    	}
	}

	private function dirLookup($dir)
	{
		$folder = scandir($dir);
    
    	foreach($folder as $ff){
        	if($ff == '.' || $ff == '..' || is_dir($dir.'/'.$ff))
        	{
        		continue;
        	}
        	else
        	{

        		$isItDirOrFile = (is_dir($dir.'/'.$ff))?"DIR":"FILE";
				
				if( $this->getFileExtension($dir.'/'.$ff) == 'c' || $this->getFileExtension($dir.'/'.$ff) == 'h')
				{        	
        			$this->inputFileArray[] = $dir.'/'.$ff;
           	 	}

        	}
    	}	
	}

	private function getFileExtension($fileName)
	{
		return pathinfo($fileName, PATHINFO_EXTENSION);
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