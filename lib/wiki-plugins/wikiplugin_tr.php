<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_tr_info() {
	return array(
		'name' => tra('Translate'),
		'documentation' => 'PluginTR',
		'description' => tra('Translate text to the user language'),
		'prefs' => array( 'wikiplugin_tr' ),
		'body' => tra('string'),
		'icon' => 'pics/icons/page_translate_to.png',
		'params' => array(
		),
	);
}

function wikiplugin_tr($data) {
	return tra($data);
}
