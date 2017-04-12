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
		'vendor/player/mp3/template_default/compileTemplateDefault.bat',
		'vendor/blueimp/javascript-load-image/js/demo.js',
		'vendor/blueimp/javascript-load-image/css/demo.css',
		'vendor/Sam152/Javascript-Equal-Height-Responsive-Rows/demo.html',
		'vendor/jquery/jtrack/demo.html',
		'vendor_extra/elfinder/elfinder.html',
		'vendor/blueimp/jquery-file-upload/css/demo-ie8.css',
		'vendor/blueimp/jquery-file-upload/css/demo.css',
		'vendor/blueimp/jquery-file-upload/angularjs.html',
		'vendor/blueimp/jquery-file-upload/basic.html',
		'vendor/blueimp/jquery-file-upload/basic-plus.html',
		'vendor/blueimp/jquery-file-upload/index.html',
		'vendor/blueimp/jquery-file-upload/jquery-ui.html',
		'vendor/svg-edit/svg-edit/embedapi.html',
		'vendor/svg-edit/svg-edit/extensions/imagelib/index.html',
		'vendor/svg-edit/svg-edit/browser-not-supported.html',
	);

	$folders = array(
		'vendor/codemirror/codemirror/doc',
		'vendor/phpcas/phpcas/CAS-1.3.3/docs',
		'vendor/zendframework/zend-json/doc',
		'vendor/fortawesome/font-awesome/src/_includes/examples',
		'vendor/fortawesome/font-awesome/src/3.2.1/examples',
		'vendor/tijsverkoyen/css-to-inline-styles/TijsVerkoyen/CssToInlineStyles/tests/examples',
		'vendor/phpcas/phpcas/CAS-1.3.3/docs/examples',
		'vendor/fortawesome/font-awesome/src/_includes/tests',
		'vendor/tijsverkoyen/css-to-inline-styles/TijsVerkoyen/CssToInlineStyles/tests',
		'vendor/twitter/bootstrap/js/tests',
		'vendor/symfony/dependency-injection/Symfony/Component/DependencyInjection/Tests',
		'vendor/symfony/console/Symfony/Component/Console/Tests',
		'vendor/symfony/config/Symfony/Component/Config/Tests',
		'vendor/symfony/filesystem/Tests',
		'vendor_extra/pear/XML_Parser/examples',
		'vendor_extra/pear/XML_Parser/tests',
		'vendor_extra/elfinder/files',
		'vendor/blueimp/jquery-file-upload/cors',
		'vendor/blueimp/jquery-file-upload/server',
		'vendor/phpcas/phpcas/CAS-1.3.3/docs',
		'vendor/jquery/plugins/jquery-json/test',
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
