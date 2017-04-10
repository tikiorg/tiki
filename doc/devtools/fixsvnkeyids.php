<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


require_once('../../tiki-setup_base.php');
require_once('svntools.php');

// Make sure script is run from a shell
if (PHP_SAPI !== 'cli')
	die("Please run from a shell");


/**
 *
 * This file may be run to fix svn keyword ids for all files within tiki.
 *
 * Reads the beginning of each file in tiki, and adds a svn Keyword id if the marker in file is found.
 *
 */

info("Updating SVN keyword Id's. This will take a minute...");
// apply filter only to these file types
foreach (glob_recursive('*.{php,tpl,sh,sql,js,less}', GLOB_BRACE, '', 'vendor_') as $fileName) {
	$handle = fopen( $fileName, "r");
	$count = 1;
	do {
		$buffer = fgets($handle);

		if (preg_match('/(\/\/ |{\* |\# | \* )\$Id.*\$/', $buffer)){ // match several different comment styles
			shell_exec('svn propset svn:keywords "Id" ' . escapeshellarg($fileName));
			break;
		}
		$count++;
	} while ($count < 11 && $buffer); // search through up to 11 lines of code (no results expanding that)
	fclose($handle);
}

info("Keywords updated, you may now review and commit.");



/**
 *
 * Recursively calls, glob()
 *
 * @param string $pattern
 * @param int    $flags
 * @param string $startdir
 * @param $exclude string|bool if this string is found withn a directory name, it wont be included
 *
 * @return array
 */

function glob_recursive($pattern, $flags = 0, $startdir = '', $exclude= false){
	$files = glob($startdir.$pattern, $flags);
	foreach (glob($startdir.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK) as $dir){

		if (strpos($dir,$exclude) === false) {
			$files = array_merge($files, glob_recursive($pattern, $flags, $dir, $exclude));
		}
	}
	return $files;
}
