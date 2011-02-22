<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_modif_events_info() {
	return array(
		'name' => tra('Last-Modified Events'),
		'description' => tra('Displays the specified number of calendar events, starting from the most recently modified.'),
		'prefs' => array("feature_calendar"),
		'params' => array(
			'calendarId' => array(
				'name' => tra('Calendar identifier'),
				'description' => tra('If set to a calendar identifier, restricts the events to those in the identified calendar.') . " " . tra('Example value: 13.') . " " . tra('Not set by default.')
			),
			'maxlen' => array(
				'name' => tra('Maximum length'),
				'description' => tra('Maximum number of characters in event names allowed before truncating.'),
				'filter' => 'int'
			),
			'nodate' => array(
				'name' => tra("Don't show date"),
				'description' => tra('If set to "y", it will hide date.'),
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_modif_events( $mod_reference, $module_params ) {
	global $smarty;
	global $calendarlib; include_once ('lib/calendar/calendarlib.php');
	
	$events = $calendarlib->last_modif_events($mod_reference["rows"], isset($module_params["calendarId"]) ? $module_params["calendarId"] : 0);
	
	$smarty->assign('modLastEvents', $events);
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
	$smarty->assign('nodate', isset($module_params["nodate"]) ? $module_params["nodate"] : 'n');
}
