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
			'feed' => array(
				'required' => false,
				'name' => tra('Feed location'),
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
	global $tikilib, $headerlib, $page, $caching;
	static $htmlFeedLinkI = 0;
	++$htmlFeedLinkI;
	$i = $htmlFeedLinkI;
	
	$params = array_merge(array(
		"feed" => "",
		"name" => "",
		"type" => "replace",
		"moderate" => "y",
		"style" => "",
	), $params);
	
	extract ($params,EXTR_SKIP);
	
	if (empty($feed)) return $data;
	if (isset($caching)) return $data; //caching is running, if no return, causes recursive parsing
	
	$htmlFeed = new HtmlFeed_Remote($feed);
	
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
					});
				
				var items = " . json_encode($htmlFeed->listItemNames()) . ";
				
				for(var i = 0; i < items.length; i++) {
					$('<option />')
						.val(items[i])
						.text(items[i])
						.appendTo(nameSelect);
				}
				nameSelect.val(name.val());
			});
			
		$('.revision').click(function() {
			$.getJSON('tiki-html_feed.php', {
				feed: $(this).data('feed'),
				name: $(this).data('name')
			}, function(link) {
				$('<div />')
					.html(link.description)
					.dialog({
						title: link.name,
						buttons: [{
							text: 'Accept Update',
							click: function () {
								$('#form$htmlFeedLinkI [name=\'content\']').val('~np~' + link.description + '~/np~')
								$('#form$htmlFeedLinkI').submit();
							}
						}]
					});
			});
		});
	");
	
	$item = $htmlFeed->getItem($name);
	
	if (!empty($item->name)) {
		$name = $item->name;
		switch($type) {
			case "replace":
				$same = levenshtein($data, $item->description) < 4 ? true : false;

				if (!$same)
					$data .= "~np~<img
						src='pics/icons/flag_blue.png'
						class='revision'
						title='Revision Available, click to see'
						style='cursor: pointer;'
						data-feed='".urlencode($feed)."'
						data-name='".urlencode($name)."'
						/>
						<form id='form$htmlFeedLinkI' method='post' action='tiki-wikiplugin_edit.php'>
							<input type='hidden' name='page' value='$page'/>
							<input type='hidden' name='index' value='$htmlFeedLinkI'/>
							<input type='hidden' name='type' value='htmlfeedlink'/>
							<input type='hidden' name='params[name]' value='$name'/>
							<input type='hidden' name='params[feed]' value='$feed'/>
							<input type='hidden' name='params[type]' value='$type'/>
							<input type='hidden' name='params[style]' value='$style'/>
							<input type='hidden' name='content' value='$data'/>
						</form>
						~/np~";
						
				break;
			case "backlink":
				$data = "<a href='$item->url'>" . $data . "</a>";
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
	
	$result = "<span id='htmlFeedLink$i' title='$name'>". $data ."</span>";
	
	switch ($style) {
		case "highlight":
			$headerlib->add_jq_onready("
				$('#htmlFeedLink$i')
					.css('border', '1px solid red');
			");
			break;
		case "asterisk":
			$result = "<sup>*</sup>" . $result;
			break;
	}
	
	return  $result;
}