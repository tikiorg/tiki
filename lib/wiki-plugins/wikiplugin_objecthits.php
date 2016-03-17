<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_objecthits_info()
{
	return array(
		'name' => tra('Object Hits'),
		'documentation' => 'PluginObjectHits',
		'description' => tra('Display the number of hits for certain objects'),
		'prefs' => array( 'wikiplugin_objecthits' ),
		'iconname' => 'chart',
		'introduced' => 1,
		'params' => array(
			'object' => array(
				'required' => false,
				'name' => tra('Object'),
				'description' => tra('For a wiki page, the page name, for other object types: ID number + ? +
					object title'),
				'since' => '1',
				'default' => '',
				'filter' => 'text',
				'profile_reference' => 'type_in_param',
			),
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tr('Object type, such as wiki, file gallery, file, article, etc. Default is
					%0wiki%1.', '<code>', '</code>'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'wiki',
			),
			'days' => array(
				'required' => false,
				'name' => tra('Days'),
				'description' => tra('Show the number of hits over the past number of days indicated. Default is to
					show all hits.'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
			),
			'since' => array(
				'required' => false,
				'name' => tra('Since a date'),
				'description' => tra('Date since the hits are collected in a format supported by strtotime'),
				'since' => '10.0',
				'default' => '',
				'filter' => 'text',
			),
		)
	);
}

function wikiplugin_objecthits($data, $params)
{
	$tikilib = TikiLib::lib('tiki');
	$default = array('days' => 0, 'since' => '', 'type' => 'wiki');
	$params = array_merge($default, $params);

	$statslib = TikiLib::lib('stats');
 
	extract($params, EXTR_SKIP);

	if (!isset($object)) {
		global $page;
		$object = $page;
		$type= "wiki";
	}
	if (!empty($since)) {
		$since = strtotime($since);
	}
	
	return $statslib->object_hits($object, $type, $days, $since);
}
