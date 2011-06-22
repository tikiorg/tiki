<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_perspective_info() {
	return array(
		'name' => tra('Perspective'),
		'documentation' => 'PluginPerspective',
		'description' => tra('Display content based on the user\'s current perspective'),
		'prefs' => array( 'feature_perspective', 'wikiplugin_perspective' ),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed to users not matching the condition.'),
		'prefs' => array('wikiplugin_perspective'),
		'filter' => 'wikicontent',
		'params' => array(
			'perspectives' => array(
				'required' => false,
				'name' => tra('Allowed Perspectives'),
				'description' => tra('Pipe separated list of perspectives allowed to view the block. Perspectives may be specified by name or id. ex: Admins|2|Managers'),
				'filter' => 'text',
				'default' => ''
			),
			'notperspectives' => array(
				'required' => false,
				'name' => tra('Denied Perspectives'),
				'description' => tra('Pipe separated list of perspectives denied from viewing the block. Perspectives may be specified by name or id. ex: Anonymous|Managers|1'),
				'filter' => 'text',
				'default' => ''
			),
		),
	);
}

function wikiplugin_perspective($data, $params)
{
	global $access, $prefs, $perspectivelib;
	$access->check_feature('feature_perspective');

	require_once 'lib/perspectivelib.php';
	$currentPerspective = $perspectivelib->get_current_perspective( $prefs );

	$dataelse = '';
	if (strpos($data,'{ELSE}')) {
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	}

	if (!empty($params['perspectives'])) {
		$perspectives = array();
		foreach (explode('|', $params['perspectives']) as $p) {
			$perspectives[] = (!is_numeric($p))
				? $perspectivelib->get_perspectives_with_given_name($p)
				: $p;
		}
	}

	if (!empty($params['notperspectives'])) {
		$notperspectives = array();
		foreach (explode('|', $params['notperspectives']) as $p) {
			$notperspectives[] = (!is_numeric($p))
				? $perspectivelib->get_perspectives_with_given_name($p)
				: $p;
		}
	}

	if (!empty($perspectives) && !in_array($currentPerspective, $perspectives)) {
		return $dataelse;
	}

	if (!empty($notperspectives) && in_array($currentPerspective, $notperspectives)) {
		return $dataelse;
	}

	return $data;
}
