<?php
/**
 * Check the file gallery directory for consistancy. 
 * 
 * This implies
 * <ol>
 *   <li>Prereq: File Gallery uses a directory to store the files</li>
 *   <li>All rows in the tiki_files tables have a corresponding file in the directory</li>
 *   <li>All files in the directory have a corresponding row in the tiki_files table</li>
 * </ol>
 *
 * @package Tikiwiki\doc
 * @subpackage devtools
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

require_once('tiki-setup.php');
$tikilib = TikiLib::lib('tiki');

// Make sure script is run from a shell
if (PHP_SAPI !== 'cli') {
	die("Please run from a shell");
}


// Show a description of this tool
echo "check_filegal_dir: consistancy check for the file galley\n";
echo "The files must be stored in a directory\n";
echo "---------------------------------------\n";

// Verify the a directory is used to store the file gallery files
if ($prefs['fgal_use_db'] == 'y') {
	echo "Files are stored in the database\n";
	exit;
}
$fg_dir = $prefs['fgal_use_dir'];
if (is_dir($fg_dir) == false) {
	echo "Files directory: ".$fg_dir." does not exist\n";
	exit;
}
echo "Files directory: ".$fg_dir."\n";

// Load db files
$table = $tikilib->table('tiki_files');
$result = $table->fetchAll();
$db_files = array();
foreach ($result as $r) {
	$db_files[] = $r['path'];
}
echo "Found ".count($db_files)." files in DB\n";

// Load directory files
$dir_files = array();
$dir = dir($fg_dir);
while (($file = $dir->read()) !== false) {
	if (is_dir($file) == false) {
		$dir_files[] = $file;
	}
}
$dir->close();
echo "Found ".count($dir_files)." files in directory\n";


// Verify that all tiki_files rows point to an existing file in the directory
$dir_errors = 0;
foreach ($db_files as $file) {
	if (in_array($file, $dir_files) == false) {
		echo "File missing in directory: ".$file."\n";
		$dir_errors++;
	}
}
if ($dir_errors == 0) {
	echo "All db files found in directory";
}
echo "\n";

// Verify that all files in the directory have a row in tiki_files
$db_errors = 0;
foreach ($dir_files as $file) {
	if (in_array($file, $db_files) == false) {
		echo "File missing in database: ".$file."\n";
		$db_errors++;
	}
}
if ($db_errors == 0) {
	echo "All directory files found in database";
}
echo "\n";
