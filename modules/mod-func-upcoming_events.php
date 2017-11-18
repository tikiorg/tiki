<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_upcoming_events_info()
{
	return [
		'name' => tra('Upcoming Events'),
		'description' => tra('Lists the specified number of calendar events, ordered by their start date.'),
		'prefs' => ["feature_calendar"],
		'params' => [
			'calendarId' => [
				'name' => tra('Calendars filter'),
				'description' => tra('If set to a list of calendar identifiers, restricts the events to those in the identified calendars. Identifiers are separated by vertical bars ("|"), commas (",") or colons (":").') . " " . tra('Example values:') . '"13", "4,7", "31:49". ' . tra('Not set by default.'),
				'profile_reference' => 'calendar',
			],
			'maxDays' => [
				'name' => tra('Maximum days in the future'),
				'description' => tra('Maximum distance to event start dates in days (looking forward).') . " " . tra('Example values:') . ' 7, 14, 31.' . " " . tra('Default:') . ' 365',
				'filter' => 'int'
			],
			'priorDays' => [
				'name' => tra('Maximum days in the past'),
				'description' => tra('Maximum distance to event end dates in days (looking backward).') . " " . tra('Example values:') . ' 7, 14, 31.' . " " . tra('Default:') . ' 0',
				'filter' => 'int'
			],
			'cellpadding' => [
				'name' => tra('cellpadding'),
				'description' => tra('If set to an integer, apply this cellpadding to the HTML table generated.'),
				'filter' => 'int'
			],
			'cellspacing' => [
				'name' => tra('cellspacing'),
				'description' => tra('If set to an integer, apply this cellspacing to the HTML table generated.'),
				'filter' => 'int'
			],
			'showDescription' => [
				'name' => tra('Show description'),
				'description' => tra('If set to "y", event descriptions are displayed.') . " " . tra('Default:') . ' "n"',
				'filter' => 'word'
			],
			'showEnd' => [
				'name' => tra('Show end date and time'),
				'description' => tra('If set to "y", event end dates and times are displayed, when appropriate.') . " " . tra('Default:') . ' "n"',
				'filter' => 'word'
			],
			'showColor' => [
				'name' => tra('Use custom calendar background colors'),
				'description' => tra('If set to "y", events are displayed with their calendar\'s custom background color (if one is set).') . " " . tra('Default:') . ' "n"',
				'filter' => 'word'
			],
			'tooltip_infos' => [
				'name' => tra('Show information in tooltips'),
				'description' => tra('If set to "n", event tooltips will not display event information.') . " " . tra('Default:') . ' "y"',
				'filter' => 'word'
			],
			'date_format' => [
				'name' => tra('Date format'),
				'description' => tra('Format to use for most dates. See <a href="http://www.php.net/manual/en/function.strftime.php">strftime() documentation</a>.') .
								" " . tra('Example value:') . ' %m/%e/%y %H:%M %Z. ' . tra('Default:') . ' ' .
								tra('site preference for short date format followed by site preference for short time format')
			],
			'maxlen' => [
				'name' => tra('Maximum length'),
				'description' => tra('If set to an integer, event names are allowed that number of characters as a maximum before being truncated.'),
				'filter' => 'int'
			],
			'showaction' => [
				'name' => tra('Show action'),
				'description' => 'y|n',
				'filter' => 'word'
			]
		],
		'common_params' => ['nonums', 'rows']
	];
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_upcoming_events($mod_reference, $module_params)
{
	global $user, $globalperms;
	$smarty = TikiLib::lib('smarty');
	$calendarlib = TikiLib::lib('calendar');

	$rawcals = $calendarlib->list_calendars();
	$calIds = [];
	$viewable = [];
	foreach ($rawcals['data'] as $cal_id => $cal_data) {
		$calIds[] = $cal_id;
		$canView = 'n';
		if ($globalperms->admin) {
			$canView = 'y';
		} elseif ($cal_data['personal'] == 'y') {
			if ($user && $user == $cal_data['user']) {
				$canView = 'y';
			}
		} else {
			$objectperms = Perms::get(['type' => 'calendar', 'object' => $cal_id]);
			if ($objectperms->view_calendar || $objectperms->admin_calendar) {
				$canView = 'y';
			}
		}
		if ($canView == 'y') {
			$viewable[] = $cal_id;
		}
	}
	$smarty->assign_by_ref('infocals', $rawcals['data']);

	$events = [];
	if (! empty($module_params['calendarId'])) {
		$calIds = preg_split('/[\|:\&,]/', $module_params['calendarId']);
	}

	if (! empty($viewable)) {
		$events = $calendarlib->upcoming_events(
			$mod_reference['rows'],
			array_intersect($calIds, $viewable),
			-1,
			'start_asc',
			isset($module_params['priorDays']) ? (int) $module_params['priorDays'] : 0,
			isset($module_params['maxDays']) ? (int) $module_params['maxDays'] : 365
		);
	}

	$smarty->assign('modUpcomingEvents', isset($events['data']) ? $events['data'] : []);
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
	$smarty->assign('showDescription', isset($module_params['showDescription']) ? $module_params['showDescription'] : 'n');
	$smarty->assign('showEnd', isset($module_params['showEnd']) ? $module_params['showEnd'] : 'n');
	$smarty->assign('showColor', isset($module_params['showColor']) ? $module_params['showColor'] : 'n');
	$smarty->assign('tooltip_infos', isset($module_params['tooltip_infos']) ? $module_params['tooltip_infos'] : 'y');
}
