<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_friend_info()
{
	return array(
		'name' => tra('Friend'),
		'documentation' => 'PluginFriend',
		'description' => tra('Friend and unfriend other users'),
		'prefs' => array('wikiplugin_friend', 'feature_search'),
		'format' => 'html',
		'body' => tra('Add or Remove Friend Button'),
		'filter' => 'int',
		'introduced' => 15,
		'iconname' => 'group',
		'params' => array(
			'other_user' => array(
				'required' => false,
				'name' => tra('Other User'),
				'description' => tra("The user you wish to change relations with."),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
			'add_button_text' => array(
				'required' => false,
				'name' => tra('Button Text'),
				'description' => tra("Button text that's displayed when friending a user."),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
			'remove_button_text' => array(
				'required' => false,
				'name' => tra('Button Text'),
				'description' => tra("Button text that's displayed when un-friending a user."),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_friend($data, $params)
{
	extract($params, EXTR_SKIP);
	global $user;

	if(empty($other_user)) {
		return;
	}
	if(empty($add_button_text)) {
		$add_button_text = tra("Add to Friend Network");
	}
	if(empty($remove_button_text)) {
		$remove_button_text = tra("Remove from Friend Network");
	}

	$relationlib = Tikilib::lib('relation');
	$is_friend = $relationlib->get_relation_id("tiki.friend.follow", "user", $user, "user", $other_user);

	if($is_friend) {
		$action = 'remove';
	} else {
		$action = 'add';
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('add_button_text', $add_button_text);
	$smarty->assign('remove_button_text', $remove_button_text);
	$smarty->assign('userwatch', $other_user);
	$smarty->assign('action', $action);
	return $smarty->fetch('wiki-plugins/wikiplugin_friend.tpl');
}

