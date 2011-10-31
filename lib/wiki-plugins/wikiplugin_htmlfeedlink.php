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
		'body' => tra('Initial Value'),
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
			'type' => array(
				'required' => false,
				'name' => tra('Html Feed Link Type'),
				'default' => 'replace',
				'options' => array(
					array('text' => tra('Replace'), 'value' => 'replace'),
					array('text' => tra('Backlink'), 'value' => 'backlink'),
					array('text' => tra('Popup'), 'value' => 'popup'),
					array('text' => tra('Hover'), 'value' => 'hover'),
				),
			),
			'moderate' => array(
				'required' => false,
				'name' => tra('Is the html feed moderated'),
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
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
		"type" => "replace",
		"moderate" => "y",
		"style" => "",
		"name" => ""
	), $params);
	
	extract ($params,EXTR_SKIP);
	
	if (empty($url)) return $data;
	if (isset($cachebuild)) return $data;
	
	$htmlFeed = new HtmlFeed_Remote($url);
	
	$headerlib->add_jq_onready("
		if (!$.fn.htmlFeedPopup) {
			$.fn.htmlFeedPopup = function(s) {
				$(this).each(function() {
					$(this)
						.css('cursor', 'pointer')
						.hover(function(){
							$(this).css('background-color', 'yellow');
						},function(){
							$(this).css('background-color', '');
						})
						.click(function() {
							$('<div>' +
								s.description +
							'</div>')
								.dialog({
									title: s.name
								});
						});
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
				
				var links = " . json_encode($htmlFeed->listLinkNames()) . ";
				
				for(var i = 0; i < links.length; i++) {
					$('<option />')
						.val(links[i])
						.text(links[i])
						.appendTo(nameSelect);
				}
			});
	");
	
	$link = $htmlFeed->getLink($name);
	
	if (!empty($link->name)) {
		$name = $link->name;
		switch($type) {
			case "replace":
				$data = $link->description;
				break;
			case "backlink":
				$data = "<a href='$link->url'>" . $data . "</a>";
				break;
			case "popup":
				$headerlib->add_jq_onready("
					$('#backlink$i')
						.htmlFeedPopup(" . $link . ");
				");
				break;
			case "hover": break;
		}
		
		$link = json_encode($link);
	}
	
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