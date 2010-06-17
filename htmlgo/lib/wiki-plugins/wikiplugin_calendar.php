<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_calendar_info() {
	return array(
		'name' => tra('Calendar'),
		'documentation' => 'PluginCalendar',
		'description' => tra('Includes a calendar and/or a list of calendar events.'),
		'prefs' => array( 'feature_calendar', 'wikiplugin_calendar' ),
		'params' => array(
			'calIds' => array(
				'name' => tra('Calendars filter'),
				'description' => tra('If set to a list of calendar identifiers, restricts the events to those in the identified calendars. Identifiers are separated by commas (",").') . " " . tra('Example values:') . '"13", "4,7", "31,49". ' . tra('Not set by default.'),
				'filter' => 'digits',
				'separator' => ',',
			),
			'viewlist' => array(
				'required' => false,
				'name' => tra('View type'),
				'description' => tra('Determines how to show events.') . ' ' . tra('Possible values:') . ' ' . 'table, list, both. ' . tra('"table" shows events in a calendar.') . ' ' . tra('Default value:') . ' calendar.',
				'filter' => 'word',
			),
			'viewmode' => array(
				'name' => tra('Calendar view type time span'),
				'description' => tra('If in calendar (or "table") view type, determines the time span displayed by the calendar.') . ' ' . tra('Possible values:') . ' year, semester, quarter, month, week, day.',
				'filter' => 'word'
			),
			'viewnavbar' => array(
				'required' => false,
				'name' => tra('View the navigation bar'),
				'description' => tra('Decide or not to show the navigation bar.'),
				'filter' => 'alpha',
			),
		),
	);
}

function wikiplugin_calendar($data, $params) {
    global $smarty, $tikilib, $prefs, $tiki_p_admin, $tiki_p_view_calendar;
    global $dbTiki, $dc, $user, $calendarlib;

    require_once("lib/calendar/calendarlib.php");

	if ( empty($params['calIds']) ) {
		$params['calIds'] = array(1);
	}
	if ( empty($params['viewlist']) ) {
		$params['viewlist'] = 'table';
	}
	if ( empty($params['viewmode']) ) {
		$params['viewmode'] = 'month';
	}
	if ( empty($params['viewnavbar']) ) {
		$params['viewnavbar'] = 'n';
	}

	$module_reference = array(
		'moduleId' => null,
		'name' => 'calendar_new',
		'params' => array( 'calIds' => $params['calIds'], 'viewnavbar'=> $params['viewnavbar'],
													'viewlist'=> $params['viewlist'],
													'viewmode' => $params['viewmode'] ),
		'position' => null,
		'ord' => null,
	);

	global $modlib; require_once 'lib/modules/modlib.php';
	$out = '';
	if ($params['viewlist'] == 'table' || $params['viewlist'] == 'both') {
		$out .= $modlib->execute_module( $module_reference );
	}
	if ( $params['viewlist'] == 'list' || $params['viewlist'] == 'both' ) {
		$module_reference['params']['viewlist'] = 'list';
		$out .= "<div>".$modlib->execute_module( $module_reference )."</div>";
	}

	return "<div>$out</div>";
}
