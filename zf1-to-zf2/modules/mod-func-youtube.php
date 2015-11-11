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
 * @return array
 */
function module_youtube_info()
{
	return array(
		'name' => tra('YouTube'),
		'description' => tra('Displays YouTube videos and/or a link to a YouTube user\'s page.'),
		'prefs' => array(),
		'params' => array(
			'ids' => array(
				'name' => tra('Video identifiers'),
				'description' => tra('List of YouTube videos identifiers to display. Identifiers are separated by a comma (",").') . ' ' . tra('Example value:') . ' _wesmkqvUPI,XFGrQMD6Uqc. ',
				'filter' => 'striptags'
			),
			'user' => array(
				'name' => tra('YouTube user identifier'),
				'description' => tra('If set to a YouTube user identifier, display a link to the videos of this user.') . ' ' . tra('Example value:') . ' missmusic.',
				'filter' => 'striptags'
			),
			'width' => array(
				'required' => false,
				'name' => tra('width'),
				'description' => tra('Width of each video in pixels'),
				'default' => 200,
			),
			'height' => array(
				'required' => false,
				'name' => tra('height'),
				'description' => tra('Height of each video in pixels'),
				'default' => 350,
			)
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_youtube($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	
	$data = array(
		'urls' => array(),
		'xhtml' => array()
	);
	
	if (!empty($module_params['ids'])) {
		require_once('lib/wiki-plugins/wikiplugin_youtube.php');
		$ids = explode(',', $module_params['ids']);
		$data['urls']['gdata'] = array();
		foreach ($ids as $id) {
			$data['urls']['gdata'][$id] = ZendGData\YouTube::VIDEO_URI . '/' . $id;
			$params = array('movie' => $id);
			if (isset($module_params['width'])) $params['width'] = $module_params['width'];
			if (isset($module_params['height'])) $params['height'] = $module_params['height'];
			$data['xhtml'][$id] = preg_replace('/~np~(.*)~\/np~/', '$1', wikiplugin_youtube('', $params));
		}
	}
	
	if (!empty($module_params['user'])) {
		$data['urls']['user_home'] = 'http://www.youtube.com/user/' . $module_params['user'];
	}
	
	$smarty->assign_by_ref('data', $data);
	$smarty->assign('tpl_module_title', tra('Videos on YouTube'));
}
