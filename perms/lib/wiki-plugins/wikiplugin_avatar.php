<?php

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
		'documentation' => 'PluginAvatar',
		'description' => tra('Displays the user Avatar'),
		'prefs' => array('wikiplugin_avatar'),
		'body' => tra('username'),
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('The wiki page the avatar will link to.'),
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('left|right'),
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
