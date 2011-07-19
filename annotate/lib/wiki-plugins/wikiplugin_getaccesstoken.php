<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_getaccesstoken_info() {
	return array(
		'name' => tra('Get Security Token'),
		'documentation' => tra('PluginGetAccessToken'),
		'description' => tra('Get security token for specified parameters'),
		'prefs' => array( 'auth_token_access', 'wikiplugin_getaccesstoken' ),
		'inline' => true,
		'validate' => 'all',
		'filter' => 'wikicontent',
		'params' => array(
			'entry' => array(
				'required' => true,
				'name' => tra('Entry point path or part of it'),
				'description' => tra('The path of part of the path for which the token is for'),
				'filter' => 'text',
				'default' => ''
			),
			'keys' => array(
				'required' => false,
				'keys' => tra('Query string parameter keys'),
				'description' => tra('Query string parameter keys for which the token is for, separated by :'),
				'filter' => 'text',
				'default' => array(),
				'separator' => ':'
			),
			'values' => array(
				'required' => false,
				'name' => tra('Query string parameter Values'),
				'description' => tra('Query string parameter values for which the token is for, separated by :'),
				'filter' => 'text',
				'default' => array(),
				'separator' => ':'
			), 
		),
	);
}

function wikiplugin_getaccesstoken( $data, $params ) {
	global $tikilib;
	if (!isset($params['entry'])) {
		return '';
	} else {
		$entry = $params['entry'];
	}
	if (!isset($params['keys'])) {
		$keys = array();
	} else {
		$keys = $params['keys'];
	}
	if (!isset($params['keys'])) {
		$values = array();
	} else {
		$values = $params['values'];
	}
	$bindvars = array();
	$querystringvars = array();
	for ($i = 0; $i < count($keys); $i++) {
		$querystringvars[$keys[$i]] = $values[$i];
	}
	if (!empty($querystringvars)) {
		$encoded = json_encode($querystringvars);
		$mid = " and `parameters` = ?";
		$bindvars[] = $encoded; 
	}	
	$query = "select `token` from `tiki_auth_tokens` where `entry` like '%$entry%' $mid";
	if ($ret = $tikilib->getOne($query, $bindvars)) {
		return $ret;
	} else {
		return '';
	}
} 
			
