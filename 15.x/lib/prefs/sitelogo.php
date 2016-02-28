<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_sitelogo_list()
{
	return array(
		'sitelogo_src' => array(
			'name' => tra('Logo source (image path)'),
			'type' => 'text',
			'default' => 'img/tiki/Tiki_WCG.png',
			'tags' => array('basic'),
		),
		'sitelogo_bgcolor' => array(
			'name' => tra('Logo background color'),
			'hint' => tra('Examples:') . ' ' .  '1) silver - 2) #fff',
			'type' => 'text',
			'size' => '15',
			'default' => 'transparent',
			'tags' => array('basic'),
		),
		'sitelogo_title' => array(
			'name' => tra('Logo title (on mouseover)'),
			'type' => 'text',
			'size' => '50',
			'default' => 'Tiki powered site',
			'tags' => array('basic'),
		),
		'sitelogo_alt' => array(
			'name' => tra('HTML "alt" tag description (for text browsers, screen readers, etc.)'),
			'type' => 'text',
			'size' => '50',
			'default' => 'Site Logo',
			'tags' => array('basic'),
		),
		'sitelogo_icon' => array(
			'name' => tra('Site icon'),
			'hint' => tra('Recommended image height: 32 pixel'),
			'type' => 'text',
			'default' => 'img/tiki/tikilogo_icon.png',
			'tags' => array('basic'),
		),
	);	
}
