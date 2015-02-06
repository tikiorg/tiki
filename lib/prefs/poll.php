<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_poll_list()
{
	return array(
		'poll_comments_per_page' => array(
			'name' => tra('Default number per page'),
            'description' => tra('number of comments to show for poll pages'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 10,
		),
		'poll_comments_default_ordering' => array(
			'name' => tra('Default Ordering'),
            'description' => tra('Poll ordering algorithm (default=Points)'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
			'default' => 'points_desc',
		),
		'poll_list_categories' => array(
			'name' => tra('Show categories'),
            'description' => tra(''),
			'type' => 'flag',
			'dependencies' => array(
				'feature_categories',
			),
			'default' => 'n',
		),
		'poll_list_objects' => array(
			'name' => tra('Show objects'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'poll_multiple_per_object' => array(
			'name' => tra('Multiple polls per object'),
			'description' => tra('When used with the rating features, allow multiple polls to be attached to a single object.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'poll_surveys_textarea_hidetoolbar' => array(
			'name' => tra('Disable textarea toolbar'),
			'description' => tra('Hide toolbar for textarea fields in surveys.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
