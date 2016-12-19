<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * A problem with having to maintain translations of several versions of Tiki is
 * that the same strings have the same translations, but is not desirable to change
 * them for each branch. This utility takes the translations from a source version
 * and add to a target version if the string exists in the target language files. 
 */

if ($argc < 3) {
	$helpMsg = "\nUsage: php doc/devtools/mergelang.php pathToSourceTikiVersion pathToTargetTikiVersion [lang=pt-br,es]\n";
	$helpMsg .= "\nExamples: \n\t\tphp doc/devtools/mergelang.php /home/user/devel/tiki80 /home/user/deve/tiki60";
	$helpMsg .= "\n\t\tphp doc/devtools/mergelang.php /home/user/devel/tiki80 /home/user/deve/tiki60 lang=pt-br,es\n\n"; 
	die($helpMsg);
}

require_once('lib/language/Language.php');
require_once('lib/language/File.php');
require_once('lib/language/MergeFiles.php');

$sourcePath = $argv[1];
$targetPath = $argv[2];

if (isset($argv[3])) {
	list($key, $value) = explode('=', $argv[3]);
	$languages = explode(',', $value);
} else {
	$languages = Language::getLanguages();
}

if (!file_exists($sourcePath)) {
	die("\nPath $sourcePath does not exist.\n\n");
}

if (!file_exists($targetPath)) {
	die("\nPath $targetPath does not exist.\n\n");
}

foreach ($languages as $language) {
	$sourceLangFile = "$sourcePath/lang/$language/language.php";
	$targetLangFile = "$targetPath/lang/$language/language.php";
	$tmpTargetLangFile = "$targetPath/lang/$language/language.php.tmp";

	try {
		$sourceObj = new Language_File($sourceLangFile);	
		$targetObj = new Language_File($targetLangFile);
		
		$mergeFiles = new Language_MergeFiles($sourceObj, $targetObj);
		$mergeFiles->merge();
	} catch (Language_Exception $e) {
		echo "Warning: " . $e->getMessage() . "\n";
	}
}
