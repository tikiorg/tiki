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
			'forwardlink' => array(
				'required' => true,
				'name' => tra('ForwardLink'),
				'default' => false
			),
		),
	);
}

function wikiplugin_textlink($data, $params)
{
    global $tikilib, $headerlib, $feedItem, $caching, $page;
    static $textlinkI = 0;
	++$textlinkI;
	$i = $textlinkI;
	
	$params = array_merge(array("forwardlink" => ""), $params);
	extract($params, EXTR_SKIP);
	
	$forwardlink = json_decode(stripslashes(trim(urldecode($forwardlink))));
	if(empty($forwardlink)) return $data;
	
	$forwardlink->href = urldecode($forwardlink->href);
	$forwardlink->serial = urldecode($forwardlink->serial);

	//$feed = Feed_Remote_ForwardLink::forwardlink($forwardlink);
	
	$result = Feed_Remote_ForwardLink_Contribution::send(array(
		"page"=> $page,
		"href"=> $forwardlink->href,
		"textlinkBody"=> $data,
		"textlinkHref"=> $tikilib->tikiUrl() . 'tiki-index.php?page=' . $page
	));
	
	//print_r($result);
	
	if (!empty($forwardlink->href)) {
    	return $data."~np~<a href='" .$forwardlink->href ."'>*</a>~/np~";
	} else {
    	return $data;
    }
}
