<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
Name:		SKYPE plugin for Tiki Wiki/CMS/Groupware. 
Description:	Creates an clickable link to call or chat with a Skype user. It will soon indicate if the person is online.
Author:		Marc Laporte
License:		LGPL
Version: 		1.0 ( 2006-07-25)

Syntax:
{SKYPE(action->call|chat,showstatus->y|n)}name or number{SKYPE}

Parameters:
action => call or chat (default chat)
showstatus-> yes or no (default no)

Todo:
add show status image.  Ex.: $ret = "<a href='skype:$data?$action' onclick='return skypeCheck();'><img src='http://mystatus.skype.com/smallicon/$data' style='border: none;' alt='My status' /></a>
solve problem for getting https image

Tip:
You can change your defaults

 */

function wikiplugin_skype_help() {
        return tra("Clickable Skype link").":<br />~np~{SKYPE(action->call|chat)}name or number{SKYPE}~/np~"
		      .tra("do not forget to check the tools / options/ privacy / allow my status to be show on the web");
}

function wikiplugin_skype_info() {
	return array(
		'name' => tra('Skype'),
		'documentation' => tra('PluginSkype'),
		'description' => tra('Clickable Skype link'),
		'prefs' => array( 'wikiplugin_skype' ),
		'body' => tra('Name or number to call or chat with.')." ".tra("do not forget to check the tools / options/ privacy / allow my status to be show on the web"),
		'params' => array(
			'action' => array(
				'required' => false,
				'name' => tra('Action'),
				'description' => tra('Set whether to call or chat. Default is chat'),
				'default' => 'chat',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Call'), 'value' => 'call'), 
					array('text' => tra('Chat'), 'value' => 'chat')
				)
			),
			'showstatus' => array(
				'required' => false,
				'name' => tra('Show status'),
				'description' => tra('Show a status icon or not i.e. if online/offline/etc. Default is no.'),
				'default' => 'n',
				'options' => array(
					array('text' => tra('yes'), 'value' => 'y'), 
					array('text' => tra('no'), 'value' => 'n')
				)
			),
		)
	);
}

function wikiplugin_skype($data, $params) {

	extract($params, EXTR_SKIP);
	
	if (empty($data)) {
		return ("<b>You need to add a Skype username</b><br />".
        "~np~{SKYPE()}username{SKYPE}~/np~");
	}
	
	if (!isset($action)) {
		$action = "chat";
	}
	if (!isset($showstatus)) {
		$showstatus = "n";
	}

	$ret = "<script type=\"text/javascript\" src=\"http://download.skype.com/share/skypebuttons/js/skypeCheck.js\"></script>";
	$ret.= "<a href='skype:$data?$action' onclick='return skypeCheck();'>";
	if ($showstatus == "y") {
		$ret.= "<img src=\"http://mystatus.skype.com/mediumicon/$data\" style=\"border: none;\" width=\"26\" height=\"26\" alt=\"".tra("click to")." ".tra($action)."\" />";
	} else {
		$ret.= tra($action)." ".$data;
	}
	$ret.= "</a>";	

	return $ret;
}
