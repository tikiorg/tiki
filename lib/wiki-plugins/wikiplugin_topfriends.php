<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_topfriends_info()
{
	return array(
		'name' => tra('Top Friends'),
		'documentation' => 'PluginTopFriends',
		'description' => tra('List top-scoring users'),
		'prefs' => array( 'feature_friends', 'wikiplugin_topfriends' ),
		'iconname' => 'star',
		'introduced' => 2,
		'params' => array(
			'limit' => array(
				'required' => false,
				'name' => tra('Limit'),
				'description' => tra('Maximum result count.'),
				'since' => '2.0',
				'filter' => 'digits',
				'default' => 5,
			),
			'public' => array(
				'required' => false,
				'name' => tra('Public'),
				'description' => tra('Set whether public or not.'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_topfriends($data, $params)
{
	// TODO : Re-implement
	$smarty = TikiLib::lib('smarty');
	$empty = array();
	$smarty->assign_by_ref('listusers', $empty);

	return $smarty->fetch('plugins/plugin-topfriends.tpl');
}
