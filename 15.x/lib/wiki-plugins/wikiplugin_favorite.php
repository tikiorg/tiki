<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_favorite_info()
{
	return array(
		'name' => tra('Favorite'),
		'documentation' => 'PluginFavorite',
		'description' => tra('Display a button for a user to click to make an object a favorite'),
		'prefs' => array( 'wikiplugin_favorite', 'user_favorites' ),	
		'format' => 'html',
		'iconname' => 'star',
		'introduced' => 8,
		'params' => array(
			'objectType' => array(
				'required' => true,
				'name' => tra('Object Type'),
				'description' => tra('Indicate type of object'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '',
			),
			'objectId' => array(
				'required' => true,
				'name' => tra('Object ID'),
				'description' => tra('Enter the ID of the object'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'type_in_param',
			),
		)
	);
}
function wikiplugin_favorite($data, $params)
{
	$smarty = TikiLib::lib('smarty');
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
