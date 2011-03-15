<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * $Header$
 *
 * Tiki-Wiki BACK plugin.
 * 
 * Syntax:
 * 
 *  {BACK()}{BACK}
 * 
 */
function wikiplugin_back_help() {
	return tra("Insert back link on wiki page").":<br />~np~{BACK()/}~/np~";
}

function wikiplugin_back_info() {
	return array(
		'name' => tra('Back'),
		'documentation' => 'PluginBack',
		'description' => tra('Displays a link that goes back one page in the browser history'),
		'prefs' => array( 'wikiplugin_back' ),
		'icon' => 'pics/icons/arrow_left.png',
		'params' => array(),
		);
}

function wikiplugin_back($data, $params) {
	global $tikilib;
	
	// Remove first <ENTER> if exists...
	// if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
	
	extract ($params,EXTR_SKIP);

	$begin = "<a href=\"javascript:history.go(-1)\">";

	$content = tra('Back');

	$end = "</a>";
	
	return "~np~" . $begin . $content  . $end . "~/np~"; 
}
