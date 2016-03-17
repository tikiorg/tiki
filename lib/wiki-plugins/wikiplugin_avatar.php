<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_avatar_info()
{
	return array(
		'name' => tra('Profile picture'),
		'documentation' => 'PluginAvatar',
		'description' => tra('Display a user\'s profile picture'),
		'prefs' => array('wikiplugin_avatar'),
		'body' => tra('username'),
		'iconname' => 'user',
		'introduced' => 1,
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('The wiki page the profile picture will link to. If empty and the user\'s
					information is public, then the profile picture will link automatically the that user\'s user
					information page'),
				'since' => '1',
				'default' => '',
				'profile_reference' => 'wiki_page',
			),
			'float' => array(
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the profile picture on the page'),
				'since' => '1',
				'filter' => 'word',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
				),
			),
			'fullsize' => array(
				'required' => false,
				'name' => tra('Full Size'),
				'description' => tra('If full-size images are stored in the file gallery, show the full-size image.'),
				'default' => 'n',
				'since' => '10.0',
			),
		),
	);
}

function wikiplugin_avatar($data, $params)
{
	global $prefs, $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');

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
