<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_directory_list()
{
	return array(
		'directory_country_flag' => array(
			'name' => tra('Show country flag'),
			'description' => tra('Show the country flag'),
			'type' => 'flag',
			'default' => 'y',
		),
		'directory_cool_sites' => array(
			'name' => tra('Enable "popular sites"'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
		),
		'directory_validate_urls' => array(
			'name' => tra('Validate URLs'),
			'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'directory_columns' => array(
			'name' => tra('Number of columns per page when listing directory categories'),
			'description' => tra('Number of columns per page when listing directory categories'),
			'type' => 'list',
			'options' => array(
                '1' => tra('1'),
                '2' => tra('2'),
                '3' => tra('3'),
                '4' => tra('4'),
                '5' => tra('5'),
                '6' => tra('6')),
			'default' => 3,
			),
		'directory_links_per_page' => array(
			'name' => tra('Links per page'),
            'description' => tra(''),
			'type' => 'text',
			'default' => 20,
			),
		'directory_open_links' => array(
            'name' => tra('Method to open Directory links'),
			'description' => tra('The linked-to website can be opened in various ways'),
            'type' => 'list',
        	'options' => array(
	            'r' => tra('Replace the current window'),
                'n' => tra('Open a new window'),
                'f' => tra('Open an iframe')),
			'default' => 'n',
			),
	);
}
