<?php

//load file into array
$fileContents = file('1letterFolder/21.c');

//init counter
$charCount = 0;

foreach ($fileContents as $fileContent)
{
    //+count
    $charCount +=  (strpos(trim($fileContent),'//') === 0 )?strlen(trim($fileContent)):0;
}

//print
echo $charCount;

?>
