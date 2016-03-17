<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
function module_last_podcasts_info()
{
	return array(
		'name' => tra('Newest Podcasts'),
		'description' => tra('Displays Podcasts.'),
		'prefs' => array('feature_file_galleries'),
		'common_params' => array('nonums', 'rows'),
		'params' => array(
			'galleryId' => array(
				'required' => false,
				'name' => tra('File galleries IDs'),
				'description' => tra('List of IDs of file galleries of type "Podcast (Audio)". Identifiers are separated by a colon (":"). If none, all file galleries will be scanned.') . ' ' . tra('Example value:') . ' 1:3. ',
				'filter' => 'int',
				'separator' => ':',
				'profile_reference' => 'file_gallery',
			),
			'width' => array(
				'required' => false,
				'name' => tra('width'),
				'description' => tra('Width of mediaplayer in pixels.'),
				'default' => 190,
			),
			'height' => array(
				'required' => false,
				'name' => tra('height'),
				'description' => tra('Height of mediaplayer in pixels.'),
				'default' => 20,
			),
			'mediaplayer' => array(
				'name' => tra('mediaplayer'),
				'description' => tra('Path to mp3 player. For instance media/player_mp3_maxi.swf if you downloaded player_mp3_maxi.swf from http://flash-mp3-player.net/players/maxi/download/ to directory media/ (directory needs to be created or you can chose another place).'),
			),
			'link_url' => array(
				'required' => false,
				'name' => tra('Bottom Link URL'),
				'description' => tra('URL for a link at bottom of module.'),
			),
			'link_text' => array(
				'required' => false,
				'name' => tra('Bottom Link URL Text'),
				'description' => tra('Text for link if Bottom Link URL is set.'),
				'default' => tra('More Podcasts'),
				'filter' => 'striptags',
			),
			'verbose' => array(
				'required' => false,
				'name' => tra('Verbose'),
				'description' => 'y|n ' . tra('Display description of podcast below player if "y", and on title mouseover if "n".'),
				'default' => 'y',
				'filter' => 'striptags',
			)
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_podcasts($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');

	$filegallib = TikiLib::lib('filegal');

	if (isset($module_params['galleryId'])) {
		if (is_string($module_params['galleryId'])) {
			$module_params['galleryId'] = explode(':', $module_params['galleryId']);
		}
		$mediafiles = $filegallib->get_files(0, $mod_reference["rows"], 'created_desc', '', $module_params['galleryId']);
	} else {
		$mediafiles = $filegallib->list_files(0, $mod_reference["rows"], 'created_desc', '');
	}
	
	$mediaplayer = (isset($module_params['mediaplayer']) && is_readable($module_params['mediaplayer'])) ? $module_params['mediaplayer'] : '';
	
	$smarty->assign('modLastFiles', $mediafiles['data']);
	$smarty->assign('mediaplayer', $mediaplayer);
	$smarty->assign('nonums', isset($module_params['nonums']) ? $module_params['nonums'] : 'n');
	$smarty->assign('verbose', isset($module_params['verbose']) ? $module_params['verbose'] : 'y');
	$smarty->assign('link_url', isset($module_params['link_url']) ? $module_params['link_url'] : '');
	$smarty->assign('link_text', isset($module_params['link_text']) ? $module_params['link_text'] : 'More Podcasts');
	$smarty->assign('player_width', isset($module_params['width']) ? $module_params['width'] : '190');
	$smarty->assign('player_height', isset($module_params['height']) ? $module_params['height'] : '20');
	$smarty->assign('module_rows', $mod_reference["rows"]);
}


