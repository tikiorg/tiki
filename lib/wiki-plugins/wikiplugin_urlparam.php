<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_group.php 25177 2010-02-13 17:34:48Z changi67 $

// Display wiki text if a parameter is set in URL
// Usage:
// {URLPARAM(name=>Date|Version)}wiki text{URLPARAM}

function wikiplugin_urlparam_help() {
	$help = tra("Display wiki text if all keys are existing URL parameters").":\n";
	$help.= "~np~<br />{URLPARAM(name=>Date|Version)}wiki text{URLPARAM}<br />
	{URLPARAM(name=>Date|Version)}wiki text{ELSE}alternate text when parameters do not exist{URLPARAM}~/np~";
	return $help;
}

function wikiplugin_urlparam_info() {
	return array(
                'name' => tra('UrlParam'),
		'documentation' => 'PluginUrlParam',
		'description' => tra('Display wiki text if URL param is set'),
		'prefs' => array( 'wikiplugin_urlparam' ),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed to users not matching the condition.'),
                'params' => array(
			'name' => array(
                            'required' => true,
                            'name' => tra('Name'),
                            'description' => tra('Names of URL parameter required to display text')
                        )
                )
	);
}

function wikiplugin_urlparam($data, $params) {
	$dataelse = '';
        $names = array();

	if (strpos($data,'{ELSE}')) {
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	}

	if (!empty($params['name'])) {
		$names = explode('|', $params['name']);
	}

        foreach ($names as $name) {
            if (!isset($_REQUEST[$name]) || empty($_REQUEST[$name])) {
                return $dataelse;
            }
        }

	return $data;
}
