<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 * @return void|bool
 */
function upgrade_20170127_remove_templates_c_tiki($installer)
{
	$dir_handle = false;
	$dirname = 'templates_c';
	if (is_dir($dirname)) {
		$dir_handle = opendir($dirname);
	}
	if (! $dir_handle) {
		return false;
	}
	while ($file = readdir($dir_handle)) {
		if ($file != "." && $file != "..") {
			if (! is_dir($dirname . "/" . $file)) {
				unlink($dirname . "/" . $file);
			} else {
				delete_directory($dirname . '/' . $file);
			}
		}
	}
	closedir($dir_handle);
	rmdir($dirname);
}
