<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to display a browser client information
// damian aka damosoft May 2004

function wikiplugin_agentinfo_help() {
        return tra("Displays browser client info").":<br />~np~{AGENTINFO(info=>IP or SVRSW or BROWSER)/}~/np~";
}

function wikiplugin_agentinfo_info() {
	return array(
		'name' => tra( 'User Agent Info' ),
		'documentation' => 'PluginAgentinfo',
		'description' => tra( 'Show user\'s browser and server information' ),
		'prefs' => array('wikiplugin_agentinfo'),
		'params' => array(
			'info' => array(
				'required' => false,
				'name' => tra('Info'),
				'description' => tra('Display\'s the visitor\'s IP address (IP or default), browser information (BROWSER), or server software (SVRSW).'),
				'default' => 'IP',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('IP address'), 'value' => 'IP'), 
					array('text' => tra('Server software'), 'value' => 'SVRSW'), 
					array('text' => tra('Browser'), 'value' => 'BROWSER'), 
				),
				
			),
		),
	);
}

function wikiplugin_agentinfo($data, $params) {
	global $tikilib;
	
	extract ($params,EXTR_SKIP);

	$asetup = '';

	if (!isset($info)) {
		$info = 'IP';
	}

	if ($info == 'IP') {
		$asetup = $tikilib->get_ip_address();
	}

	if ($info == 'SVRSW') {
		$asetup = $_SERVER["SERVER_SOFTWARE"];
	}
	
	if ($info == 'BROWSER') {
		$asetup = $_SERVER["HTTP_USER_AGENT"];
	}

	return $asetup;
}
