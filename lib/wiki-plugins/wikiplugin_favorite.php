<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_trackercomments.php 34503 2011-05-19 13:42:26Z sylvieg $

function wikiplugin_favorite_info() {
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
				'filter' => 'int',
				'default' => '',
			),
		)
	);
}
function wikiplugin_favorite($data, $params) {
	global $smarty;
	$smarty->assign('wikiplugin_favorite_objectId', urlencode($params['objectId']));
	$smarty->assign('wikiplugin_favorite_objectType', urlencode($params['objectType']));	
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_favorite.tpl');
	return $ret;
}						   
