<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_sub.php,v 1.6 2005-05-18 11:02:00 mose Exp $

// Wiki plugin to output <sub>...</sub>
// - rlpowell

function wikiplugin_sub_help() {
        return tra("Displays text in subscript.").":<br />~np~{SUB()}text{SUB}~/np~";
}

function wikiplugin_sub_info() {
	return array(
		'name' => tra( 'Subscript' ),
		'documentation' => 'PluginSub',		
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
