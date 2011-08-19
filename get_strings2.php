<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Update language.php files
 * 
 * Examples:
 * 		- get_strings.php -> update all language.php files
 * 		- get_strings.php?lang=fr -> update just lang/fr/language.php file
 * 		- get_strings.php?lang[]=fr&lang[]=pt-br&outputFiles -> update both French
 * 		  and Brazilian Portuguese language.php files and for each string add a line with
 * 		  the file where it was found.
 */

if (php_sapi_name() != 'cli') {
	require_once('tiki-setup.php');
	$access->check_permission('tiki_p_admin');
}

require_once('lib/language/CollectFiles.php');
require_once('lib/language/FileType.php');
require_once('lib/language/FileType/Php.php');
require_once('lib/language/FileType/Tpl.php');
require_once('lib/language/GetStrings.php');
require_once('lib/language/WriteFile.php');

require_once('lib/setup/timer.class.php');

$timer = new timer();
$timer->start();

$options = array();

if (isset($_GET['lang']) && !empty($_GET['lang'])) {
	$options['lang'] = $_GET['lang'];
}

if (isset($_GET['outputFiles'])) {
	$options['outputFiles'] = true;
}

$getStrings = new Language_GetStrings(new Language_CollectFiles, new Language_WriteFile, $options);

$getStrings->addFileType(new Language_FileType_Php);
$getStrings->addFileType(new Language_FileType_Tpl);

// skip the following directories 
$getStrings->collectFiles->setExcludeDirs(array(
	'dump' , 'img', 'lang', 'lib/adodb', 'lib/ckeditor',
	'lib/codemirror', 'lib/core/Zend', 'lib/ezcomponents', 'lib/html5shim', 
	'lib/htmlpurifier', 'lib/jquery', 'lib/jquery.s5', 'lib/jquery.sheet', 'lib/jscalendar', 'lib/mobileesp', 'lib/pclzip',
	'lib/pear', 'lib/phpcas', 'lib/smarty', 'lib/svg-edit', 'lib/test',	'temp',
	'temp/cache',	'templates_c'
));

// manually add the following files from skipped directories
$getStrings->collectFiles->setIncludeFiles(array(
	'./lang/langmapping.php', './img/flags/flagnames.php'
));

echo formatOutput("Languages: " . implode(' ', $getStrings->getLanguages()) . "\n");

$getStrings->run();

echo formatOutput("\nTotal time spent: " . $timer->stop() . " seconds\n");

function formatOutput($string)
{
	if (php_sapi_name() == 'cli') {
		return $string;
	} else {
		return nl2br($string);
	}
}
