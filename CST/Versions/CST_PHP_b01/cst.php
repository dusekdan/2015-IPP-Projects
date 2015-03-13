<?php
error_reporting(E_ALL);

$keywordsC99 = array("auto", "break", "case", "char", "const", "continue", "default", "do", "double", "else", "enum", "extern", "float", "for", "goto", "if", "int", "long", "register", "return", "short",
	"signed", "sizeof", "static", "struct", "switch", "typedef", "union", "unsigned", "void", "volatile", "while", "_Bool", "_Complex", "_Imaginary", "inline", "restrict");




include("./iohandler.class.php");
include("./cstprogram.class.php");


$cstProgram = new cstProgram();

$iohandler = new IOHandler($cstProgram);

$iohandler->handleParams();
	$cstProgram->Run();









// that's how I get the file content into string variable - safely | newfile - cant be read, newfile2 can be read
/*if((@$cont = file_get_contents("newfile")) === FALSE)
{
	echo "Unable to open newfile!\n";
}
else
{
	echo "Able to open newfile!\n";
	print $cont;
}

if(!($cont2 = file_get_contents("newfile2")))
{
	echo "Unable to open newfile2!\n";
}
else
{
	echo "Able to open newfile2!\n";
	print $cont2;
}*/





?>