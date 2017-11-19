<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_calendar_info()
{
	return [
		'name' => tra('Calendar'),
		'documentation' => 'PluginCalendar',
		'description' => tra('Display a calendar and its events'),
		'prefs' => [ 'feature_calendar', 'wikiplugin_calendar' ],
		'iconname' => 'calendar',
		'format' => 'html',
		'introduced' => 4,
		'params' => [
			'calIds' => [
				'name' => tra('Calendar IDs'),
				'description' => tra('Comma-separated list of calendar Ids to restrict the events to specified calendars.')
					. " " . tra('Example values:') . '<code>13</code>, <code>4,7</code> ' . tra('Not set by default.'),
				'since' => '4.0',
				'filter' => 'digits',
				'separator' => ',',
				'default' => '',
				'profile_reference' => 'calendar',
			],
			'viewlist' => [
				'required' => false,
				'name' => tra('View Type'),
				'description' => tra('Determines how events.') . ' ' . tr(
					'%0 (default) shows events in a calendar.',
					'<code>table</code>'
				),
				'since' => '4.0',
				'filter' => 'word',
				'default' => 'table',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('List'), 'value' => 'list'],
					['text' => tra('Table'), 'value' => 'table'],
					['text' => tra('Both'), 'value' => 'both'],
				],
			],
			'viewmode' => [
				'name' => tra('View Time Span'),
				'description' => tr('If in calendar (%0) View Type, determines the time span displayed by the
					calendar.', '<code>table</code>') . tra('Default is month'),
				'since' => '4.0',
				'filter' => 'word',
				'default' => 'month',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Day'), 'value' => 'day'],
					['text' => tra('Week'), 'value' => 'week'],
					['text' => tra('Month'), 'value' => 'month'],
					['text' => tra('Quarter'), 'value' => 'quarter'],
					['text' => tra('Semester'), 'value' => 'semester'],
					['text' => tra('Year'), 'value' => 'year'],
				],
			],
			'viewnavbar' => [
				'required' => false,
				'name' => tra('Navigation Bar'),
				'description' => tra('Show or hide the navigation bar (not shown by default)'),
				'since' => '4.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				],
			],
		],
	];
}

function wikiplugin_calendar($data, $params)
{
	global $prefs, $tiki_p_admin, $tiki_p_view_calendar;
	global $dc, $user;

	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$calendarlib = TikiLib::lib('calendar');

	if (empty($params['calIds'])) {
		$params['calIds'] = [1];
	}
	if (empty($params['viewlist'])) {
		$params['viewlist'] = 'table';
	}
	if (empty($params['viewmode'])) {
		$params['viewmode'] = 'month';
	}
	if (empty($params['viewnavbar'])) {
		$params['viewnavbar'] = 'n';
	}

	$module_reference = [
		'moduleId' => null,
		'name' => 'calendar_new',
		'params' => [ 'calIds' => $params['calIds'],
							'viewnavbar' => $params['viewnavbar'],
							'viewlist' => $params['viewlist'],
							'viewmode' => $params['viewmode'],
							'nobox' => 'y' ],
		'position' => null,
		'ord' => null,
	];

	$modlib = TikiLib::lib('mod');
	$out = '';
	if ($params['viewlist'] == 'table' || $params['viewlist'] == 'both') {
		$out .= $modlib->execute_module($module_reference);
	}
	if ($params['viewlist'] == 'list' || $params['viewlist'] == 'both') {
		$module_reference['params']['viewlist'] = 'list';
		$out .= "<div>" . $modlib->execute_module($module_reference) . "</div>";
	}

	return "<div>$out</div>";
}
