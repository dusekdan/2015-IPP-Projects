<?php
function error($value) {
	$error_message = array(
		1	=>	"Error 1 - Info",
		2	=>	"Error 2 - Info",
		3	=>	"Error 3 - Info",
		4	=>	"Error 4 - Info",
		21	=>	"Error 21 - Info"
	);

	fwrite(STDERR, $error_message[$value]."\n");
	exit($value);
}

function check_args($opt, $set) {
	if($set['help'] &&
		count($opt) == 1 &&
		count($opt['help']) == 1) {}
	else if (
				!$set['help'] &&
				(
					(isset($opt['k']) && count($opt['k']) == 1) ||
					(isset($opt['o']) && count($opt['o']) == 1) ||
					(isset($opt['i']) && count($opt['i']) == 1) ||
					(isset($opt['w']) && count($opt['w']) == 1) ||
					(isset($opt['c']) && count($opt['c']) == 1)
				)
				&&
				(
					$set['k'] xor
					$set['o'] xor
					$set['i'] xor
					$set['w'] xor
					$set['c']
				)
				&&
				(
					(
						(
							$set['input'] &&
							count($opt['input']) == 1 &&
							!is_dir($opt['input']) &&
							!$set['nosubdir']
						)
					)
					||
					(
						$set['input'] &&
						count($opt['input']) == 1 &&
						is_dir($opt['input']) &&
						$set['nosubdir'] &&
						count($opt['nosubdir']) == 1
					)
					||
					(
						$set['input'] &&
						count($opt['input']) == 1 &&
						is_dir($opt['input']) &&
						!$set['nosubdir']
					)
					||
					(!$set['input'])
				)
			) {}
	else {
		error(1);
	}
}

function help() {
	echo "Daniel Hladik (xhladi21) - FIT VUT\n\n";
	echo "Pouziti: php cts.php [options]\n";
	echo "--help\t\t\tZobrazi napovedu.\n";
	echo "--input=fileordir\tVstupni soubor nebo slozka.\n";
	echo "--nosubdir\t\tNebudou se prohledavat podslozky.\n";
	echo "--output=filename\tVystupni soubor.\n";
	echo "-k\t\t\tSpocita vsechny klicove slova.\n";
	echo "-o\t\t\tSpocita jednoduche operatory.\n";
	echo "-i\t\t\tSpocita vsechny identifikatory.\n";
	echo "-w=pattern\t\tSpocita vyskyt zadaneho retezce(pattern).\n";
	echo "-c\t\t\tSpocita znaky v komentarich.\n";
	echo "-p\t\t\tVypis souboru bude bez absolutni cesty.\n";
}

// lists files and subfolders in current folder and calls function
function list_dir($input, $function) {
	global $set;
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
		$function($input."/".$file);
	}
}

// loads content from file
function load_content($input) {
	$fr = fopen($input, "rb");
	if (!$fr) { error(21); }
	$c = "";
	while (!feof($fr)) {
		$c .= fread($fr, 512);
	}
	fclose($fr);
	return $c;
}

function count_keywords($input) {
	global $set, $data;
	$keywords = array("auto", "break", "case", "char", "const", "continue",
		"default", "do", "double", "else", "enum", "extern", "float", "for",
		"goto", "if", "int", "long", "register", "return", "short", "signed",
		"sizeof", "static", "struct", "switch", "typedef", "union", "unsigned",
		"void", "volatile", "while");

	if (!file_exists($input)) { error(2); }
	if (is_dir($input)) {
		list_dir($input, 'count_keywords');
		return;
	}

	$c = load_content($input);
	$c = preg_replace("/^[ \t]*#include.*$/m", "", $c);			// removes includes
	$c = preg_replace('/^[ \t]*#define(.*\\\n)*.*$/m', "", $c);	// removes macros
	$c = preg_replace('/\'.*\'/', "", $c);						// removes chars
	$c = preg_replace('/\".*\"/', "", $c);						// removes strings
	$c = preg_replace('/\s*\/\/.*/', "", $c);					// removes single line comments
	$c = preg_replace('/\/\*.*\*\//s', "", $c);					// removes multiple lines comments

	$counter = 0;
	foreach ($keywords as $keyword) {
		preg_match_all("/\b".$keyword."/i", $c, $o);	// finds all keywords
		$counter += count($o[0]);
	}
	
	$filename = (($set['p']) ? basename($input) : realpath($input));
	if(strlen($filename) > $data['left_max']) { $data['left_max'] = strlen($filename); }
	if(strlen($counter) > $data['right_max']) { $data['right_max'] = strlen($counter); }
	array_push($data, array($filename, $counter));
}

function count_operators($input) {
	global $set, $data;

	if (!file_exists($input)) { error(2); }
	if (is_dir($input)) {
		list_dir($input, 'count_operators');
		return;
	}

	$c = load_content($input);
	$c = preg_replace("/^[ \t]*#include.*$/m", "", $c);			// removes includes
	$c = preg_replace('/^[ \t]*#define(.*\\\n)*.*$/m', "", $c);	// removes macros
	$c = preg_replace('/\'.*\'/', "", $c);						// removes chars
	$c = preg_replace('/\".*\"/', "", $c);						// removes strings
	$c = preg_replace('/\s*\/\/.*/', "", $c);					// removes single line comments
	$c = preg_replace('/\/\*.*\*\//s', "", $c);					// removes multiple lines comments

	// counts '++', '--', '==', '!=', '>=', '<=', '&&', '||', '<<', '>>', '->*', '->', '.*', '=', '+', '-', '*', '/', '%', '<', '>', '!', '~', '&', '|', '^', '.'
	preg_match_all('/(\+\+|\-\-|\=\=|\!\=|\>\=|\<\=|\&\&|\|\||\<\<|\>\>|\-\>\*|\-\>|\.\*|[\=\+\-\*\/\%\<\>\!\~\&\|\^\.])/', $c, $o);
	$counter = count($o[0]);

	$filename = (($set['p']) ? basename($input) : realpath($input));
	if(strlen($filename) > $data['left_max']) { $data['left_max'] = strlen($filename); }
	if(strlen($counter) > $data['right_max']) { $data['right_max'] = strlen($counter); }
	array_push($data, array($filename, $counter));
}

function count_identifiers($input) {
	global $set, $data;
	$keywords = array("auto", "break", "case", "char", "const", "continue",
		"default", "do", "double", "else", "enum", "extern", "float", "for",
		"goto", "if", "int", "long", "register", "return", "short", "signed",
		"sizeof", "static", "struct", "switch", "typedef", "union", "unsigned",
		"void", "volatile", "while");

	if (!file_exists($input)) { error(2); }
	if (is_dir($input)) {
		list_dir($input, 'count_identifiers');
		return;
	}

	$c = load_content($input);
	$c = preg_replace("/^[ \t]*#include.*$/m", "", $c);			// removes includes
	$c = preg_replace('/^[ \t]*#define(.*\\\n)*.*$/m', "", $c);	// removes macros
	$c = preg_replace('/\'.*\'/', "", $c);						// removes chars
	$c = preg_replace('/\".*\"/', "", $c);						// removes strings
	$c = preg_replace('/\s*\/\/.*/', "", $c);					// removes single line comments
	$c = preg_replace('/\/\*.*\*\//s', "", $c);					// removes multiple lines comments
	foreach ($keywords as $keyword) {
		$c = preg_replace("/\b".$keyword."/i", "", $c);			// removes all keywords
	}
	// removes all operators
	$c = preg_replace('/(\+\+|\-\-|\=\=|\!\=|\>\=|\<\=|\&\&|\|\||\<\<|\>\>|\-\>\*|\-\>|\.\*|[\=\+\-\*\/\%\<\>\!\~\&\|\^\.])/', "", $c);

	// counts all identifiers
	preg_match_all('/[a-zA-Z_][a-zA-Z0-9_]{0,30}/', $c, $o);
	$counter = count($o[0]);

	$filename = (($set['p']) ? basename($input) : realpath($input));
	if(strlen($filename) > $data['left_max']) { $data['left_max'] = strlen($filename); }
	if(strlen($counter) > $data['right_max']) { $data['right_max'] = strlen($counter); }
	array_push($data, array($filename, $counter));
}

function count_strings($input) {
	global $set, $data, $pattern;

	if (!file_exists($input)) { error(2); }
	if (is_dir($input)) {
		list_dir($input, 'count_strings');
		return;
	}

	$c = load_content($input);

	preg_match_all('/\b'.$pattern.'/', $c, $o);
	$counter = count($o[0]);

	$filename = (($set['p']) ? basename($input) : realpath($input));
	if(strlen($filename) > $data['left_max']) { $data['left_max'] = strlen($filename); }
	if(strlen($counter) > $data['right_max']) { $data['right_max'] = strlen($counter); }
	array_push($data, array($filename, $counter));
}

function count_comments($input) {
	global $set, $data;

	if (!file_exists($input)) { error(2); }
	if (is_dir($input)) {
		list_dir($input, 'count_comments');
		return;
	}
	
	$c = load_content($input);

	$counter = 0;
	preg_match_all('/\/\/.*/', $c, $o);				// match single line comments
	foreach ($o[0] as $i) {
		$counter += strlen($i);
	}
	preg_match_all('/\/\*(.*\n)*.*\*\//', $c, $o);	// match multiple lines comments
	foreach ($o[0] as $i) {
		$counter += strlen($i);
	}

	$filename = (($set['p']) ? basename($input) : realpath($input));
	if(strlen($filename) > $data['left_max']) { $data['left_max'] = strlen($filename); }
	if(strlen($counter) > $data['right_max']) { $data['right_max'] = strlen($counter); }
	array_push($data, array($filename, $counter));
}

function print_data($data, $output) {
	$counter = 0;
	$left_max = $data['left_max'];
	$right_max = $data['right_max'];
	unset($data['left_max']);
	unset($data['right_max']);
	foreach ($data as $item) {
		$counter += $item[1];
	}
	array_push($data, array("CELKEM:", $counter));
	if (strlen("CELKEM:") > $left_max) { $left_max = strlen("CELKEM:"); }
	if (strlen($counter) > $right_max) { $right_max = strlen($counter); }
	$line_length = $left_max + $right_max + 1;
	foreach ($data as $item) {
		echo $item[0];
		$left_length = strlen($item[0]);
		$right_length = strlen($item[1]);
		for($i = 0; $i < ($line_length-$left_length-$right_length); $i++) {
			echo " ";
		}
		$counter += $item[1];
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
$input = ($set['input']) ? $opt['input'] : getcwd();
$output = ($set['output']) ? $opt['output'] : 'php://stdout';

check_args($opt, $set);
if ($set['help']) {
	help();
} else if ($set['k']) {
	count_keywords($input);
	print_data($data, $output);
} else if ($set['o']) {
	count_operators($input);
	print_data($data, $output);
} else if ($set['i']) {
	count_identifiers($input);
	print_data($data, $output);
} else if ($set['w']) {
	$pattern = $opt['w'];
	count_strings($input);
	print_data($data, $output);
} else if ($set['c']) {
	count_comments($input);
	print_data($data, $input);
}
?>