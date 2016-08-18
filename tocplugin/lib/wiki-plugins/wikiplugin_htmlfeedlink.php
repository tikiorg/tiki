<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_htmlfeedlink_info()
{
	return array(
		'name' => tra('HTML Feed Link'),
		'documentation' => 'PluginHtmlFeedlink',
		'description' => tra('Receive and display content from another site sent using PluginHTMLFeed'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_htmlfeedlink', 'feature_htmlfeed' ),
		'body' => tra('Initial Value'),
		'iconname' => 'link',
		'filter' => 'rawhtml_unsafe',
		'tags' => array( 'basic' ),
		'introduced' => 9,
		'params' => array(
			'feed' => array(
				'required' => false,
				'name' => tra('Feed Location'),
				'description' => tra(''),
				'since' => '9.0',
			),
			'name' => array(
				'required' => false,
				'name' => tra('Content Name'),
				'description' => tra(''),
				'since' => '9.0',
			),
			'style' => array(
				'required' => false,
				'name' => tra('Content Style'),
				'since' => '9.0',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Highlight'), 'value' => 'highlight'),
					array('text' => tra('Asterisk'), 'value' => 'asterisk'),
				),
			),
			'type' => array(
				'required' => false,
				'name' => tra('HTML Feed Link Type'),
				'since' => '9.0',
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
				'name' => tra('Moderated?'),
				'since' => '9.0',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'description' => tr('Date of last accepted HTML item, not used if not moderated'),
				'since' => '9.0',
				'default' => '',
			),
		),
	);
}

function wikiplugin_htmlfeedlink($data, $params)
{
	global $page, $caching;
	$headerlib = TikiLib::lib('header');
	$tikilib = TikiLib::lib('tiki');

	static $htmlFeedLinkI = 0;
	++$htmlFeedLinkI;

	$params = array_merge(
		array(
			"feed" => "",
			"name" => "",
			"type" => "replace",
			"moderate" => "y",
			"style" => "",
			"date" => ""
		),
		$params
	);

	extract($params, EXTR_SKIP);

	if (empty($feed)) return $data;
	if (isset($caching)) return $data; //caching is running, if no return, causes recursive parsing

	$htmlFeed = new Feed_Html_Receive($feed);

	$headerlib->add_jq_onready(
    	"if (!$.fn.htmlFeedPopup) {
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
						name.val($(this).val()).change();
					});

				var items = " . json_encode($htmlFeed->listItemNames()) . ";

				for(var i = 0; i < items.length; i++) {
					$('<option />')
						.val(items[i])
						.text(items[i])
						.appendTo(nameSelect);
				}
				nameSelect.val(name.val()).change();
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
		});"
	);

	$item = $htmlFeed->getItem($name);
	$same = $date == $item->date;

	if (!empty($item->name)) {
		$name = $item->name;
		$date = $item->date;
		switch($type) {
			case "":
			case "replace":
				$data = "~np~" . $item->data . "~/np~";
				//moderate isn't yet working
				if ($moderate == 'y') {
					if ($same == false) {
						$data .= "~np~<img
							src='img/icons/flag_blue.png'
							class='revision'
							title='Revision Available, click to see'
							style='cursor: pointer;'
							data-feed='".urlencode($feed)."'
							data-name='".urlencode($name)."'
							/>
							<form id='form$htmlFeedLinkI' method='post' action='tiki-wikiplugin_edit.php' style='display: none;'>
								<input type='hidden' name='page' value='$page'/>
								<input type='hidden' name='index' value='$htmlFeedLinkI'/>
								<input type='hidden' name='type' value='htmlfeedlink'/>
								<input type='hidden' name='params[name]' value='".htmlspecialchars($name)."'/>
								<input type='hidden' name='params[feed]' value='".htmlspecialchars($feed)."'/>
								<input type='hidden' name='params[type]' value='".htmlspecialchars($type)."'/>
								<input type='hidden' name='params[style]' value='".htmlspecialchars($style)."'/>
								<input type='hidden' name='params[date]' value='".htmlspecialchars($date)."'/>
								<input type='hidden' name='content' value='".htmlspecialchars($data)."'/>
							</form>
							~/np~";
					} else {
							$data = "~np~" . $item->description . "~/np~";
					}
				} else {
					$data = $item->description;
				}
    			break;
			case "backlink":
				$data = "<a href='$item->url'>" . $data . "</a>";
    			break;
			case "popup":
				$headerlib->add_jq_onready(
    				"$('#backlink')
						.htmlFeedPopup(" . $link . ");"
				);
    			break;
			case "hover":
    			break;
		}

		$link = json_encode($link);
	}

	$result = "<span id='htmlFeedLink' title='$name'>". $data ."</span>";

	switch ($style) {
		case "highlight":
			$headerlib->add_jq_onready(
    			"$('#htmlFeedLink$htmlFeedLinkI')
					.css('border', '1px solid red');"
			);
    		break;
		case "asterisk":
			$result = "<sup>*</sup>" . $result;
    		break;
	}

	$archives = "";
	foreach ($htmlFeed->getItemFromDates($item->name) as $archive) {
		$archives .= "<a href='tiki-html_feed.php?feed=".$feed.
			"&name=".urlencode($archive->name).
			"&date=".urlencode($archive->date)."'>". htmlspecialchars($archive->name) ." ". htmlspecialchars($archive->date) . "</a><br />";
	}

	if (strlen($archives) > 0) {
		$result .= "~np~<img src='img/icons/disk_multiple.png' id='viewArchives$htmlFeedLinkI' title='View Archives' name='".htmlspecialchars($archive->name)."' style='cursor: pointer;' />
		<div id='archives$htmlFeedLinkI' style='display: none;' >" . $archives . "</div>~/np~";
		$headerlib->add_jq_onready(
<<<JQ
			$('#viewArchives$htmlFeedLinkI').click(function() {
				$('#archives$htmlFeedLinkI')
					.dialog({title: "Revisions for " + $(this).attr('name')})
					.find('a').click(function() {
						$.getJSON($(this).attr('href'), function(item) {
							$('<div>')
								.html(item.description)
								.dialog();
						});
						return false;
					});
			});
JQ
		);
	}

	return  $result;
}
