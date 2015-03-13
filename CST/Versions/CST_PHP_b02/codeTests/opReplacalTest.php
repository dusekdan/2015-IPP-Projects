<?php


$string =" int a = a+b; a += b; b -= a-b";

$string = str_replace("+=", "UNIFIED", $string);
$string = str_replace("-=", "UNIFIED", $string);
$string = str_replace("+", "UNIFIED", $string);
$string = str_replace("-", "UNIFIED", $string);
$string = str_replace("=", "UNIFIED", $string);

$var = preg_match_all("/UNIFIED/", $string, $matches);
var_dump($matches); 

echo "Pocet vyskytu: ".count($matches[0]).PHP_EOL;

?>