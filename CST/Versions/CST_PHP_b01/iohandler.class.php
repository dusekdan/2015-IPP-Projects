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

	const errOk = 0;
	const errParam = 1;

	private $program;

	/**
	 * Constructor - loading pointer to cstProgram object to a 'program' property
	 * @return void
	 */

	public function __construct($cstProgramPtr)
	{
		$this->program = $cstProgramPtr;
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
	 *	Call parseParams() and gets script parameters as a array, checks them if they are correct, based on results invokes error code or proceed further
	 *	@return value not specified YET
	 */
	public function handleParams()
	{

		$options = $this->parseParams();
		
		if(!is_array($options))
		{
			$this->program->terminateProgram(666);	// CHANGE ERROR CODE
		}

		// check for anything that follows or precedes "help" option. If such a thing is present, then error & return value shall be generated
		if(array_key_exists("help", $options))
		{
			if(count($options) != 1)
			{
				$this->writeStdout("Help can't be used in combination with any other parameter. Generating return value: 1.\n");
				$this->program->terminateProgram(self::errParam);	// generating return value 1
			}

			//$this->program->showHelp();
			$this->program->propertySetter("showHelpB", true);
			return true;
		}


		// check for "input" && "nosubdir" used at once -> if they are, we need to check if the input is a single file or a directory; if it is a file, we have to generate an error
		if(array_key_exists("input", $options) && array_key_exists("nosubdir", $options))
		{
			if(is_file($options["input"]))
			{
				$this->writeStdout("Param 'input' and 'nosubdir' can not be used at once, if input specifies a file! Generating return value: 1.\n");
				$this->program->terminateProgram(self::errParam);
			}
		}
		else
		{
			// in case that input switch is not present, we will have to read from standard input, therefore I add "input" key to the array $options pointed to standard input file
			if(!array_key_exists("input", $options))
			{
				$options["input"] = "php://stdin";
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
		
			$this->writeStdout("No or more than one param of the group 'koiwc' is used! Generating return value: 1.\n");	// know error with "-nosubdir" right here.
			$this->program->terminateProgram(self::errParam);
		}


		// NOW WE HAVE ALL SWITCHES CHECKED AND WE PROCEED TO PROCESSING THEM
			// since processing parameters is the "program part", I will just pass parameters to the class responsible for functionality of a program
		$this->program->propertySetter("options", $options);
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
}
?>