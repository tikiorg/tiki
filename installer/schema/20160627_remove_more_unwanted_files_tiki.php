<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20150508_perspectives_style_to_theme_tiki.php 57954 2016-03-17 19:34:29Z jyhem $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 */
function upgrade_20160627_remove_more_unwanted_files_tiki($installer)
{

	$files = array(
		'vendor/jquery/jtrack/demo.html',
		'vendor_extra/elfinder/elfinder.html',
		'vendor/svg-edit/svg-edit/embedapi.html',
		'vendor/svg-edit/svg-edit/extensions/imagelib/index.html',
		'vendor/svg-edit/svg-edit/browser-not-supported.html',
	);

	$folders = array(
		'vendor/zendframework/zend-json/doc',
		'vendor/symfony/dependency-injection/Symfony/Component/DependencyInjection/Tests',
		'vendor/symfony/console/Symfony/Component/Console/Tests',
		'vendor_extra/pear/XML_Parser/examples',
		'vendor_extra/pear/XML_Parser/tests',
		'vendor_extra/elfinder/files',
	);

	foreach ($files as $file) {
		if (is_writable($file)) {
			unlink($file);
		}
	}

	foreach ($folders as $folder) {
		if (is_writable($folder)) {
			delTree($folder);
		}
	}

}

function delTree($dir)
{
	$files = array_diff(scandir($dir), array('.', '..'));
	foreach ($files as $file) {
		$path = "$dir/$file";
		if ((is_dir($path) && !is_link($dir))) {
			delTree($path);
		} else {
			unlink($path);
		}
	}
	return rmdir($dir);
}
