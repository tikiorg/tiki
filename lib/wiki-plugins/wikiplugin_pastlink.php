<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
		'description' => tra('Links your article to a site using futurelink protocol'),
        'keywords' => array('forward', 'futurelink', 'futurelink-protocol', 'futurelinkprotocol', 'protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_pastlink', 'feature_futurelinkprotocol' ),
		'icon' => 'img/icons/link.png',
		'body' => tra('Text to link to futurelink'),
		'params' => array(			
			'clipboarddata' => array(
				'required' => true,
				'name' => tra('ClipboardData'),
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
