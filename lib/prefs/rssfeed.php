<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_rssfeed_list() {
	return array(
		'rssfeed_default_version' => array(
			'name' => tra('Default RDF version'),
			'type' => 'list',
			'options' => array(
				'9' => tra('RSS 0.91'),
				'1' => tra('RSS 1.0'),
				'2' => tra('RSS 2.0'),
				'3' => tra('PIE0.1'),
				'4' => tra('MBOX'),
				'5' => tra('ATOM 0.3'),
				'6' => tra('OPML'),
				'7' => tra('HTML'),
				'8' => tra('JS'),
			),
		),
		'rssfeed_language' => array(
			'name' => tra('Language'),
			'type' => 'text',
			'size' => '10',
		),
		'rssfeed_editor' => array(
			'name' => tra('Editor'),
			'type' => 'text',
			'size' => '40',
		),
		'rssfeed_webmaster' => array(
			'name' => tra('Webmaster'),
			'type' => 'text',
			'size' => '40',
		),
		'rssfeed_img' => array(
			'name' => tra('Image path'),
			'type' => 'text',
			'size' => '40',
		),
	);
}

