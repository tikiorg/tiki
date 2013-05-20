<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_avatar_info()
{
	return array(
		'name' => tra('Avatar'),
		'documentation' => 'PluginAvatar',
		'description' => tra('Display a user\'s avatar'),
		'prefs' => array('wikiplugin_avatar'),
		'body' => tra('username'),
		'icon' => 'img/icons/user.png',
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('The wiki page the avatar will link to. If empty and the user\'s information is public, then the avatar will link automatically the that user\'s user information page'),
				'default' => '',
				'profile_reference' => 'wiki_page',
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the avatar on the page'),
				'filter' => 'word',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
				),
			),
			'fullsize' => array(
				'required' => false,
				'name' => tra('Show fullsize File Gallery image'),
				'description' => tra('If fullsize images are stored in the File Gallery, show the full size one.'),
				'default' => 'n',
			),
		),
	);
}

function wikiplugin_avatar($data, $params)
{
	global $tikilib, $userlib, $prefs, $user;

	extract($params, EXTR_SKIP);

	if (!$data) {
		$data = $user;
	}

	if (isset($float))
		$avatar = $tikilib->get_user_avatar($data, $float);
	else
		$avatar = $tikilib->get_user_avatar($data);


	if (isset($fullsize) && $fullsize == 'y' && $prefs["user_store_file_gallery_picture"] == 'y') {
		$avatar = '<img src="tiki-show_user_avatar.php?fullsize=y&user='. urlencode($data) . '"></img>';
	}

	if (isset($page)) {
		$avatar = "<a href='tiki-index.php?page=$page'>" . $avatar . '</a>';
	} else if ($userlib->user_exists($data) && $tikilib->get_user_preference($data, 'user_information', 'public') == 'public') {
		$id = $userlib->get_user_id($data);
		$avatar = "<a href=\"tiki-user_information.php?userId=$id\">" . $avatar . '</a>';
	}

	return $avatar;
}
