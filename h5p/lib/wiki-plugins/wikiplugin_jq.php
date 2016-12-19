<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_jq_info()
{
	return array(
		'name' => tra('jQuery'),
		'documentation' => 'PluginJQ',
		'description' => tra('Add jQuery JavaScript code'),
		'prefs' => array( 'wikiplugin_jq' ),
		'body' => tra('JavaScript code'),
		'validate' => 'all',
		'filter' => 'none',
		'iconname' => 'code',
		'introduced' => 3,
		'params' => array(
			'notonready' => array(
				'required' => false,
				'name' => tra('Not On Ready'),
				'description' => tra('Do not execute on document ready (execute inline)'),
				'since' => '3.0',
			),
			'nojquery' => array(
				'required' => false,
				'name' => tra('No JavaScript'),
				'description' => tra('Optional markup for when JavaScript is off'),
				'since' => '3.0',
			),
			'lang' => array(
				'required' => false,
				'name' => tra('Language'),
				'description' => tra('Language to apply JQuery to'),
				'since' => '13.0',
			),
		)
	);
}
	
function wikiplugin_jq($data, $params)
{
	global $prefs;
	$headerlib = TikiLib::lib('header');
	extract($params, EXTR_SKIP);
	
	$nojquery = isset($nojquery) ? $nojquery : tr('<!-- jq plugin inactive: JavaScript off -->');
	if ($prefs['javascript_enabled'] != 'y') { 
		return $nojquery;
	}
	$notonready = isset($notonready) ? $notonready : false;

	if (!empty($lang) && $lang != $prefs['language']) {
		return;
	}

	// Need to manually decode greater than and less than (not sure if we want to decode all HTML entities
	$data = str_replace('&lt;', '<', $data);
	$data = str_replace('&gt;', '>', $data);

	if (!$notonready) {		
		$headerlib->add_jq_onready($data);
	} else { 
		$headerlib->add_js($data);
	}
	return '';
}
