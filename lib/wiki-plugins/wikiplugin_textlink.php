<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		'prefs' => array( 'feature_wiki', 'wikiplugin_textlink', 'feature_forwardlinkprotocol' ),
		'icon' => 'pics/icons/link.png',
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
    global $tikilib, $headerlib, $caching, $page;
    static $textlinkI = 0;
	++$textlinkI;
	$i = $textlinkI;
	
	$params = array_merge(array("clipboarddata" => ""), $params);
	extract($params, EXTR_SKIP);
	
	$clipboarddata = json_decode(stripslashes(trim(urldecode($clipboarddata))));
	if(empty($clipboarddata)) return $data;
	
	$clipboarddata->href = urldecode($clipboarddata->href);
	$clipboarddata->serial = urldecode($clipboarddata->serial);
	
	Feed_Remote_ForwardLink_Contribution::add(array(
		"page"=> $page,
		"forwardLink"=> $clipboarddata,
		"textlink"=> array(
			"body"=> $data,
			"href"=> $tikilib->tikiUrl() . "tiki-index.php?page=$page#textlink$i"
		)
	));
	
	if (!empty($clipboarddata->href)) {
    	return $data."~np~<a href='" .$clipboarddata->href ."' id='textlink$i'>*</a>~/np~";
	} else {
    	return $data;
    }
}
