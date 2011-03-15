<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_calendar_info() {
	return array(
		'name' => tra('Calendar'),
		'documentation' => 'PluginCalendar',
		'description' => tra('Display a calendar and its events'),
		'prefs' => array( 'feature_calendar', 'wikiplugin_calendar' ),
		'icon' => 'pics/icons/calendar.png',
		'params' => array(
			'calIds' => array(
				'name' => tra('Calendar IDs'),
				'description' => tra('If set to a list of calendar identifiers, restricts the events to those in the identified calendars. Identifiers are separated by commas (",").') . " " . tra('Example values:') . '"13", "4,7", "31,49". ' . tra('Not set by default.'),
				'filter' => 'digits',
				'separator' => ',',
				'default' => '',
			),
			'viewlist' => array(
				'required' => false,
				'name' => tra('View Type'),
				'description' => tra('Determines how to show events.') . ' ' . tra('Possible values:') . ' ' . 'table, list, both. ' . tra('"table" shows events in a calendar.') . ' ' . tra('Default value:') . ' table.',
				'filter' => 'word',
				'default' => 'table',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('List'), 'value' => 'list'), 
					array('text' => tra('Table'), 'value' => 'table'),
					array('text' => tra('Both'), 'value' => 'both'),
				),
			),
			'viewmode' => array(
				'name' => tra('View Time Span'),
				'description' => tra('If in calendar (or "table") view type, determines the time span displayed by the calendar.') . ' ' . tra('Possible values:') . ' year, semester, quarter, month, week, day. '
										. tra('Default is month'),
				'filter' => 'word',
				'default' => 'month',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Day'), 'value' => 'day'), 
					array('text' => tra('Week'), 'value' => 'week'),
					array('text' => tra('Month'), 'value' => 'month'),
					array('text' => tra('Quarter'), 'value' => 'quarter'), 
					array('text' => tra('Semester'), 'value' => 'semester'),
					array('text' => tra('Year'), 'value' => 'year'),
				),
			),
			'viewnavbar' => array(
				'required' => false,
				'name' => tra('Navigation Bar'),
				'description' => tra('Decide or not to show the navigation bar (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
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
		'params' => array( 'calIds' => $params['calIds'],
							'viewnavbar'=> $params['viewnavbar'],
							'viewlist'=> $params['viewlist'],
							'viewmode' => $params['viewmode'],
							'nobox' => 'y' ),
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
