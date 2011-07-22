<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_trackercomments.php 34503 2011-05-19 13:42:26Z sylvieg $

function wikiplugin_comment_info() {
	return array(
		'name' => tra('Comment'),
		'documentation' => 'PluginComment',
		'description' => tra('Display a comment area for any specified object'),
		'prefs' => array( 'wikiplugin_comment' ),	
		'format' => 'html',
		'icon' => 'pics/icons/comments.png',
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
function wikiplugin_comment($data, $params) {
	global $smarty;
	$smarty->assign('wikiplugin_comment_objectId', $params['objectId']);
	$smarty->assign('wikiplugin_comment_objectType', $params['objectType']);	
	$ret = $smarty->fetch('wiki-plugins/wikiplugin_comment.tpl');
	return $ret;
}						   
