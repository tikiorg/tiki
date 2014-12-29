<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_display_list()
{
	return array(
		'display_field_order' => array(
			'name' => tra('Fields display order'),
            'description' => tra('The order date field inputs should be listed in.'),
			'type' => 'list',
			'options' => array(
				'DMY' => tra('Day') . ' ' . tra('Month') . ' ' . tra('Year'),
				'DYM' => tra('Day') . ' ' . tra('Year') . ' ' . tra('Month'),
				'MDY' => tra('Month') . ' ' . tra('Day') . ' ' . tra('Year'),
				'MYD' => tra('Month') . ' ' . tra('Year') . ' ' . tra('Day'),
				'YDM' => tra('Year')  . ' ' . tra('Day') . ' ' . tra('Month'),
				'YMD' => tra('Year')  . ' ' . tra('Month') . ' ' . tra('Day'),
			),
			'default' => 'MDY',
			'tags' => array('basic'),
		),
		'display_start_year' => array(
			'name' => tra('Start Year'),
            'description' => tra('Year to show as first on drop down year lists.') . '<br>' .
							tra('E.G. Use "-2" for the current year minus two, or "2003" for an explicit year'),
			'type' => 'text',
			'size' => 6,
			'default' => '-3',
		),
		'display_end_year' => array(
			'name' => tra('End Year'),
            'description' => tra('Year to show as last on drop down year lists.') . '<br>' .
							tra('E.G. Use "+2" for the current year plus two, or "2013" for an explicit year'),
			'type' => 'text',
			'size' => 6,
			'default' => '+1',
		),
	);
}
