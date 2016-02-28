<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_file_list()
{
	return array(
		'file_galleries_comments_per_page' => array(
			'name' => tra('Default number per page'),
            'description' => tra('Number of comments per page'),
			'type' => 'text',
			'size' => '5',
			'default' => 10,
		),
		'file_galleries_comments_default_ordering' => array(
			'name' => tra('Default ordering'),
            'description' => tra('default ordering algorithm'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
			'default' => 'points_desc',
		),
		'file_galleries_use_jquery_upload' => array(
			'name' => tra('Use jQuery Upload'),
            'description' => tra('Use the improved Tiki 15+ upload page'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('experimental'),
			'dependencies' => array(
				'feature_file_galleries',
				'feature_jquery_ui',
			),
		),
	);
}
