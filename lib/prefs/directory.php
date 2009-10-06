<?php

function prefs_directory_list() {
	return array(
		'directory_country_flag' => array(
			'name' => tra('Show Country Flag'),
			'description' => tra('Show the country flag'),
			'type' => 'flag',
		),
		'directory_cool_sites' => array(
			'name' => tra('Enable cool sites'),
			'description' => tra(''),
			'type' => 'flag',
		),
		'directory_validate_urls' => array(
			'name' => tra('Validate URLs'),
			'description' => tra(''),
			'type' => 'flag',
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
                       		'6' => tra('6'),
				),
			),
		'directory_links_per_page' => array(
			'name' => tra('Links per page'),
			'description' => tra(''),
			'type' => 'text',
			),
		'directory_open_links' => array(
                       'name' => tra('Method to Open Directory Links'),
			'description' => tra('Method to open directory links'),
                       'type' => 'list',
        	       'options' => array(
	                        'r' => tra('replace current window'),
               		        'n' => tra('new window'),
                       		'f' => tra('inline frame'),
				),
			),
	);
}
