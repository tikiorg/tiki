#!/usr/bin/php
<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: prefsdoc.php 60335 2016-11-20 19:23:21Z drsassafras $


// can only be run from the command line
if (php_sapi_name() !== 'cli') die;


require_once ($baseDir.'tiki-setup.php');

/**
 * This file is used for automatically generating prefrence documentaion
 * for use on doc.tiki.org. It takes the currently installed prefrences
 * and generates files in wiki-syntax to be pasted into doc.tiki.org
 *
 * Running this will overwrite any previously generated pref doc files
 *
 *
 * limitations
 *
 * Wiki Syntax is not supported in documentation, but it is on doc.tiki.org
 * Currently there is no escaping of wiki syntax when files are generated.
 *
 * Currently a single file is generated for each prefrence page, however the
 * documentaion on doc.tiki.org is split into tabs.
 *
 */


$baseDir = realpath(__DIR__ .'/../../../').'/';

@mkdir ($baseDir.'storage/prefsdoc');			// create subdir for housing generated files, if it does not exist
$PrefVars = array();
$fileCount=0;

$docFiles = scandir($baseDir.'lib/prefs'); // grab all the files that house prefs
foreach ($docFiles as $fileName) {
	if ($fileName !== 'index.php' && substr($fileName, -4) === '.php') {  // filter out any file thats not a pref file
		require ($baseDir.'lib/prefs/'.$fileName);
		$callVar = 'prefs_'.substr($fileName,0,-4).'_list';
		$PrefVars = array_merge($PrefVars,$callVar());						// create one big var with all the pref info
	}
}
$prefCount=0;

$TWV = new TWVersion();

$docFiles = scandir($baseDir.'templates/admin'); // grab all the files that house prefs
foreach ($docFiles as $fileName) {
	if (substr($fileName, 0,8) === 'include_') {  // filter out any file thats not a pref file
		$file = file_get_contents($baseDir.'templates/admin/' . $fileName);
		$file = str_replace(array('\'', '"'), '', $file);
		preg_match_all('{preference name=(\w*)(.*)}', $file, $out, PREG_PATTERN_ORDER);  // return array of every pref name in file
		$writeFile = '~hc~ -- Table generated with prefsdoc.php for tiki '.$TWV->getVersion()." -- ~/hc~\n";
		$writeFile .= '{FANCYTABLE(head="Option | Description | Default" sortable="n")}'."\n";
		foreach ($out[1] as $param) {
			if ($PrefVars[$param]['default'] == 'n') {
				$PrefVars[$param]['default'] = 'Disabled';
			} else if ($PrefVars[$param]['default'] == 'y') {
				$PrefVars[$param]['default'] = 'Enabled';			// Change default codes to human readable format
			} else if (is_array($PrefVars[$param]['default'])){
				$PrefVars[$param]['default'] = implode(', ',$PrefVars[$param]['default']);
			}

			$writeFile .= $PrefVars[$param]['name'] . '~|~';
			$writeFile .= $PrefVars[$param]['description'];
			foreach ($PrefVars[$param]['tags'] as $tag)
				if ($tag === 'experimental')
					$writeFile .= ' (experimental)';
			$writeFile .= '~|~';
			$writeFile .= (string)$PrefVars[$param]['default'] . "\n";
			$prefCount++;
		}
		$writeFile .= "{FANCYTABLE}\n/////\n";
		$fileCount++;
		$docName = substr(substr($fileName,8),0,-4).".txt";				// Name of file to be written
		@unlink($baseDir.'storage/prefsdoc/'.$docName);
		file_put_contents($baseDir.'storage/prefsdoc/'.$docName, $writeFile);		// write one file for each pref page on control panel
	}
}


echo "\033[33m".$fileCount. ' pref files written to storage/prefsdoc/ with a total of '.$prefCount." prefs.\033[0m\n";
