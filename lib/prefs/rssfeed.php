<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_rssfeed_list() {
	return array(
		'rssfeed_default_version' => array(
			'name' => tra('Default feed format'),
			'type' => 'list',
			'options' => array(
				'2' => tra('RSS 2.0'),
				'5' => tra('ATOM 1.0'),
			),
		),
		'rssfeed_language' => array(
			'name' => tra('Language'),
			'type' => 'text',
			'size' => '10',
		),
		'rssfeed_img' => array(
			'name' => tra('Image path'),
			'type' => 'text',
			'size' => '40',
		),
	);
}

