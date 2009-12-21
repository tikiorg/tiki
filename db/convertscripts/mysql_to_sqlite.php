<?php

// Set tikiversion variable
require 'tikiversion.php';
if(!isset($_GET['version'])) {
	echo "version not given. Using default $tikiversion.<br />";
} else {
	if (preg_match('/\d\.\d/',$_GET['version'])) {
		$tikiversion=$_GET['version'];
	}
}


$data = file_get_contents('../tiki.sql');
echo "<br />\n";

// remove ENGINE
$data = preg_replace('/ENGINE=[a-zA-Z0-9]*/', '', $data);

// remove table AUTO_INCREMENT
$data = preg_replace('/AUTO_INCREMENT=[0-9]+/', '', $data);

// remove column auto_increment
$data = preg_replace('/(,)?\n(.*)auto_increment(,)?\n/', "\n$1$2$3\n", $data);
//$data = preg_replace('/\n(.*)(PRIMARY KEY )auto_increment(,)?\n/', "\n$1$2 AUTOINCREMENT$3\n", $data);
//$data = preg_replace('/\n(.*)(?!PRIMARY KEY )auto_increment(,)?\n/', "\n$1$3\n", $data);

// remove table KEYs
$data = preg_replace('/(,)?\n([ \t]*)(FULLTEXT )?KEY [a-zA-Z0-9`]* \([a-zA-Z0-9`\(\), ]*\),?/', "\n$2\n", $data);

// remove column types unsigned
$data = preg_replace('/(,)?\n(.*)unsigned(.)*(,)?\n/', "$1\n$2$3$4\n", $data);

$data = preg_replace('/,([\n \t]*)\)(.*);/', '$1)$2;', $data);




//$data = preg_replace('/,([\n \t]*)\),/', '', $data);
//$data = preg_replace('/\n([ \t]*)\),([ \t]*)\)\n/', '', $data);

//$data = preg_replace('/\n[ \t]*\n/', '', $data);
//$data = preg_replace('/\n/', '', $data);
//$data = preg_replace('/(CREATE TABLE [`a-zA-Z0-9]* )\((.*)\)(.*);/', '$1$2$3', $data);

// save file
file_put_contents($tikiversion.'.to_sqlite.sql', $data);

?>