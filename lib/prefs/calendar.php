<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_calendar_list()
{
	return [
		'calendar_view_days' => [
			'name' => tra('Days to display in the calendar'),
			'type' => 'multicheckbox',
			'options' => [
				0 => tra('Sunday'),
				1 => tra('Monday'),
				2 => tra('Tuesday'),
				3 => tra('Wednesday'),
				4 => tra('Thursday'),
				5 => tra('Friday'),
				6 => tra('Saturday'),
			],
			'default' => [0,1,2,3,4,5,6],
		],
		'calendar_view_mode' => [
			'name' => tra('Default view mode'),
			'description' => '',
			'type' => 'list',
			'options' => [
				'day' => tra('Day'),
				'week' => tra('Week'),
				'month' => tra('Month'),
				'quarter' => tra('Quarter'),
				'semester' => tra('Semester'),
				'year' => tra('Year'),
			],
			'default' => 'month',
			'tags' => ['basic'],
		],
		'calendar_list_begins_focus' => [
			'name' => tra('View list begins'),
			'description' => '',
			'type' => 'list',
			'options' => [
				'y' => tra('Focus date'),
				'n' => tra('Period beginning'),
			],
			'default' => 'n',
		],
		'calendar_firstDayofWeek' => [
			'name' => tra('First day of the week'),
			'description' => '',
			'type' => 'list',
			'options' => [
				'0' => tra('Sunday'),
				'1' => tra('Monday'),
				'user' => tra('Depends user language'),
			],
			'default' => 'user',
		],
		'calendar_timespan' => [
			'name' => tra('Split hours in periods of'),
			'description' => '',
			'type' => 'list',
			'units' => tra('minutes'),
			'options' => [
				'1' => tra('1'),
				'5' => tra('5'),
				'10' => tra('10'),
				'15' => tra('15'),
				'30' => tra('30'),
			],
			'default' => '30',
		],
		'calendar_start_year' => [
			'name' => tra('First year in the dropdown'),
			'units' => tra('years'),
			'description' => '',
			'type' => 'text',
			'size' => '5',
			'hint' => tra('Enter a year or use +/- N to specify a year relative to the current year. Year selection is valid when the JS Calendar __is not__ enabled'),
			'default' => '-3',
		],
		'calendar_end_year' => [
			'name' => tra('Last year in the dropdown'),
			'description' => '',
			'units' => tra('years'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('Enter a year or use +/- N to specify a year relative to the current year'),
			'default' => '+5',
		],
		'calendar_sticky_popup' => [
			'name' => tra('Sticky popup'),
			'description' => '',
			'type' => 'flag',
			'default' => 'n',
		],
		'calendar_view_tab' => [
			'name' => tra('Item view tab'),
			'description' => '',
			'type' => 'flag',
			'default' => 'n',
		],
		'calendar_addtogooglecal' => [
			'name' => tra('Show "Add to Google Calendar" icon'),
			'description' => '',
			'type' => 'flag',
			'dependencies' => [
				'wikiplugin_addtogooglecal'
			],
			'default' => 'n',
		],
		'calendar_export' => [
			'name' => tra('Show "Export Calendars" button'),
			'description' => '',
			'type' => 'flag',
			'default' => 'n',
		],
		'calendar_export_item' => [
			'name' => tra('Show "Export Calendar Item" Button'),
			'description' => tra('Allow exporting a single calendar event as an iCal file'),
			'type' => 'flag',
			'default' => 'n',
		],
		'calendar_fullcalendar' => [
			'name' => tra('Use FullCalendar to display calendars'),
			'description' => '',
			'type' => 'flag',
			'default' => 'n',
		],
		'calendar_description_is_html' => [
			'name' => tra('Treat calendar item descriptions as HTML'),
			'description' => tra('Use this if you use the WYSIWYG editor for calendars. This is to handle legacy data from Tiki pre 7.0.'),
			'type' => 'flag',
			'default' => 'y',
		],
		'calendar_watch_editor' => [
			'name' => tra('Enable watch events when you are the editor'),
			'description' => tra('Check this to receive email notifications of events you changed yourself.'),
			'type' => 'flag',
			'default' => 'y',
		],
	];
}
