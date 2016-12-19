<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_perm_info()
{
	return array(
		'name' => tra('Permissions'),
		'documentation' => 'PluginPerm',
		'description' => tra('Display content based on permission settings'),
		'body' => tr('Wiki text to display if conditions are met. The body may contain %0{ELSE}%1. Text after the
			marker will be displayed to users not matching the conditions.', '<code>', '</code>'),
		'prefs' => array('wikiplugin_perm'),
		'filter' => 'wikicontent',
		'iconname' => 'permission',
		'introduced' => 5,
		'params' => array(
			'perms' => array(
				'required' => false,
				'name' => tra('Possible Permissions'),
				'description' => tra('Pipe-separated list of permissions, one of which is needed to view the default
					text.') . ' ' . tra('Example:') . ' <code>tiki_p_rename|tiki_p_edit</code>',
				'since' => '5.0',
				'filter' => 'text',
				'separator' => '|',
				'default' => '',
			),
			'notperms' => array(
				'required' => false,
				'name' => tra('Forbidden Permissions'),
				'description' => tra('Pipe-separated list of permissions, any of which will cause the default text
					not to show.') . ' ' . tra('Example:') . ' <code>tiki_p_rename|tiki_p_edit</code>',
				'since' => '5.0',
				'filter' => 'text',
				'separator' => '|',
				'default' => '',
			),
			'global' => array(
				'required' => false,
				'name' => tra('Global'),
				'description' => tra('Indicate whether the permissions are global or local to the object'),
				'since' => '5.0',
				'filter' => 'text',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0')
				),
			),
		)
	);
}

function wikiplugin_perm($data, $params)
{
	global $user;
	$userlib = TikiLib::lib('user');
	if (!empty($params['perms']))
		$perms = $params['perms'];
	if (!empty($params['notperms']))
		$notperms = $params['notperms'];
	if (!empty($params['global']) && $params['global'] == '1') {
		$global = true;
	} else {
		$global = false;
	}

	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	} else {
		$dataelse = '';
	}

	if (!empty($perms)) {
		$ok = false;
		foreach ($perms as $perm) {
			if ($global) {
				if ($userlib->user_has_permission($user, $perm)) {
					$ok = true;
					break;
				}
			} else {
	    		global $$perm;
				if ($$perm == 'y') {
					$ok = true;
					break;
				}
			}
		}
		if (!$ok)
			return $dataelse;
	}
	if (!empty($notperms)) {
		$ok = true;
		foreach ($notperms as $perm) {
			if ($global) {
				if ($userlib->user_has_permission($user, $perm)) {
					$ok = false;
					break;
				}
			} else {
				global $$perm;
				if ($$perm == 'y') {
					$ok = false;
					break;
				}
			}
		}
		if (!$ok)
			return $dataelse;
	}

	return $data;
}
