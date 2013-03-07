<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     iconify
 * Purpose:  Returns a filetype icon if the filetype is known and there's an icon in img/icons/mime. Returns a default file type icon in any other case
 * -------------------------------------------------------------
 */

function smarty_modifier_iconify($string, $filetype = null)
{
	global $smarty;

	$smarty->loadPlugin('smarty_function_icon');
	$icon = '';
	$ext = strtolower(substr($string, strrpos($string, '.') + 1));
	if (file_exists("img/icons/mime/$ext.png")) {
		$icon = $ext;
	} elseif (file_exists('img/icons/mime/' . substr($ext, 0, 3) . '.png')) {
		$icon = substr($ext, 0, 3);
	} else {
		include_once ('lib/mime/mimetypes.php');
		global $mimetypes;

		$mimes = array_keys($mimetypes, $filetype);
		foreach ($mimes as $m) {
			if (file_exists("img/icons/mime/$m.png")) {
				$icon = $m;
			}
		}
		if (empty($icon)) {
			$icon = 'default';
		}
	}

	return smarty_function_icon(
		array(
			'_id' => 'img/icons/mime/'.$icon.'.png',
			'alt' => ( $filetype === null ? $icon : $filetype ),
			'class' => ''
		),
		$smarty
	);
}
