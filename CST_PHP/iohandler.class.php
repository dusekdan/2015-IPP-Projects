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

	private $errParam = 1;

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
	 *	Call parseParams() and gets script parameters as a array, checks them if they are correct, based on results invokes error code or proceed further
	 *	@return value not specified YET
	 */
	public function handleParams()
	{

		$options = $this->parseParams();
		
		if(!is_array($options))
		{
			$this->terminateProgram(666, "You're trying to abuse this method and you shall be terminated. I am sorry.\n");
		}

		var_dump($options);

		// check for anything that follows or precedes "help" option. If such a thing is present, then error & return value shall be generated
		if(array_key_exists("help", $options))
		{
			if(count($options) != 1)
			{
				$this->writeStdout("Help can't be used in combination with any other parameter. Generating return value: 1.\n");
				$this->terminateProgram($this->errParam);	// generating return value 1
			}

			// HELP PRITOMEN ZDE

		}


		// check for "input" && "nosubdir" used at once, so we can generate error
		if(array_key_exists("input", $options) && array_key_exists("nosubdir", $options))
		{
			$this->writeStdout("Param 'input' and 'nosubdir' can not be used at once! Generating return value: 1.\n");
			$this->terminateProgram($this->errParam);
		}
		else
		{
			$this->writeStdout("Input a subdir nepouzito naraz!\n");
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
		
			$this->writeStdout("No or more than one param of the group 'koiwc' is used! Generating return value: 1.\n");	// know error with "-nosubdir" right here.
			$this->terminateProgram($this->errParam);
		}




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

	private function terminateProgram($errno)
	{
		exit($errno);
		// IN PROGRESS - constants for different return values required
	}
}
?>
