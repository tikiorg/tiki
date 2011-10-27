<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_htmlfeedlink_info() {
	return array(
		'name' => tra('Html Feed Link'),
		'documentation' => 'PluginHtmlFeedLink',
		'description' => tra('Display remote content'),
		'prefs' => array('wikiplugin_htmlfeedlink'),
		'body' => tra('Initial text'),
		'icon' => 'pics/icons/page_white_code.png',
		'filter' => 'rawhtml_unsafe',
		'tags' => array( 'basic' ),	
		'params' => array(
			'url' => array(
				'required' => false,
				'name' => tra('Url of content'),
				'description' => tra(''),
			),
			'name' => array(
				'required' => false,
				'name' => tra('Name of content'),
				'description' => tra(''),
			),
			'style' => array(
				'required' => false,
				'name' => tra('Style of content'),
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Highlight'), 'value' => 'highlight'),
					array('text' => tra('Asterisk'), 'value' => 'asterisk'),
				),
			),
			'clickinfo' => array(
				'required' => false,
				'name' => tra('Click Info'),
				'description' => tra('y/n. Show info when clicked "y"'),
				'filter' => 'alpha',
				'accepted' => 'y or n',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_htmlfeedlink($data, $params) {
	global $tikilib, $headerlib, $page, $cachebuild;
	static $htmlFeedLinkI = 0;
	++$htmlFeedLinkI;
	$i = $htmlFeedLinkI;
	
	$params = array_merge(array(
		"url" => "",
		"name" => "",
		"style" => "",
		"name" => "",
		"clickinfo" => "y"
		
	), $params);
	
	extract ($params,EXTR_SKIP);
	
	$link = "{}";
	$links = "[]";
	
	if (empty($url) || empty($name)) return $data;
	if (isset($cachebuild)) return $data;
	
	$tbl = new HtmlFeed_Remote($url, $name);
	$links = $tbl->listLinkNames();
	$link = $tbl->getLink($name);
	
	$link->clickinfo = $clickinfo;
	
	if (!empty($link->name)) {
		$name = $link->name;
		$data = $link->description;
		$link = json_encode($link);
	}
	
	$headerlib->add_js("
		if (!$.fn.makeBacklink) {
			$.fn.makeBacklink = function(s) {
				$(this).each(function() {
					$(this)
						.css('cursor', 'pointer')
						.hover(function(){
							$(this).css('background-color', 'yellow');
						},function(){
							$(this).css('background-color', '');
						});
					if (s.clickinfo) {
						$(this).click(function() {
							$('<div>' +
								s.description +
							'</div>')
								.dialog({
									title: s.name
								});
						});
					}
				});
				return this;
			};
		}
		
		$(document)
			.unbind('plugin_htmlfeedlink_ready')
			.bind('plugin_htmlfeedlink_ready', function(e) {
				var name = $(e.container).find('#param_name input:first');
				name.hide();
				var nameSelect = $('<select>')
					.insertAfter(name)
					.change(function() {
						name.val($(this).val());
					})
					.change();
				
				var links = " . json_encode($links) . ";
				
				for(var i = 0; i < links.length; i++) {
					$('<option />')
						.val(links[i])
						.text(links[i])
						.appendTo(nameSelect);
				}
			});
	");
	
	$headerlib->add_jq_onready("
		$('#backlink$i')
			.makeBacklink(" . $link . ");
	");
	
	$result = "<span id='backlink$i' title='$name'>". $data ."</span>";
	
	switch ($style) {
		case "highlight":
			$headerlib->add_jq_onready("
				$('#backlink$i')
					.css('border', '1px solid red');
			");
			break;
		case "asterisk":
			$result = "<sup>*</sup>" . $result;
			break;
	}
	
	return  $result;
}