<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to output <sub>...</sub>
// - rlpowell

function wikiplugin_sub_help() {
        return tra("Displays text in subscript.").":<br />~np~{SUB()}text{SUB}~/np~";
}

function wikiplugin_sub_info() {
	return array(
		'name' => tra( 'Subscript' ),
		'documentation' => tra('PluginSub'),		
		'description' => tra('Displays text in subscript.'),
		'prefs' => array( 'wikiplugin_sub' ),
		'body' => tra('text'),
		'icon' => 'pics/icons/text_subscript.png',
		'params' => array(
		),
	);
}

function wikiplugin_sub($data, $params)
{
        global $tikilib;

        extract ($params,EXTR_SKIP);
	return "<sub>$data</sub>";
}
