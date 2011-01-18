<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to display the number of hits per object
// Franck Martin 2005

function wikiplugin_objecthits_help() {
        return tra("Displays object hit info by object and days").":<br />~np~{OBJECTHITS(object=>,type=>,days=>)/}~/np~";
}

function wikiplugin_objecthits_info() {
	return array(
		'name' => tra('Object Hits'),
		'documentation' => 'PluginObjectHits',
		'description' => tra('Display the number of hits for certain objects'),
		'prefs' => array( 'wikiplugin_objecthits' ),
		'params' => array(
			'object' => array(
				'required' => false,
				'name' => tra( 'Object' ),
				'description' => tra( 'For a wiki page, the page name, for other object types: ID number + ? + object title' ),
				'default' => '',
			),
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Object type, such as wiki, file gallery, file, article, etc. Default is "wiki".'),
				'filter' => 'alpha',
				'default' => 'wiki',
			),
			'days' => array(
				'required' => false,
				'name' => tra('Days'),
				'description' => tra('Show the number of hits over the past number of days indicated. Default is to show all hits.'),
				'default' => 0,
			)
		)
	);
}

function wikiplugin_objecthits($data, $params) {
	global $tikilib;

	global $statslib;
	if (!is_object($statslib)) {
		global $dbTiki;
		include "lib/stats/statslib.php";
	}
 
	extract ($params,EXTR_SKIP);

	if (!isset($object)) {
	  global $page;
		$object = $page;
		$type= "wiki";
	}

	if (!isset($days)) {
		$days=0;
	}
	
	if (!isset($type)) {
		$type="wiki";
	}
	
  return $statslib->object_hits($object,$type,$days);
}
