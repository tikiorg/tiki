<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Purpose:  Returns a filetype icon or file type
 * -------------------------------------------------------------
 *
 * @param $string               File name with extension
 * @param null $filetype        File type
 * @param null $fileId          File id when using file galleries
 * @param int $size             Icon size
 * @param string $return        icon or filtype
 * @return null|string
 * @throws Exception
 * @throws SmartyException
 */
function smarty_modifier_iconify($string, $filetype = null, $fileId = null, $size = 1, $return = 'icon')
{
	$smarty = TikiLib::lib('smarty');
	global $prefs;

	$smarty->loadPlugin('smarty_function_icon');
	$icon = '';
	$ext = strtolower(substr($string, strrpos($string, '.') + 1));
	if ($fileId && substr($filetype, 0, 6) == 'image/') {
		// Special handling for file gallery images,
		// display thumbnail
		$smarty->loadPlugin('smarty_modifier_sefurl');
		$smarty->loadPlugin('smarty_modifier_escape');
		$icon = smarty_modifier_sefurl($fileId, 'thumbnail');
		$icon = smarty_modifier_escape($icon);

		return "<img src=\"$icon\" width=\"16\"/>";
	} else {
		include_once ('lib/mime/mimetypes.php');
		global $mimetypes;
		$mimes = array_keys($mimetypes, $filetype);
		if ($prefs['theme_iconset'] === 'legacy') {
			if (file_exists("img/icons/mime/$ext.png")) {
				$icon = $ext;
			} elseif (file_exists('img/icons/mime/' . substr($ext, 0, 3) . '.png')) {
				$icon = substr($ext, 0, 3);
			} else {
				foreach ($mimes as $m) {
					if (file_exists("img/icons/mime/$m.png")) {
						$icon = $m;
					}
				}
				if (empty($icon)) {
					$icon = 'default';
				}
			}
			if ($return === 'filetype') {
				return $m;
			} elseif ($return === 'icon') {
				return smarty_function_icon(
					array(
						'_id' => 'img/icons/mime/'.$icon.'.png',
						'alt' => ( $filetype === null ? $icon : $filetype ),
						'class' => '',
						'size' => $size
					),
					$smarty
				);
			}
		//iconsets introduced with Tiki14
		} else {
			if (!empty($filetype)) {
				$type = $filetype;
			} elseif (!empty($mimetypes[$ext])) {
				$type = $mimetypes[$ext];
			} else {
				$type = 'file';
			}
			switch ($type) {
				case $type === 'application/msword'
					|| strpos($type, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0:
					$iconname = 'word';
					break;
				case $type === 'application/pdf':
					$iconname = 'pdf';
					break;
				case $type === 'application/vnd.ms-excel'
					|| $type === 'application/ms-excel'
					|| $type === 'application/msexcel'
					|| strpos($type, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0:
					$iconname = 'excel';
					break;
				case $type === 'application/vnd.ms-powerpoint'
					|| $type === 'application/ms-powerpoint'
					|| $type === 'application/mspowerpoint'
					|| strpos($type, 'application/vnd.openxmlformats-officedocument.presentationml') === 0:
					$iconname = 'powerpoint';
					break;
				case strpos($type,'audio/') === 0:
					$iconname = 'audio';
					break;
				case strpos($type,'image/') === 0:
					$iconname = 'image';
					break;
				case strpos($type,'text/') === 0:
					$iconname = 'textfile';
					break;
				case strpos($type,'video/') === 0:
					$iconname = 'video_file';
					break;
				case strpos($type,'application/') === 0:
					$iconname = 'code_file';
					break;
				default:
					$iconname = 'file';
					break;
			}
			if ($return === 'filetype') {
				return $type;
			} else  {
				return smarty_function_icon(['name' => $iconname, 'size' => $size], $smarty);
			}

		}
	}
}
