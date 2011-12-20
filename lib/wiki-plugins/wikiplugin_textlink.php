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
		'description' => tra('Links your article to a site using textbacklink protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_textlink', 'feature_forwardlinkprotocol' ),
		'icon' => 'pics/icons/link.png',
		'params' => array(			
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'default' => false
			),
			'href' => array(
				'required' => true,
				'name' => tra('Href'),
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
	
	$params = array_merge(array("href" => ""), $params);
	
	extract($params, EXTR_SKIP);
	
	if (empty($href) || empty($name)) return tr("Ensure name and href are set.");
	
	$feed = Feed_Remote_ForwardLink::href($href);
	
	$href = parse_url($href);
	
	Feed_Remote_ForwardLink_Contribution::sendItem(array(
		'href'=> $href['scheme'] . "://" . $href['host'] . $href['path'],
		'pageName'=> $page,
		'linkName'=> $page . $textlinkI,
		'description'=> TikiLib::lib("parser")->parse_data($data),
		'originName'=> $name
	));

	$item = $feed->getItem($name);
	
	if (!empty($item->href)) {
    	return "<a href='$item->href'>$data</a>";
	} else {
    	return $data;
    }
}
