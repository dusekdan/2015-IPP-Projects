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

		if(array_key_exists("help", $options) && count($options) != 1)
		{
			$this->writeStdout("Help obsazeno, ale dalsi taky. Error!\n");
		}
		else
		{
			$this->writeStdout("Only help obsazeno! Good!\n");
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
		$shortOpts .= "koicp";

		$longOpts = array(
			"input:",
			"output:",
			"nosubdir",
			"help"
		);

		$options = getopt($shortOpts, $longOpts);
		return $options;
	}

	private function terminateProgram($errno, $message)
	{
		$this->writeStderr($message);
		exit($errno);
		// IN PROGRESS - constants for different return values required
	}
}
?>
