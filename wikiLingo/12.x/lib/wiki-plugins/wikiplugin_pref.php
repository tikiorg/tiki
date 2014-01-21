<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_pref_info()
{
	return array(
		'name' => tra('Preference'),
		'documentation' => 'PluginPref',
		'description' => tra('Display contents based on global preferences'),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed if not matching the conditions.'),
		'prefs' => array('wikiplugin_pref'),
		'filter' => 'wikicontent',
		'extraparams' => true,
		'icon' => 'img/icons/wrench.png',
		'params' => array(
		),
	);
}

function wikiplugin_pref($data, $params)
{
	global $prefs, $tikilib;
	$dataelse = '';
	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}

	$else = false;
	foreach ($params as $prefName=>$prefValue) {
		if ($tikilib->get_preference($prefName) != $prefValue) {
			return $dataelse;
		}
	}
	return $data;
}
