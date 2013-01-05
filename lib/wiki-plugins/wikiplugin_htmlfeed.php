<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_htmlfeed_info()
{
	return array(
		'name' => tra('Textlink'),
		'documentation' => 'PluginHtmlFeed',
		'description' => tra('Adds an item to HTML feed'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_htmlfeed' , 'feature_htmlfeed'),
		'icon' => 'img/icons/link.png',
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
    global $tikilib, $headerlib, $feedItem, $caching, $page;
    static $feedhtmlFeedI = 0;
	++$feedhtmlFeedI;
	
	$params = array_merge(array("name" => ""), $params);
	
	extract($params, EXTR_SKIP);
	
	if ($caching == true) {
		$htmlFeed = new Feed_Html();
		$data = TikiLib::lib("parser")->parse_data($data);
		
		$feedItem->data = $data;
		$feedItem->name = (!empty($name) ? $name : $feedItem->name . ' ' . $feedhtmlFeedI);;
		
		$htmlFeed->addItem($feedItem);
	}
	
    return $data;
}
