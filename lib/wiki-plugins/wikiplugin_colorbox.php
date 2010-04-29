<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_colorbox_info() {
	return array(
		'name' => tra('colorbox'),
		'documentation' => 'PluginClorBox',
		'description' => tra("Display all the images of a file gallery in a colorbox popup"),
		'prefs' => array( 'feature_file_galleries', 'feature_shadowbox', 'wikiplugin_colorbox' ),
		'params' => array(
			'fgalId' => array(
				'required' => false,
				'name' => tra('File gallery ID'),
				'description' => tra('File gallery ID'),
				'filter' => 'digits'
			),
			'galId' => array(
				'required' => false,
				'name' => tra('Image gallery ID'),
				'description' => tra('Image gallery ID'),
				'filter' => 'digits'
			),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumb'),
				'description' => tra('The image in the page is displayed or not in mode thumb:'). 'y|n',
				'filter' => 'alpha'
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort Mode'),
				'filter' => 'word'
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show file title'),
				'description' => 'y|n '. tra('Show file title'),
				'filter' => 'alpha',
				'default' => 'n',
			),
			'showfilename' => array(
				'required' => false,
				'name' => tra('Show file name'),
				'description' => 'y|n '. tra('Show file name'),
				'filter' => 'alpha',
				'default' => 'n',
			),
		),
	);
}
function wikiplugin_colorbox($data, $params) {
	global $tikilib, $smarty, $user, $prefs;
	static $iColorbox;
	$default = array('showfilename' => 'n', 'showtitle'=>'n');
	$params = array_merge($default, $params);

	if (!empty($params['fgalId'])) {
		if ($prefs['feature_file_galleries'] != 'y') {
			return tra('This feature is disabled') . ': feature_file_galleries';
		}
		if (!$tikilib->user_has_perm_on_object($user, $params['fgalId'], 'file gallery', 'tiki_p_view_file_gallery')) {
			return tra('Permission denied');
		}
		if (empty($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
		$files = $tikilib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId'], false, false, false, true, false, false, false);
		$smarty->assign('colorboxUrl', 'tiki-download_file.php?fileId=');
		$smarty->assign('colorboxColumn', 'id');
		if ($params['thumb'] != 'n') {
			$smarty->assign('colorboxThumb', 'thumbnail');
		}
	} elseif (!empty($params['galId'])) {
		if ($prefs['feature_galleries'] != 'y') {
			return tra('This feature is disabled') . ': feature_galleries';
		}
		if (!$tikilib->user_has_perm_on_object($user, $params['galId'], 'gallery', 'tiki_p_view_image_gallery')) {
			return tra('Permission denied');
		}
		global $imagegallib; include_once ('lib/imagegals/imagegallib.php');
		if (empty($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
		$files = $imagegallib->get_images(0, -1, $params['sort_mode'], '', $params['galId']);
		$smarty->assign('colorboxUrl', 'show_image.php?id=');
		$smarty->assign('colorboxColumn', 'imageId');
		if ($params['thumb'] != 'n') {
			$smarty->assign('colorboxThumb', 'thumb');
		}
	} else {
		return tra('Incorrect param');
	}
	$smarty->assign('iColorbox', $iColorbox++);
	$smarty->assign_by_ref('colorboxFiles', $files);
	$smarty->assign_by_ref('params', $params);
	return $smarty->fetch('wiki-plugins/wikiplugin_colobox.tpl');
}
/* 
{img src=tiki-download_file.php?fileId=1&amp;thumbnail link=tiki-download_file.php?fileId=1&amp;display rel="shadowbox[gallery];type=img"}
<a href="tiki-download_file.php?fileId=4&amp;display" rel="shadowbox[gallery];type=img"></a>
<a href="tiki-download_file.php?fileId=7&amp;display" rel="shadowbox[gallery];type=img"></a>
*/
