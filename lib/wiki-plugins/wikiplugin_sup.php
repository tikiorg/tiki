<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to output superscript <sup>...</sup>
// based on sub plugin

function wikiplugin_sup_help() {
        return tra("Displays text in superscript.").":<br />~np~{SUP()}text{SUP}~/np~";
}

function wikiplugin_sup_info() {
	return array(
		'name' => tra( 'Superscript' ),
		'documentation' => 'PluginSup',
		'description' => tra('Apply superscript font to text'),
		'prefs' => array( 'wikiplugin_sup' ),
		'body' => tra('text'),
		'icon' => 'pics/icons/text_superscript.png',
		'params' => array(
		),
	);
}

function wikiplugin_sup($data, $params)
{
        global $tikilib;

        extract ($params,EXTR_SKIP);
	return "<sup>$data</sup>";
}
