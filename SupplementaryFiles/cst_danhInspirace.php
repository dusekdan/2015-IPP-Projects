<?php
function error($value) {
	$error_message = array(
		1	=>	"Error 1 - Info",
		2	=>	"Error 2 - Info",
		3	=>	"Error 3 - Info",
		4	=>	"Error 4 - Info"
	);

	fwrite(STDERR, $error_message[$value]."\n");
	exit($value);
}

function check_args($opt, $set) {
	if($set['help'] &&
		count($opt) == 1) {}
	else if (!$set['help'] &&
		($set['k'] xor
			$set['o'] xor
			$set['i'] xor
			$set['w'] xor
			$set['c']) &&
		(($set['input'] &&
		!is_dir($opt['input']) &&
		!$set['nosubdir'])) ||
		($set['input'] &&
		is_dir($opt['input']) &&
		$set['nosubdir']) ||
		($set['input'] &&
		is_dir($opt['input']))) {}
	else {
		error(1);
	}
}

function help() {
	echo "HELP is here!\n";
}

// should be complete
function count_keywords($input) {
	global $set, $data;
	$keywords = array("auto", "break", "case", "char", "const", "continue",
		"default", "do", "double", "else", "enum", "extern", "float", "for",
		"goto", "if", "int", "long", "register", "return", "short", "signed",
		"sizeof", "static", "struct", "switch", "typedef", "union", "unsigned",
		"void", "volatile", "while");

	if (is_dir($input)) {
		$files = scandir($input);
		unset($files[0]);	// removes . (dir)
		unset($files[1]);	// removes .. (dir)
		foreach ($files as $file) {
			$file_e = explode(".", $input."/".$file);
			$extension = end($file_e);				// gets file's extension
			$is_dir = is_dir($input."/".$file);		// checks if file is folder
			// if file is folder and nosubdir is active, folder will be skipped
			if ($set['nosubdir'] && $is_dir) { continue; }
			// if file has unknown extension, file will be skipped
			if (!$is_dir && $extension != "c" && $extension != "h") { continue; }
			count_keywords($input."/".$file);
		}
		return;
	}

	// dodelat kontrolu otevreni souboru
	$fr = fopen($input, "rb");
	$c = "";
	if($fr) {
		while (!feof($fr)) {
			$c .= fread($fr, 512);
		}
	}
	fclose($fr);

	$c = preg_replace("/^[ \t]*#define(.*\\\n)*.*$/m", "", $c);	// removes macros
	#$c = preg_replace("/\".*\"/", "", $c);						// removes strings
	#$c = preg_replace("/\s*\/\/.*/", "", $c);					// removes single line comments
	#$c = preg_replace("/\/\*.*\*\//s", "", $c);					// removes multiple lines comments
	echo $c;

	$counter = 0;
	foreach ($keywords as $keyword) {
		preg_match_all("/\b".$keyword."/i", $c, $o);	// finds all keywords
		$counter += count($o[0]);
	}
	
	$filename = (($set['p']) ? $input : realpath($input));
	if(strlen($filename) > $data['left_max']) { $data['left_max'] = strlen($filename); }
	if(strlen($counter) > $data['right_max']) { $data['right_max'] = strlen($counter); }
	array_push($data, array($filename, $counter));
}

function print_data($data, $output) {
	$line_length = $data['left_max'] + $data['right_max'] + 1;
	unset($data['left_max']);
	unset($data['right_max']);
	foreach ($data as $item) {
		echo $item[0];
		$left_length = strlen($item[0]);
		$right_length = strlen($item[1]);
		for($i = 0; $i < ($line_length-$left_length-$right_length); $i++) {
			echo " ";
		}
		echo $item[1]."\n";
	}
}

$opt = getopt("koiw:cp", array("help", "input:", "nosubdir", "output:"));
$set = array(
	"k"			=>	isset($opt['k']),
	"o"			=>	isset($opt['o']),
	"i"			=>	isset($opt['i']),
	"w"			=>	isset($opt['w']),
	"c"			=>	isset($opt['c']),
	"p"			=>	isset($opt['p']),
	"help"		=>	isset($opt['help']),
	"input"		=>	isset($opt['input']),
	"nosubdir"	=>	isset($opt['nosubdir']),
	"output"	=>	isset($opt['output'])
);
$data = array(
	"left_max" => 0,
	"right_max" => 0
);
$input = ($set['input']) ? $opt['input'] : 'php://stdin';
$output = ($set['output']) ? $opt['output'] : 'php://stdout';

check_args($opt, $set);
if ($set['help']) {
	help();
} else if ($set['k']) {
	count_keywords($input);
}
print_data($data, $output);
?>