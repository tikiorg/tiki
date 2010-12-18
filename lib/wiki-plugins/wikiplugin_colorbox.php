<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_colorbox_info() {
	return array(
		'name' => tra('colorbox'),
		'documentation' => 'PluginColorBox',
		'description' => tra('Display a gallery of images in a popup slideshow'),
		'prefs' => array( 'feature_file_galleries', 'feature_shadowbox', 'wikiplugin_colorbox' ),
		'introduced' => 5,
		'params' => array(
			'fgalId' => array(
				'required' => false,
				'name' => tra('File Gallery ID'),
				'description' => tra('ID number of the file gallery that contains the images to be displayed'),
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
				'since' => '5.0'
				),
			'galId' => array(
				'required' => false,
				'name' => tra('Image Gallery ID'),
				'description' => tra('ID number of the image gallery that contains the images to be displayed'),
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
				'since' => '5.0'
				),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID Filter'),
				'description' => tra('Filter on fileIds in a file gallery to only show those images. Separate each fileId with \':\''),
				'filter' => 'digits',
				'separator' => ':',
				'accepted' => 'ID separated with :',
				'default' => '',
				'since' => '6.0'
				),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumb'),
				'description' => tra('Display as a thumbnail (y) or full size (n)'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'since' => '5.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
					),
				),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort by database table field name, ascending or descending. Examples: fileId_asc or name_desc.'),
				'filter' => 'word',
				'accepted' => 'fieldname_asc or fieldname_desc with actual table field name in place of \'fieldname\'.',
				'default' => 'created_desc',
				'since' => '5.0'
				),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show File Title'),
				'description' => tra('Show file title'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'since' => '5.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'showfilename' => array(
				'required' => false,
				'name' => tra('Show File Name'),
				'description' => tra('Show file name'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'since' => '5.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'showallthumbs' => array(
				'required' => false,
				'name' => tra('Show All Thumbs'),
				'description' => tra('Show thumbnails of all the images in the gallery'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'since' => '5.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'parsedescriptions' => array(
				'required' => false,
				'name' => tra('Parse Descriptions'),
				'description' => tra('Wiki parse the file descriptions'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'n',
				'since' => '5.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
		),
	);
}
function wikiplugin_colorbox($data, $params) {
	global $tikilib, $smarty, $user, $prefs;
	static $iColorbox = 0;
	$default = array('showfilename' => 'n', 'showtitle'=>'n', 'thumb'=>'y', 'showallthumbs'=>'n', 'parsedescriptions'=>'n');
	$params = array_merge($default, $params);

	if (!empty($params['fgalId'])) {
		if ($prefs['feature_file_galleries'] != 'y') {
			return tra('This feature is disabled') . ': feature_file_galleries';
		}
		if (!$tikilib->user_has_perm_on_object($user, $params['fgalId'], 'file gallery', 'tiki_p_view_file_gallery')) {
			return tra('Permission denied');
		}
		if (empty($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
		$filter = empty($params['fileId'])? '': array('fileId'=> $params['fileId']);
		$files = $tikilib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId'], false, false, false, true, false, false, false, false, '', true, false, false, $filter);
		$smarty->assign('colorboxUrl', 'tiki-download_file.php?fileId=');
		$smarty->assign('colorboxColumn', 'id');
		if ($params['thumb'] != 'n') {
			$smarty->assign('colorboxThumb', 'thumbnail');
		} else {
			$smarty->assign('colorboxThumb', 'display');
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
	foreach ($files['data'] as &$file) {
		$str = '';
		if ($params['showtitle'] == 'y' && !empty($file['name'])) {
			$str .= '<strong>' . $file['name'] . '</strong>';
		}
		if ($params['showfilename'] == 'y' && !empty($file['filename'])) {
			$str .= empty($str) ? '' : '<br />';
			$str .= $file['filename'];
		}
		if (!empty($file['description'])) {
			global $tikilib, $prefs;
			$str .= empty($str) ? '' : '<br />';
			if ($params['parsedescriptions'] == 'y') {
				$op = $prefs['feature_wiki_paragraph_formatting'];
				$op2 = $prefs['feature_wiki_paragraph_formatting_add_br'];
				$prefs['feature_wiki_paragraph_formatting'] = 'n';
				$prefs['feature_wiki_paragraph_formatting_add_br'] = 'n';
				$str .= $tikilib->parse_data($file['description'], array( 'suppress_icons' => true ));
				$prefs['feature_wiki_paragraph_formatting'] = $op;
				$prefs['feature_wiki_paragraph_formatting_add_br'] = $op2;
			} else {
				$str .= preg_replace('/[\n\r]/', '', nl2br($file['description']));
			}
		}
		$file['elTitle'] = $str;
	}
	$smarty->assign('iColorbox', $iColorbox++);
	$smarty->assign_by_ref('colorboxFiles', $files);
	$smarty->assign_by_ref('params', $params);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_colobox.tpl').'~/np~';
}
/* 
{img src=tiki-download_file.php?fileId=1&amp;thumbnail link=tiki-download_file.php?fileId=1&amp;display rel="shadowbox[gallery];type=img"}
<a href="tiki-download_file.php?fileId=4&amp;display" rel="shadowbox[gallery];type=img"></a>
<a href="tiki-download_file.php?fileId=7&amp;display" rel="shadowbox[gallery];type=img"></a>
*/
