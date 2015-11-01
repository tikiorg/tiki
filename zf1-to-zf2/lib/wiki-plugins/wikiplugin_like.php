<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_like_info()
{
	return array(
		'name' => tra('Like'),
		'documentation' => 'PluginFavorite',
		'description' => tra('Replicates like functionality seen on the web. Allows user to have a like button to like and unlike. Also shows the count of total likes.'),
		'prefs' => array( 'wikiplugin_like', 'user_likes' ),
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
			'count_only' => array(
				'required' => false,
				'name' => tra('Count only'),
				'description' => tra('Sets whether to only show the count of likes rather than give the option to vote'),
				'filter' => 'flag',
				'default' => 'false',
			),
		)
	);
}
function wikiplugin_like($data, $params)
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
	$smarty->assign('wikiplugin_like_objectId', urlencode($objectId));
	$smarty->assign('wikiplugin_like_objectType', urlencode($objectType));
	$smarty->assign('wikiplugin_like_count_only', urlencode($params['count_only']));
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_like.tpl');
	return $ret;
}						   
