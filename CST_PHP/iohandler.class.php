<?php 
/**
 *	This file contains class for working with standard input and output streams. Also takes care of parameters.
 *
 *	@author Daniel Dusek (xdusek21) <dusekdan@gmail.com>	
 */

/**
 *	Class IOHandler contains methods for quick and efficient working with stdin, stderr and stdout
 */
final class IOHandler
{

	private $options;

	/**
	 *	This method returns a value of private property $options to be used 
	 *	@return array|$options
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 *	Takes care of incoming parameters - loads them into array which it returns
	 *	@return array|$options
	 */
	private function parseParams()
	{
		$shortOpts = "";
		$shortOpts .= "w:";
		$shortOpts .= "k";
		$shortOpts .= "o";
		$shortOpts .= "i";
		$shortOpts .= "c";
		$shortOpts .= "p";

		$longOpts = array(
			"input:",
			"output:",
			"nosubdir",
			"help"
		);

		$options = getopt($shortOpts, $longOpts);
		return $options;
	}

	/**
	 *	Checks if parameters given to a filter are proper
	 *	@return const integer|errOk OR const integer|errParams
	 */
	public function checkParams()
	{

		$options = $this->parseParams();

		// check for anything that follows or precedes "help" option. If such a thing is present, then error & return value shall be generated
		if(array_key_exists("help", $options))
		{
			if(count($options) != 1)
			{
				$this->writeStderr("Help can't be used in combination with any other parameter. Generating return value: 1.\n");
				$this->terminateProgram(errParams);	// generating return value 1
			}
			$this->options = $options;
			return errOk;
		}


		// check for "input" && "nosubdir" used at once -> if they are, we need to check if the input is a single file or a directory; if it is a file, we have to generate an error
		if(array_key_exists("input", $options) && array_key_exists("nosubdir", $options))
		{
			if(is_file($options["input"]))
			{
				$this->writeStderr("Param 'input' and 'nosubdir' can not be used at once, when input specifies a file! Generating return value: 1.\n");
				$this->terminateProgram(errParams);
			}
		}

		if(array_key_exists("input", $options))
		{
			if(!is_file($options["input"]) && !is_dir($options["input"]))
			{
				$this->writeStderr("Specified input can not be opened/read.\n");
				$this->terminateProgram(errInputFile);
			}
		}

		// let's check for switches if there's only one of those: "k", "o", "i", "w", "c" | this also checks if the "w" has a value
		$koiwcArr = array(
			array_key_exists("k", $options),
			array_key_exists("o", $options),
			array_key_exists("i", $options),
			array_key_exists("w", $options),
			array_key_exists("c", $options)
			);

		if(array_sum($koiwcArr) != 1)	// if the sum of koiwcArr is not equal to one we have more than one param of this type used, or less than one => we generate error
		{
		
			$this->writeStderr("No or more than one param of the group 'koiwc' is used! Generating return value: 1.\n");
			$this->terminateProgram(errParams);
		}

		// Now all switches are checked to be used correctly, therefore property $option is set and errOk is returned
		
		$this->options = $options;

		return errOk;
	}

	/**
	 *	Returns a file content as a string - in a safe way
	 *	@param string|$filename
	 *	@return string|$fileContent OR integer|$errCode
	 */
	public function safelyGetFileContents($filename)
	{
		if((@$cont = file_get_contents($filename)) === FALSE)	// '@' sign suppress error report in case of file opening issues -> nothing on stdout, and I can handle the error on my own
		{	
			return $errCode = errDirFileInternal;
		}
		else
		{
			return $fileContent = $cont;
		}
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
	
	/**
	 *	Takes care of returning return value, using function exit()
	 *	@param int|$errno
	 *	@return void
	 */
	public function terminateProgram($errno)
	{
		exit($errno);
	}
}