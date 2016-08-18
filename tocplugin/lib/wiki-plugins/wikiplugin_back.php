<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_back_info()
{
	return array(
		'name' => tra('Back'),
		'documentation' => 'PluginBack',
		'description' => tra('Display a link that goes back one page in the browser history'),
		'prefs' => array( 'wikiplugin_back' ),
		'iconname' => 'back',
		'tags' => array( 'basic' ),
		'introduced' => 3,
		'params' => array(),
		);
}

function wikiplugin_back($data, $params)
{
	global $tikilib;
	
	// Remove first <ENTER> if exists...
	// if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
	
	extract($params, EXTR_SKIP);

	$begin = "<a href=\"javascript:history.go(-1)\">";

	$content = tra('Back');

	$end = "</a>";
	
	return "~np~" . $begin . $content  . $end . "~/np~"; 
}
