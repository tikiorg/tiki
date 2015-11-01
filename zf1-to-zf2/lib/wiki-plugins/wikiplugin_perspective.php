<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_perspective_info()
{
	return array(
		'name' => tra('Perspective'),
		'documentation' => 'PluginPerspective',
		'description' => tra('Display content based on the user\'s current perspective'),
		'prefs' => array( 'feature_perspective', 'wikiplugin_perspective' ),
		'body' => tr('Wiki text to display if conditions are met. The body may contain %0{ELSE}%1. Text after the
			marker will be displayed to users not matching the condition.', '<code>', '</code>'),
		'filter' => 'wikicontent',
		'iconname' => 'view',
		'introduced' => 7.1,
		'params' => array(
			'perspectives' => array(
				'required' => false,
				'name' => tra('Allowed Perspectives'),
				'description' => tra('Pipe-separated list of identifiers of perspectives in which the block is shown.')
					. tra('Example value:') . ' <code>2|3|5</code>',
				'since' => '7.1',
				'filter' => 'digits',
				'separator' => '|',
				'default' => '',
				'profile_reference' => 'perspective',
			),
			'notperspectives' => array(
				'required' => false,
				'name' => tra('Denied Perspectives'),
				'description' => tra('Pipe-separated list of identifiers of perspectives in which the block is not
					shown.') . tra('Example value:') . ' <code>3|5|8</code>',
				'since' => '7.1',
				'filter' => 'digits',
				'separator' => '|',
				'default' => '',
				'profile_reference' => 'perspective',
			),
		),
	);
}

function wikiplugin_perspective($data, $params)
{
	global $prefs;

	$dataelse = '';
	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}

	if (!empty($params['perspectives'])) {
		$perspectives = $params['perspectives'];
	}
	if (!empty($params['notperspectives'])) {
		$notperspectives = $params['notperspectives'];
	}
	if (empty($perspectives) && empty($notperspectives)) {
		return '';
	}

	$perspectivelib = TikiLib::lib('perspective');
	$currentPerspective = $perspectivelib->get_current_perspective($prefs);

	// if the current perspective is not an allowed perspective, return the content after the "{ELSE}"
	if (!empty($perspectives) && !in_array($currentPerspective, $perspectives)) {
		return $dataelse;
	}

	// if the current perspective is a denied perspective, return the content after the "{ELSE}"
	if (!empty($notperspectives) && in_array($currentPerspective, $notperspectives)) {
		return $dataelse;
	}

	return $data;
}
