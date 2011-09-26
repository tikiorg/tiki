<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 *  A problem with having to maintain translations of several versions of Tiki is
 * that the same strings have the same translations, but is not desirable to change
 * them for each branch. This utility takes the translations from a source version
 * and add to a target version if the string exists in the targe language files. 
 */

if ($argc < 3) {
	$helpMsg = "\nUsage: php doc/devtools/mergelang.php pathToSourceTikiVersion pathToTargetTikiVersion [lang=pt-br,es]\n";
	$helpMsg .= "\nExamples: \n\t\tphp doc/devtools/mergelang.php /home/user/devel/tiki80 /home/user/deve/tiki60";
	$helpMsg .= "\n\t\tphp doc/devtools/mergelang.php /home/user/devel/tiki80 /home/user/deve/tiki60 lang=pt-br,es\n\n"; 
	die($helpMsg);
}

require_once('lib/language/Language.php');

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
	
	if (file_exists($sourceLangFile)) {
		$lines = file($sourceLangFile);
		$sourceTranslations = array(); 
		
		foreach ($lines as $line) {
			$matches = array();
			
			// build an array with all the translations from the source file
			if (preg_match('/^\s*\"(.*)\"\s*\=\>\s*\"(.*)\"\s*\,\s*$/', $line, $matches)) {
				$sourceTranslations[$matches[1]] = $matches[2];
			}
		}
	}
	
	if (file_exists($targetLangFile)) {
		$lines = file($targetLangFile);
		$handle = fopen("$targetLangFile.tmp", 'w');
		
		if ($handle) {
			// foreach each line in the target file check decide whether to keep the
			// current translation or use the translation from the source file if one exists 
			foreach ($lines as $line) {
				$matches = array();
				
				if (preg_match('|^/?/?\s*\"(.*)\"\s*\=\>\s*\"(.*)\"\s*\,\s*$|', $line, $matches)
					&& isset($sourceTranslations[$matches[1]]))
				{
					fwrite($handle, "\"{$matches[1]}\" => \"{$sourceTranslations[$matches[1]]}\",\n");
				} else {
					fwrite($handle, $line);
				}
			}
			
			rename($tmpTargetLangFile, $targetLangFile);
		}
	}
}