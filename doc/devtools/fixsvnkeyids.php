<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


require_once('../../tiki-setup_base.php');
require_once('svntools.php');

// Make sure script is run from a shell
if (PHP_SAPI !== 'cli') {
	die("Please run from a shell");
}


/**
 *
 * This file may be run to fix the Id's of SVN Keyword for all files within tiki.
 *
 * Reads the beginning of each file in tiki, and adds a svn Keyword id if the $Id$ marker is found.
 *
 */

$xml = new DOMDocument;
$xml->loadXML(shell_exec('svn propget -R svn:keywords --xml'));

// find the offset lengh of the base pathname
$pathLen = strlen(realpath(dirname(__FILE__) . '/../..')) + 1;

$Ids = [];

foreach ($xml->getElementsByTagName('target') as $target) {
	foreach ($target->getElementsByTagName('property') as $isKey) {
		if ($isKey->getAttribute('name') === 'svn:keywords') {
			$Ids[substr($target->getAttribute('path'), $pathLen)] = $isKey->textContent;
		}
	}
}
$matches = 0;
// apply filter only to these file types, excluding any vendor files.
foreach (glob_recursive('*{.php,.tpl,.sh,.sql,.js,.less,.css,.yml,htaccess}', GLOB_BRACE, '', 'vendor_') as $fileName) {
	// if there was no keywords defined in SVN or there is no Id defined in those keywords
	if (! isset($Ids[$fileName]) || ! preg_match('/(^I|\nI)(d$|d\n)/', $Ids[$fileName])) {
		$handle = fopen($fileName, "r");
		$count = 1;
		do {
			$buffer = fgets($handle);
			if (preg_match('/(\/\/ |{\* |\# |\* )\$Id.*\$/', $buffer)) { // match several different comment styles
				$keys = '';
				if (! empty($Ids[$fileName])) {    // if there is preexisting keys, then set them.
					$keys = $Ids[$fileName] . "\n";
				}
				$keys .= "Id";
				shell_exec("svn propset svn:keywords \"$keys\" " . escapeshellarg($fileName));
				$matches++;
				break;
			}
			$count++;
		} while ($count < 13 && $buffer); // search through up to 13 lines of code (no results increasing that)
		fclose($handle);
	}
}

if ($matches) {
	info(color("$matches keywords updated, you may now review and commit.", 'yellow'));
} else {
	info(color("All keywords were up to date, no changes made.", 'yellow'));
}


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

function glob_recursive($pattern, $flags = 0, $startdir = '', $exclude = false)
{
	$files = glob($startdir . $pattern, $flags);
	foreach (glob($startdir . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK) as $dir) {
		if (strpos($dir, $exclude) === false) {
			$files = array_merge($files, glob_recursive($pattern, $flags, $dir, $exclude));
		}
	}
	return $files;
}
