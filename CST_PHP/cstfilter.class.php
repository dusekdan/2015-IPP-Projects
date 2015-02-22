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

		print $this->iohandler->checkParams()."\n";

		echo "Filter work done...\n";
	}

	
}
?>