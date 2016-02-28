<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: wikiplugin_pastlink.php
// Required path: /lib/wiki-plugins
//
// Programmer: Robert Plummer
//
// Purpose: Plugin that instantiates a pastlink within a page

function wikiplugin_pastlink_info()
{
	return array(
		'name' => tra('PastLink'),
		'documentation' => 'PluginPastLink',
		'description' => tra('Link content to another site'),
        'keywords' => array('forward', 'futurelink', 'futurelink-protocol', 'futurelinkprotocol', 'protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_pastlink', 'feature_futurelinkprotocol' ),
		'iconname' => 'link',
		'introduced' => 13,
		'body' => tra('Text to link to futurelink'),
		'params' => array(			
			'clipboarddata' => array(
				'required' => true,
				'name' => tra('Clipboard Data'),
				'since' => '13.0',
				'default' => false
			),
		),
	);
}

function wikiplugin_pastlink($data, $params)
{
    global $page;
	
	$params = array_merge(array("clipboarddata" => ""), $params);
	
	$clipboarddata = json_decode(stripslashes(trim(urldecode($params['clipboarddata']))));

	if (empty($clipboarddata)) return $data;

	FutureLink_PastUI::add($clipboarddata, $page, $data);

    return $data;
}
