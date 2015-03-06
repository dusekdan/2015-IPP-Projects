<?php
const errOk = 0;
const errParams = 1;
const errInputFile = 2;
const errOutputFile = 3;
const errDirFile = 21;	// the directory is given, one of the files in it can not be open
	const errDirFileInternal = 101;	// unable to read file, program return value depends on context
const errInternal = 111;	// any other type of error

?>