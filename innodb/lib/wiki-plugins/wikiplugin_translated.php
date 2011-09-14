<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_translated_info() {
	return array(
		'name' => tra('Translated'),
		'documentation' => 'PluginTranslated',
		'description' => tra('Create multi-lingual links'),
		'prefs' => array( 'feature_multilingual', 'wikiplugin_translated' ),
		'body' => tra('[url] or ((wikiname)) or ((inter:interwiki)) (use wiki syntax)'),
		'icon' => 'pics/icons/world_link.png',
		'params' => array(
			'lang' => array(
				'required' => true,
				'name' => tra('Language'),
				'description' => tra('Two letter language code of the language, ex: fr'),
				'default' => '',
			),
			'flag' => array(
				'required' => false,
				'name' => tra('Flag'),
				'description' => tra('Country name, ex: France'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_translated($data, $params) {
	extract ($params,EXTR_SKIP);
	$img = '';

	$h = opendir("img/flags/");
	while ($file = readdir($h)) {
		if (substr($file,0,1) != '.' and substr($file,-4,4) == '.gif') {
			$avflags[] = substr($file,0,strlen($file)-4);
		}
	}
	if (isset($flag)) {
		if (in_array($flag,$avflags)) { 
			$img = "<img src='img/flags/$flag.gif' width='18' height='13' vspace='0' hspace='3' alt='$lang' align='baseline' /> "; 
		}
	}

	if (!$img) {
		$img = "( $lang ) ";
	}
	
	if (isset($data)) {
		$back = $img.$data;
	} else {
		$back = "''no data''";
	}

	return $back;
}
