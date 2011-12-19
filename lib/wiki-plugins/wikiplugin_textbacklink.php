<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_textbacklink_info()
{
	return array(
		'name' => tra('TextBacklink'),
		'documentation' => 'PluginTextBacklink',
		'description' => tra('Creates a linkable part of a page using textbacklink protocol'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_textbacklink', 'feature_forwardlinkprotocol' ),
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

function wikiplugin_textbacklink($data, $params)
{
    global $tikilib, $headerlib, $feedItem, $caching, $page;
    static $textbacklinkI = 0;
	++$textbacklinkI;
	
	$params = array_merge(array("name" => ""), $params);
	
	extract($params, EXTR_SKIP);
	
	if ($caching == true) {
		//here we are building a list of items that can be linked to
		$feed = new Feed_TextBacklink();
		$data = TikiLib::lib("parser")->parse_data($data);
		
		$feedItem['description'] = $data;
		$feedItem['name'] = (!empty($name) ? $name : $feedItem['name'] . ' ' . $textbacklinkI);;
		
		$feed->addItem($feedItem);
	}
	
	$contributions = json_decode( Feed_TextBacklink_Contribution::textbacklink($name)->getContents() );
	
	foreach($contributions->entry as $key => $item) {
    	$data = "~np~<a href='$item->href' title='$item->name' class='textlink$textbacklinkI' data-key='$key'>*</a>~/np~" . $data;
	}
	
	$headerlib->add_jq_onready("
		$('#textBacklink$textbacklinkI').click(function() {
			alert('Html: " . $tikilib->tikiUrl() . "tiki-feed?type=textbacklink        Name: $name');
		});
		
		
		var textlinks = " . json_encode($contributions->entry) . ";
		$('.textlink$textbacklinkI').click(function() {
			var textlink = textlinks[$(this).data('key')];
			var popup = $('<div>' +
				'<table>' +
					'<tr>' +
						'<th>Href</th>' +
						'<th>Name</th>' +
					'</tr>' +
					'<tr>' +
						'<td><a>' + textlink['href'] + '</a></td>' +
						'<td>' + textlink['name'] + '</td>' +
					'</tr>' +
				'</table>' +
			'</div>').dialog({title: textlink['name']});
			
			popup.find('a').attr('href', textlink['href']);
			
			return false;
		});
	");
	return $data . "~np~<br /><input id='textBacklink" . $textbacklinkI . "' type='button' value='" . tr("Create TextBacklink") .  "' />~/np~";
}
