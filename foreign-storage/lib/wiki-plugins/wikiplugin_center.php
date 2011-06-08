<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_center_info() {
	return array(
		'name' => tra('Center'),
		'documentation' => 'PluginCenter',
		'description' => tra('Center text'),
		'prefs' => array('wikiplugin_center'),
		'body' => tra('text'),
		'icon' => 'pics/icons/text_align_center.png',
		'params' => array(
		),
	);
}

function wikiplugin_center($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);
	$data = '<div align="center">' . trim($data). '</div>';
	return $data;
}
