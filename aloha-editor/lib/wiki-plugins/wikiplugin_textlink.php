<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_textlink_info()
{
	return array(
		'name' => tra('TextLink'),
		'documentation' => 'PluginTextlink',
		'description' => tra('Links your article to a site using forwardlink protocol'),
        'keywords' => array('forward', 'forwardlink', 'forwardlink-protocol', 'forwardlinkprotocol', 'protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_textlink', 'feature_forwardlinkprotocol' ),
		'icon' => 'img/icons/link.png',
		'body' => tra('Text to link to forwardlink'),
		'params' => array(			
			'clipboarddata' => array(
				'required' => true,
				'name' => tra('ClipboardData'),
				'default' => false
			),
		),
	);
}

function wikiplugin_textlink($data, $params)
{
    global $page;
	
	$params = array_merge(array("clipboarddata" => ""), $params);
	
	$clipboarddata = json_decode(stripslashes(trim(urldecode($params['clipboarddata']))));

	if (empty($clipboarddata)) return $data;

	Feed_TextLink::add($clipboarddata, $page, $data);

    return $data;
}
