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
	
	Feed_Remote_ForwardLink_Contribution::add(array(
		"page"=> $page,
		"forwardLink"=> $clipboarddata,
		"textlink"=> array(
			"body"=> $data,
			"href"=> $tikilib->tikiUrl() . "tiki-index.php?page=$page#textlink$i"
		)
	));
	
	$clipboarddata->date = $tikilib->get_short_date($clipboarddata->date);
	if (!empty($clipboarddata->href)) {
		$headerlib->add_jq_onready(<<<JQ
			$('#textlink$i').click(function() {
				var forwardLinkTable = $('<table>' + 
					'<tr>' +
						'<th>' + tr('Date') + '</th>' +
						'<th>' + tr('Click below to read Citing blocks') + '</th>' +
					'</tr>' +
					'<tr>' +
						'<td>$clipboarddata->date</td>' +
						'<td><a href="$clipboarddata->href" class="forwardLinkRead">Read</a></td>' + 
					'</tr>' +
				'</table>')
					.dialog();
				
				forwardLinkTable.find('.forwardLinkRead').click(function() {
					$('<form action="$clipboarddata->href" method="post">' + 
						'<input type="hidden" name="phrase" value="$clipboarddata->text" />' +
					'</form>')
						.appendTo('body')
						.submit();
					return false;
				});
				return false;
			});
JQ
		);
    	return "~np~<span class='textlink'>~/np~".$data."~np~</span><a href='" .$clipboarddata->href ."' id='textlink$i'>*</a>~/np~";
	} else {
    	return $data;
    }
}
