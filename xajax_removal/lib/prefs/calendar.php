<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_calendar_list() {
	return array(
		'calendar_view_days' => array(
			'name' => tra('Days to display in the Calendar'),
			'type' => 'multicheckbox',
			'options' => array( 
				0 => tra('Sunday'),
				1 => tra('Monday'),
				2 => tra('Tuesday'),
				3 => tra('Wednesday'),
				4 => tra('Thursday'),
				5 => tra('Friday'),
				6 => tra('Saturday'),
			)
		),
		'calendar_view_mode' => array(
			'name' => tra('Default view mode'),
			'type' => 'list',
			'options' => array(
				'day' => tra('Day'),
				'week' => tra('Week'),
				'month' => tra('Month'),
				'quarter' => tra('Quarter'),
				'semester' => tra('Semester'),
				'year' => tra('Year'),
			),
		),
		'calendar_list_begins_focus' => array(
			'name' => tra('View list begins'),
			'type' => 'list',
			'options' => array(
				'y' => tra('Focus Date'),
				'n' => tra('Period beginning'),
			),
		),
		'calendar_firstDayofWeek' => array(
			'name' => tra('First day of the week'),
			'type' => 'list',
			'options' => array(
				'0' => tra('Sunday'),
				'1' => tra('Monday'),
				'user' => tra('Depends user language'),
			),
		),
		'calendar_timespan' => array(
			'name' => tra('Split hours in periods of'),
			'type' => 'list',
			'options' => array(
				'1' => tra('1 minute'),
				'5' => tra('5 minutes'),
				'10' => tra('10 minutes'),
				'15' => tra('15 minutes'),
				'30' => tra('30 minutes'),
			),
		),
		'calendar_start_year' => array(
			'name' => tra('First year in the dropdown'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('Enter a year or use +/- N to specify a year relative to the current year'),
		),
		'calendar_end_year' => array(
			'name' => tra('Last year in the dropdown'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('Enter a year or use +/- N to specify a year relative to the current year'),
		),
		'calendar_sticky_popup' => array(
			'name' => tra('Sticky popup'),
			'type' => 'flag',
		),
		'calendar_view_tab' => array(
			'name' => tra('Item view tab'),
			'type' => 'flag',
		),
		'calendar_addtogooglecal' => array(
			'name' => tra('Show Add to Google Calendar icon'),
			'type' => 'flag',
			'dependencies' => array(
				'wikiplugin_addtogooglecal'
			),
		),
	);
}
