<?php 
error_reporting(E_ALL);
$t1 = microtime(true);
$keywordsC99 = array("auto", "break", "case", "char", "const", "continue", "default", "do", "double", "else", "enum", "extern", "float", "for", "goto", "if", "int", "long", "register", "return", "short",
	"signed", "sizeof", "static", "struct", "switch", "typedef", "union", "unsigned", "void", "volatile", "while", "_Bool", "_Complex", "_Imaginary", "inline", "restrict");

include("./error.consts.php");
include("./iohandler.class.php");
include("./cstfilter.class.php");


$iohandler = new IOHandler();
$cstFilter = new cstFilter($iohandler);

$cstFilter->Run();
$t2 = microtime(true);

$totalTime = $t2-$t1;
echo "Cas vykonavani skriptu:".$totalTime."s!\n";



?>