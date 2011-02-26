<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Display wiki text if a parameter is set in URL
// Usage:
// {PARAM(name=>Date|Version)}wiki text{PARAM}

define('WIKIPLUGIN_PARAM_REQUEST', 'request');

function wikiplugin_param_help() {
	$help = tra("Display wiki text if all keys are existing URL parameters").":\n";
	$help.= "~np~<br />{param(name=>Date|Version)}wiki text{param}<br />
	{PARAM(name=>Date|Version)}wiki text{ELSE}alternate text when parameters do not exist{PARAM}~/np~";
	return $help;
}

function wikiplugin_param_info() {
	return array(
		'name' => tra('Param'),
		'documentation' => 'PluginParam',
		'description' => tra('Display content based on URL parameters'),
		'prefs' => array( 'wikiplugin_param' ),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed to users not matching the condition.'),
		'icon' => 'pics/icons/page_gear.png',
		'params' => array(
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'description' => tra('Names of parameter required to display text')
			),
			'source' => array(
				'required' => false,
				'name' => tra('Source'),
				'default' => 'request',
				'description' => tra('Source where the parameter is checked. Possible values : request ...')
			)
		)
	);
}

function wikiplugin_param($data, $params) {
	$dataelse = '';
        $names = array();
	$test = true;

	if (strpos($data,'{ELSE}')) {
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	}

	if (!empty($params['name'])) {
		$names = explode('|', $params['name']);
	}

	if (!isset($params['source']) || empty($params['source'])) {
		$params['source'] = WIKIPLUGIN_PARAM_REQUEST;
	}

        foreach ($names as $name) {
		switch ($params['source']) {
			case WIKIPLUGIN_PARAM_REQUEST:
				$test &= (isset($_REQUEST[$name]) && !empty($_REQUEST[$name]));
				break;
		}
        }

	return $test ? $data : $dataelse;
}
