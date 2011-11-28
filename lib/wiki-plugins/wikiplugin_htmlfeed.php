<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_htmlfeed_info()
{
	return array(
		'name' => tra('Textlink'),
		'documentation' => 'PluginTextlink',
		'description' => tra('Creates a linkable part of a page using textbacklink protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_htmlfeed' ),
		'icon' => 'pics/icons/link.png',
		'params' => array(			
			'name' => array(
				'required' => true,
				'name' => tra('Name'),
				'default' => false
			),
		),
	);
}

function wikiplugin_htmlfeed($data, $params)
{
    global $tikilib, $headerlib, $htmlFeedItem, $caching, $page;
    static $feedhtmlFeedI = 0;
	++$feedhtmlFeedI;
	
	$params = array_merge(array("name" => ""), $params);
	
	extract($params, EXTR_SKIP);
	
	if ($caching == true) {
		$htmlFeed = new Feed_Html();
		$data = TikiLib::lib("parser")->parse_data($data);
		
		$htmlFeedItem['description'] = $data;
		$htmlFeedItem['name'] = (!empty($name) ? $name : $htmlFeedItem['name'] . ' ' . $feedhtmlFeedI);;
		
		$htmlFeed->addItem($htmlFeedItem);
	}
	
    return $data;
}
