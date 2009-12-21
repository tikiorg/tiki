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
$data = preg_replace('/\n(.*)(PRIMARY KEY )auto_increment(,)?\n/', "\n$1$2 AUTOINCREMENT$3\n", $data);
$data = preg_replace('/\n(.*)(!PRIMARY KEY )auto_increment(,)?\n/', "\n$1$3\n", $data);

$data = preg_replace('/\n([ \t]*)KEY [a-zA-Z0-9`]* \([a-zA-Z0-9`]*, [a-zA-Z0-9`]*\)(,)?\n/', "\n$1$2\n", $data);

// save file
file_put_contents($tikiversion.'.to_sqlite.sql', $data);

?>