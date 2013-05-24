<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_favorite_info()
{
	return array(
		'name' => tra('Favorite'),
		'documentation' => 'PluginFavorite',
		'description' => tra('Shows if item is user favorite or not and provide the way to set it'),
		'prefs' => array( 'wikiplugin_favorite', 'user_favorites' ),	
		'format' => 'html',
		'params' => array(
			'objectType' => array(
				'required' => true,
				'name' => tra('Object Type'),
				'description' => tra('Object Type'),
				'filter' => 'text',
				'default' => '',
			),
			'objectId' => array(
				'required' => true,
				'name' => tra('Object ID'),
				'description' => tra('Object ID'),
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'type_in_param',
			),
		)
	);
}
function wikiplugin_favorite($data, $params)
{
	global $smarty;
	if ($params['objectType'] == 'usertracker') {
		$objectType = 'trackeritem';
		$objectId = 0;
		if ($userid = Tikilib::lib('tiki')->get_user_id($params['objectId'])) {
			$tracker = TikiLib::lib('user')->get_usertracker($userid);
			if ( $tracker && $tracker['usersTrackerId'] ) {
				$objectId = TikiLib::lib('trk')->get_item_id($tracker['usersTrackerId'], $tracker['usersFieldId'], $params['objectId']);
			}
		}
	} else {
		$objectType = $params['objectType'];
		$objectId = $params['objectId'];
	}
	$smarty->assign('wikiplugin_favorite_objectId', urlencode($objectId));
	$smarty->assign('wikiplugin_favorite_objectType', urlencode($objectType));	
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_favorite.tpl');
	return $ret;
}						   
