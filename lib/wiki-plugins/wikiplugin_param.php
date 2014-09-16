<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


function wikiplugin_param_info()
{
	return array(
		'name' => tra('Param'),
		'documentation' => 'PluginParam',
		'description' => tra('Display content based on request parameters'),
		'prefs' => array( 'wikiplugin_param' ),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed if conditions are not met.'),
		'icon' => 'img/icons/page_gear.png',
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra('Names of parameters required to display text, separated by |.'),
				'filter' => 'text',
				'separator' => '|',
			),
			'source' => array(
				'required' => false,
				'name' => tra('Source'),
				'default' => 'request',
				'description' => tra('Source where the parameter is checked.'),
				'filter' => 'text',
				'options' => array (
					array('text' => tra('REQUEST'), 'value' => ''),
					array('text' => tra('GET'), 'value' => 'get'),
					array('text' => tra('POST'), 'value' => 'post'),
					array('text' => tra('COOKIE'), 'value' => 'cookie'),
				),
			),
			'value' => array(
				'required' => false,
				'name' => tra('Value'),
				'description' => tra('Value to test for. If empty then just tests if the named params are set and not "empty".'),
				'filter' => 'text',
			),
		)
	);
}

function wikiplugin_param($data, $params)
{
	$dataelse = '';
	$test = true;

	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data, '{ELSE}') + 6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}

	if (!isset($params['source']) || empty($params['source'])) {
		$params['source'] = 'request';
	}

	foreach ($params['name'] as $name) {
		$value = null;
		switch ($params['source']) {
			case 'get':
				$value = isset($_GET[$name]) ? $_GET[$name] : null;
				break;
			case 'post':
				$value = isset($_POST[$name]) ? $_POST[$name] : null;
				break;
			case 'cookie':
				$value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
				break;
			default:
				$value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
				break;
		}
		if (isset($params['value'])) {
			if ($value !== $params['value']) {
				$test = false;
				break;
			}
		} else if (empty($value)) {	// multiple "names" work as AND
			$test = false;
			break;
		}
	}

	return $test ? $data : $dataelse;
}
