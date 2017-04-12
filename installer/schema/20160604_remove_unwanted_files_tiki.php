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
function upgrade_20160604_remove_unwanted_files_tiki($installer)
{

	$files = array(
		'vendo/player/mp3/template_default/compileTemplateDefault.bat',
		'vendor/player/mp3/template_default/compileTemplateDefault.sh',
		'vendor/player/mp3/template_default/TemplateDefault.as',
		'vendor/player/mp3/template_default/test.mp3',
		'vendor/player/flv/flv_stream.php',
		'vendor/player/flv/template_default/compileTemplateDefault.bat',
		'vendor/player/flv/template_default/compileTemplateDefault.sh',
		'vendor/player/flv/template_default/rorobong.jpg',
		'vendor/player/flv/template_default/TemplateDefault.as',
		'vendor/jcapture-applet/jcapture-applet/applet.php',
	);

	foreach ($files as $file) {
		if (is_writable($file)) {
			unlink($file);
		}
	}

}

