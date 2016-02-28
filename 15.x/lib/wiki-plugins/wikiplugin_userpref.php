<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_userpref_info()
{
	return array(
		'name' => tra('User Preference'),
		'documentation' => 'PluginUserpref',
		'description' => tra('Display contents based on user preference settings'),
		'body' => tr('Wiki text to display if conditions are met. The body may contain %0. Text after the marker
			will be displayed to users not matching the conditions.', '<code>{ELSE}</code>'),
		'prefs' => array('wikiplugin_userpref'),
		'filter' => 'wikicontent',
		'extraparams' => true,
		'iconname' => 'user',
		'introduced' => 4,
		'params' => array(
		),
	);
}

function wikiplugin_userpref($data, $params)
{
	global $user, $prefs, $tikilib;
	$dataelse = '';
	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}

	$else = false;
	foreach ($params as $prefName=>$prefValue) {
		if ($tikilib->get_user_preference($user, $prefName) != $prefValue) {
			return $dataelse;
		}
	}
	return $data;
}
