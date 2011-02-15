<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Displays the user Avatar
// Use:
// {AVATAR()}username{AVATAR}
//  (page=>some)         Avatar is a link to "some"
//  (float=>left|right)  Avatar is floated to left or right
//
// If no avatar nothing is displayed
function wikiplugin_avatar_help() {
	return tra("Displays the user Avatar").":<br />~np~{AVATAR(page=>SomeWikiPage,float=>left|right)}".tra("username")."{AVATAR}~/np~";
}

function wikiplugin_avatar_info() {
	return array(
		'name' => tra('Avatar'),
		'documentation' => tra('PluginAvatar'),
		'description' => tra('Displays the user Avatar'),
		'prefs' => array('wikiplugin_avatar'),
		'body' => tra('username'),
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('The wiki page the avatar will link to. If empty and the user\'s information is public, 
										then the avatar will link automatically the that user\'s user information page'),
				'default' => ''
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the avatar on the page'),
				'filter' => 'word',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Right'), 'value' => 'right'), 
					array('text' => tra('Left'), 'value' => 'left')
				),
			),
		),
	);
}

function wikiplugin_avatar($data, $params) {
	global $tikilib;

	global $userlib;

	extract ($params,EXTR_SKIP);

	if (isset($float))
		$avatar = $tikilib->get_user_avatar($data, $float);
	else
		$avatar = $tikilib->get_user_avatar($data);

	if (isset($page)) {
		$avatar = "<a href='tiki-index.php?page=$page'>" . $avatar . '</a>';
	} else if ($userlib->user_exists($data) && $tikilib->get_user_preference($data, 'user_information', 'public') == 'public') {
		$id = $userlib->get_user_login($data);
		$avatar = "<a href=\"tiki-user_information.php?userId=$id\">" . $avatar . '</a>';
	}

	return $avatar;
}
