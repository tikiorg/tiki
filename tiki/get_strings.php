<?php

// $Header: /cvsroot/tikiwiki/tiki/get_strings.php,v 1.22 2003-08-07 04:33:56 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/** \file
 * $Header: /cvsroot/tikiwiki/tiki/get_strings.php
 * \brief Update the language.php files
 * \param lang the abbrevaitaion of the language - if no parameter all the languages are processed
 */
/// known bug: keeps collecting "\n for rows" and "...snippet of code.\n..."
require_once ('tiki-setup.php');

if ($tiki_p_admin != 'y') {
	die ("You need to be admin to run this script");
}

///Get the languages where the language.php has to be updated
$languages = array();

if (isset($_REQUEST["lang"])) {
	$languages[] = $_REQUEST["lang"];
} else {
	$languages = array(
		'cn',
		'de',
		'da',
		'en',
		'fr',
		'he',
		'it',
		'nl',
		'no',
		'pl',
		'ru',
		'sp',
		'sw',
		'tw'
	);
}

print ("Languages:");

foreach ($languages as $sel) {
	print ($sel);
}

print ("</br>");

$files = array();

/// Get the tpl files: the filenames end with tpl and the files are in the directory templates/ and tempaltes/*/
$dirs = array('templates/');
chdir ('templates/'); // see bug on is_dir on php.net
$handle = opendir('.');

while ($file = readdir($handle)) {
	if (is_dir($file) && $file != "." && $file != "..")
		$dirs[] = "templates/$file/";
}

chdir ("..");

foreach ($dirs as $dir) {
	$handle = opendir($dir);

	while ($file = readdir($handle)) {
		if ($file == '.' || $file == '..')
			continue;

		if (substr($file, strlen($file) - 3, 3) == "tpl") {
			print ("File: $dir$file<br/>");

			$files[] = $dir . $file;
		}
	}

	closedir ($handle);
}

/// get the php files: the filenames end with php and the files are located in the list of directories ./, modules/; lib/, lib/*/, Smarty/plugins/
$dirs = array(
	'./',
	'modules/',
	'lib/',
	'Smarty/plugins/'
);

chdir ('lib/');
$handle = opendir('.');

while ($file = readdir($handle)) {
	if (is_dir($file) && $file != "." && $file != "..")
		$dirs[] = "lib/$file/";
}

chdir ("..");

foreach ($dirs as $dir) {
	$handle = opendir($dir);

	while ($file = readdir($handle)) {
		if ($file == '.' || $file == '..')
			continue;

		if (substr($file, strlen($file) - 3, 3) == "php") {
			print ("File: $dir$file<br/>");

			$files[] = $dir . $file;
		}
	}

	closedir ($handle);
}

foreach ($languages as $sel) {
	unset ($lang);

	unset ($used);
	include ("lang/$sel/language.php");
	$nbTrads = count($lang);
	$fw = fopen("lang/$sel/new_language.php", 'w+');
	print ("&lt;?php\n<br/>\$lang=Array(\n<br/>");
	// the generated comment coding:utf-8 is for the benefit of emacs
	// The comment must be on the very first line in the file.
	fwrite($fw, "<?php // -*- coding:utf-8 -*-\n\$lang=Array(\n");

	foreach ($files as $file) {
		$fp = fopen($file, "r");

		$data = fread($fp, filesize($file));
		fclose ($fp);

		if (substr($file, strlen($file) - 3, 3) == "tpl") {
			preg_match_all("/(?s)\{tr\}(.+?)\{\/tr\}/", $data, $words);

			foreach (array_unique($words[1])as $word) {
				if (ereg("^\{[$][^\}]*\}$", $word))
					continue;

				if (!isset($used[$word]))
					$used[$word] = 1;

				if (isset($lang[$word])) {
					print ('"' . $word . '" => "' . $lang[$word] . '",' . "\n<br/>");
				//fwrite($fw,'"'.$word.'" => "'.$lang[$word].'",'."\n");
				} else {
					print ('+++"' . $word . '" => "' . $word . '",' . "\n<br/>");

					$lang[$word] = $word;
				//fwrite($fw,'"'.$word.'" => "'.$word.'",'."\n");
				}
			}
		}

		/// (?s) for multiline
		/// known bug: collects also the function xxxtra("xx") - needs only to collect hawtra("xxx") and tra("xxx")
		preg_match_all("/(?s)tra[ \t]*\( *\"([^\"]+)\"[ \t]*\)/", $data, $words);

		foreach (array_unique($words[1])as $word) {
			if (!isset($used[$word]))
				$used[$word] = 1;

			if (isset($lang[$word])) {
				print ('<b>"' . $word . '" => "' . $lang[$word] . '",' . "</b>\n<br/>");
			//fwrite($fw,'"'.$word.'" => "'.$lang[$word].'",'."\n");
			} else {
				print ('<b>+++"' . $word . '" => "' . $word . '",' . "</b>\n<br/>");

				$lang[$word] = $word;
			//fwrite($fw,'"'.$word.'" => "'.$word.'",'."\n");
			}
		}

		preg_match_all("/(?s)tra[ \t]*\( *\'([^\']+)\'[ \t]*\)/", $data, $words);

		foreach (array_unique($words[1])as $word) {
			if (!isset($used[$word]))
				$used[$word] = 1;

			if (isset($lang[$word])) {
				print ('<b>"' . $word . '" => "' . $lang[$word] . '",' . "</b>\n<br/>");
			} else {
				print ('<b>+++"' . $word . '" => "' . $word . '",' . "</b>\n<br/>");

				$lang[$word] = $word;
			}
		}
	}

	print ('"' . '##end###' . '" => "' . '###end###' . '"' . ");?&gt;\n<br/>");
	$nb = 0;

	foreach ($lang as $key => $val) {
		if ($key == '##end###' && $val == '###end###')
			continue;

		// backslash $ and \n
		fwrite($fw, '"' . str_replace("\$",
			"\\\$", str_replace("\n", "\\n", $key)). '" => "' . str_replace("\$", "\\\$", str_replace("\n", "\\n", $val)). '",');

		if (++$nb == $nbTrads)
			fwrite($fw, "//##First new line");

		if (isset($used[$key]))
			fwrite($fw, "\n");
		else
			fwrite($fw, "//perhaps not used\n");
	}

	fwrite($fw, '"' . '##end###' . '" => "' . '###end###' . '");' . "\n" . '?>' . "\n");
	fclose ($fw);
	@unlink ("lang/$sel/old.php");
	@rename("lang/$sel/language.php", "lang/$sel/old.php");
	rename("lang/$sel/new_language.php", "lang/$sel/language.php");
}

?>