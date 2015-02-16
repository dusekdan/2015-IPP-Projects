<?php

include("./iohandler.class.php");
$iohandler = new IOHandler();

$iohandler->handleParams();

/*function handleParams()
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

$option = getopt($shortOpts, $longOpts);
return $option;

}*/

//var_dump($iohandler->handleParams());



//$name = $iohandler->readStdin();



?>