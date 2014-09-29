<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_page_list()
{
	return array(
		'page_bar_position' => array(
			'name' => tra('Wiki buttons'),
			'description' => tra('Page description, icons, backlinks, ...'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top '),
				'bottom' => tra('Bottom'),
				'none' => tra('Neither'),
			),
			'default' => 'bottom',
		),
		'page_n_times_in_a_structure' => array(
			'name' => tra('Pages can re-occur in structure'),
            'description' => tra('A page can occur multiple times in a structure'),
			'type' => 'flag',
			'default' => 'n',
		),
		'page_content_fetch' => array(
			'name' => tra('Fetch page content from incoming feeds'),
			'description' => tra('Page content from the source will be fetched before sending the content to the generators'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
