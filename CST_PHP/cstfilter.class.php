<?php
final class cstFilter
{
	private $iohandler;
	private $inputFileArray = array();
	private $comentLetters = 0;

	public function __construct($IOHandlerPtr)
	{
		$this->iohandler = $IOHandlerPtr;
	}

	public function Run()
	{
		$checkParams = $this->iohandler->checkParams();

		if($checkParams == errOk)
		{
			$options = $this->iohandler->getOptions();
			$this->processOptions($options);
		}
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
		if(!(array_key_exists("output", $options)))
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
		else
		{
			if(is_file($options["input"]))
			{
				$this->inputFileArray[] = $options["input"];
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


		// for any other parametr I need to get rid of macro's and comentarries and strings...

		if(array_key_exists("c", $options))
		{
		
			$i = 0;
			foreach($this->inputFileArray as $item)
			{

				$fileContent = $this->iohandler->safelyGetFileContents($item);
				$this->stripNsave($fileContent);
				$outCount[$i] = $this->commentLetters;
				$outFiles[$i] = realpath($item);
				$bufferCount += $outCount[$i];
				++$i;
			}


		}	// 88 instead of 89, problem is with commentary at the end



		if(array_key_exists("k", $options))
		{
			$i = 0;

			foreach($this->inputFileArray as $item)
			{
				$fileContent = $this->iohandler->safelyGetFileContents($item);
				$strippedInput = $this->stripNsave($fileContent);
				$outCount[$i] = $this->preg_match_count("/\b(auto|break|case|char|const|continue|default|do|double|else|enum|extern|float|for|goto|if|int|long|register|return|short|signed|sizeof|static|struct|switch|typedef|union|unsigned|void|volatile|while|_Bool|_Complex|_Imaginary|inline|restrict)\b/", $strippedInput);
				$outFiles[$i] = realpath($item);
				$bufferCount += $outCount[$i];
				++$i;
			}
		}

		if(array_key_exists("i", $options))
		{

			$i = 0;

			foreach($this->inputFileArray as $item)
			{
				$fileContent = $this->iohandler->safelyGetFileContents($item);
				$strippedInput = $this->stripNsave($fileContent);
				$strippedInput = preg_replace("/\b(auto|break|case|char|const|continue|default|do|double|else|enum|extern|float|for|goto|if|int|long|register|return|short|signed|sizeof|static|struct|switch|typedef|union|unsigned|void|volatile|while|_Bool|_Complex|_Imaginary|inline|restrict)\b/", "", $strippedInput);
				$outCount[$i] = $this->preg_match_count("/([a-zA-Z]+)([a-zA-Z0-9_]*)/", $strippedInput);
				$outFiles[$i] = realpath($item);
				$bufferCount += $outCount[$i];
				++$i;
			}
		}

		if(array_key_exists("o", $options))
		{
			$i = 0;

			foreach($this->inputFileArray as $item)
			{
				$fileContent = $this->iohandler->safelyGetFileContents($item);
				$strippedInput = $this->stripNsave($fileContent);
				$strippedInput = str_replace("...", "", $strippedInput);
				// now there's only need to count all operators. 


				$complexRegexpRMV = "/";
				$complexRegexpRMV .= "char(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "|";
				$complexRegexpRMV .= "short(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "|";
				$complexRegexpRMV .= "int(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "|";
				$complexRegexpRMV .= "unsigned(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "|";
				$complexRegexpRMV .= "long(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "|";
				$complexRegexpRMV .= "float(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "|";
				$complexRegexpRMV .= "double(\s*)\*+(\s*)([a-zA-Z]+)([a-zA-Z0-9_]*)";
				$complexRegexpRMV .= "/";

				$strippedInput = preg_replace($complexRegexpRMV, "", $strippedInput);

				$removeNumberCNST = "/";
				$removeNumberCNST .= "[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?";
				$removeNumberCNST .= "/";

				$strippedInput = preg_replace($removeNumberCNST, "", $strippedInput);

				$complexRegexp = "/";
				$complexRegexp .= "\<\<\=|\>\>\=";
				$complexRegexp .= "|";
				$complexRegexp .= "\+\+|\-\-|\+\=|\-=|\*\=|\/\=|\%\=|\<\=|\>\=|\=\=|\!\=|\&\&|\|\||\<\<|\>\>|\&\=|\|\=|\^\=|\-\>";
				$complexRegexp .= "|";
				$complexRegexp .= "\+|\-|\*|\/|\%|\<|\>|\!|\~|\&|\||\^|\=|\.";
				$complexRegexp .= "/";

				$outCount[$i] = $this->preg_match_count($complexRegexp, $strippedInput);


				$outFiles[$i] = realpath($item);
				$bufferCount += $outCount[$i];
				++$i;
			}
		}

		// handling "p" param - simple stripping full file path to name only version; this method have to be called after all previous filtering is done
		if(array_key_exists("p", $options))
		{
			$outFiles = $this->getNamesOnlyArray($outFiles);		
		}

		
		// some sort would be nice


		$this->iohandler->printData($outFiles, $outCount, $bufferCount, $options["output"]);
	}


	private function stripNsave($fileContent)
	{
				// state machine, let's try again

				$actState = "sSTART";
				$skipB = false;
				$skipM = false;
				$skipN = 0;

				$lastLineCheck = false;

				$commentLetters = 0;

				for($i=0; isset($fileContent[$i]); $i++)
				{
					if($skipB)
					{
						$fileContent[$i] = '';
						$skipB = false;
						continue;
					}

					if($skipM)
					{
						$fileContent[$i] = '';
						$skipN--;
							if($skipN == 0)
							{
								$skipM = false;
							}
					}


					switch($actState)
					{
						case "sSTART":

							if($lastLineCheck)
							{
								$lastLineCheck = false;
							}

							if($fileContent[$i] == '#')
							{
								$fileContent[$i] = '';
								$actState = "sMACRO";
								$skipM = true;
								$skipN = 6;
								continue;
							}

							if($fileContent[$i] == '\'')
							{
								$fileContent[$i] = '';
								$actState = "sCHARLIT";
								continue;
							}

							if($fileContent[$i] == '"')
							{
								$fileContent[$i] = '';
								$actState = "sSTRING";
								continue;
							}

							if($fileContent[$i] == "/")
							{
								if($fileContent[$i+1] == "/")
								{
									$fileContent[$i] = '';
									$actState = "sLINECOMMENT";
									$skipB = true;
									$commentLetters += 2;
									continue;
								}

								if($fileContent[$i+1] == "*")
								{
									$fileContent[$i] = '';
									$actState = "sBLOCKCOMMENT";
									$skipB = true;
									$commentLetters += 2;									
									continue;
								}
							}

						break;

						case "sIDC":	// case I DONT CARE

						break;

						case "sCHARLIT":
							if($fileContent[$i] == '\'')	// POTENTIONAL ESCAPE HAZARD OVER HERE
							{
								$fileContent[$i] = '';
								$actState = "sSTART";
							}
							else
							{
								$fileContent[$i] = '';
							}
						break;

						case "sSTRING":
							if($fileContent[$i] == '"')	// POTENTIONAL ESCAPE HAZARD OVER HERE
							{
								$fileContent[$i] = '';
								$actState = "sSTART";
							}
							else
							{
								$fileContent[$i] = '';
							}
						break;

						case "sLINECOMMENT":
							$commentLetters++;
							$lastLineCheck = true;
							if($fileContent[$i] == PHP_EOL)
							{
								$actState = "sSTART";
								$fileContent[$i] = '';
							}
							else
							{
								$fileContent[$i] = ''; 
							}
						break;

						case "sBLOCKCOMMENT":
							if($fileContent[$i] == '*')
							{
								if($fileContent[$i+1] == '/')
								{
									$fileContent[$i] = '';
									$actState = "sSTART";
									$skipB = true;
									$commentLetters += 2;
									continue;
								}
								else
								{
									$fileContent[$i] = '';
									$commentLetters++;
								}
							}
							else
							{
								$fileContent[$i] = '';
								$commentLetters++;
							}
						break;

						case "sMACRO":
							if($fileContent[$i] == '\\' && ord($fileContent[$i+1]) == 10)
							{
								$fileContent[$i] = '';
								$actState = "sMACRO";
								$skipB = true;
								continue;
							}
							else
							{
								if(ord($fileContent[$i]) == 10)
								{
									$fileContent[$i] = '';
									$actState = "sSTART";
									continue;
								}
								else
								{
									$fileContent[$i] = '';
								}
							}
						break;
					}




				}
				if($lastLineCheck)
				{
					$commentLetters++;
				}
				$this->commentLetters = $commentLetters;
			return $fileContent;
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
		$this->iohandler->writeStdout("Program:\n\t cst.php\n");
		$this->iohandler->writeStdout("Author:\n\t Daniel Dusek (xdusek21) <dusekdan@gmail.com>\n");
		$this->iohandler->writeStdout(
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