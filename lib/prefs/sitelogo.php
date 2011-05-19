<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_sitelogo_list() {
	return array(
		'sitelogo_src' => array(
			'name' => tra('Logo source (image path)'),
			'type' => 'text',
			'default' => 'img/tiki/Tiki_WCG.png',
		),
		'sitelogo_bgcolor' => array(
			'name' => tra('Logo background color'),
			'hint' => tra('Examples:') . ' ' .  '1) silver - 2) #fff',
			'type' => 'text',
			'size' => '15',
			'default' => 'transparent',
		),
		'sitelogo_title' => array(
			'name' => tra('Logo title (on mouse over)'),
			'type' => 'text',
			'size' => '50',
			'default' => 'Tiki powered site',
		),
		'sitelogo_alt' => array(
			'name' => tra('Alt. description (e.g. for text browsers)'),
			'type' => 'text',
			'size' => '50',
			'default' => 'Site Logo',
		),
	);	
}
