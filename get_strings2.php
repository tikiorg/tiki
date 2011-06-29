<?php

require_once('lib/language/CollectFiles.php');
require_once('lib/language/FileType.php');
require_once('lib/language/FileType/Php.php');
require_once('lib/language/FileType/Tpl.php');
require_once('lib/language/GetStrings.php');
require_once('lib/language/WriteFile.php');

$getStrings = new Language_GetStrings(new Language_CollectFiles, new Language_WriteFile);

if (isset($_GET['lang']) && !empty($_GET['lang'])) {
	$lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
	$getStrings->setLanguages($lang);
}

$getStrings->addFileType(new Language_FileType_Php);
$getStrings->addFileType(new Language_FileType_Tpl);

// skip the following directories 
$getStrings->collectFiles->setExcludeDirs(array(
	'./dump' , './img', './lang', './lib/adodb', './lib/ckeditor',
	'./lib/codemirror', './lib/core/Zend', './lib/ezcomponents',
	'./lib/htmlpurifier', './lib/jquery', './lib/jscalendar', './lib/pclzip',
	'./lib/pear', './lib/phpcas', './lib/smarty', './lib/test',	'./temp',
	'./temp/cache',	'./templates_c'
));

$getStrings->collectFiles->setIncludeFiles(array(
	'./lang/langmapping.php', './img/flags/flagnames.php'
));

$getStrings->run();

