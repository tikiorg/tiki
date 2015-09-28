<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackercalendar_info()
{
	return array(
		'name' => tr('Tracker Calendar'),
		'description' => tr('Create and display a calendar using tracker data'),
		'prefs' => array('wikiplugin_trackercalendar', 'calendar_fullcalendar'),
		'format' => 'html',
		'iconname' => 'calendar',
		'introduced' => 10,
		'params' => array(
			'trackerId' => array(
				'name' => tr('Tracker ID'),
				'description' => tr('Tracker to search from'),
				'since' => '10.0',
				'required' => false,
				'default' => 0,
				'filter' => 'int',
				'profile_reference' => 'tracker',
			),
			'begin' => array(
				'name' => tr('Begin Date Field'),
				'description' => tr('Permanent name of the field to use for event beginning'),
				'since' => '10.0',
				'required' => true,
				'filter' => 'word',
			),
			'end' => array(
				'name' => tr('End Date Field'),
				'description' => tr('Permanent name of the field to use for event ending'),
				'since' => '10.0',
				'required' => true,
				'filter' => 'word',
			),
			'resource' => array(
				'name' => tr('Resource Descriptor Field'),
				'description' => tr('Permanent name of the field to use as the resource indicator'),
				'since' => '10.0',
				'required' => false,
				'filter' => 'word',
			),
			'coloring' => array(
				'name' => tr('Coloring Discriminator Field'),
				'description' => tr('Permanent name of the field to use to segment the information into color schemes.'),
				'since' => '10.0',
				'required' => false,
				'filter' => 'word',
			),
			'external' => array(
				'required' => false,
				'name' => tra('External Link'),
				'description' => tra('Follow external link when event item is clicked. Useful for supporting links to
					pretty tracker supported pages.'),
				'since' => '12.4',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Complete URL, internal or external.'),
				'since' => '12.4',
				'filter' => 'url',
				'default' => '',
				'parent' => array('name' => 'external', 'value' => 'y'),
			),
			'trkitemid' => array(
				'required' => false,
				'name' => tra('Tracker Item Id'),
				'description' => tr('If Yes (%0y%1) the item id will be passed as %0itemId%1, which can be used
					by Tracker plugins. Will be passed as %0itemid%1 if No (%0n%1)', '<code>', '</code>'),
				'since' => '12.4',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
				'parent' => array('name' => 'external', 'value' => 'y'),
			),
			'addAllFields' => array(
				'required' => false,
				'name' => tra('Add All Fields'),
				'description' => tr('If Yes (%0y%1)  all fields in the tracker will be added to the URL, not just the
					itemId', '<code>', '</code>'),
				'since' => '12.4',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
				'parent' => array('name' => 'external', 'value' => 'y'),
			),
			'useSessionStorage' => array(
				'required' => false,
				'name' => tra('Use Session Storage'),
				'description' => tr('If Yes (%0y%1) copy all the field values into window.sessionStorage so it can be
					accessed via JavaScript.', '<code>', '</code>'),
				'since' => '12.4',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
				'parent' => array('name' => 'addAllFields', 'value' => 'y'),
			),
			'amonth' => array(
				'required' => false,
				'name' => tra('Agenda by Months'),
				'description' => tra('Display the option to change the view to agenda by months'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'aweek' => array(
				'required' => false,
				'name' => tra('Agenda by Weeks'),
				'description' => tra('Display the option to change the view to agenda by weeks'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),			
			'aday' => array(
				'required' => false,
				'name' => tra('Agenda by Days'),
				'description' => tra('Display the option to change the view to agenda by days'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),			
			'rmonth' => array(
				'required' => false,
				'name' => tra('Resources by Months'),
				'description' => tra('Display the option to change the view to resources by months'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'rweek' => array(
				'required' => false,
				'name' => tra('Resources by Weeks'),
				'description' => tra('Display the option to change the view to resources by weeks'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),			
			'rday' => array(
				'required' => false,
				'name' => tra('Resources by Days'),
				'description' => tra('Display the option to change the view to resources by days'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),			
			'dView' => array(
				'required' => false,
				'name' => tra('Default View'),
				'description' => tra('Choose the default view for the Tracker Calendar'),
				'since' => '12.1',
				'filter' => 'alpha',
				'default' => 'month',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Agenda by Months'), 'value' => 'month'),
					array('text' => tra('Agenda by Weeks'), 'value' => 'agendaWeek'),
					array('text' => tra('Agenda by Days'), 'value' => 'agendaDay'),
					array('text' => tra('Resources by Months'), 'value' => 'resourceMonth'),
					array('text' => tra('Resources by Weeks'), 'value' => 'resourceWeek'),
					array('text' => tra('Resources by Days'), 'value' => 'resourceDay')
				)
			),
			'dYear' => array(
				'required' => false,
				'name' => tra('Default Year'),
				'description' => tra('Choose the default year (yyyy) to use for the display'),
				'since' => '12.1',
				'default' => 0,
				'filter' => 'int',
			),			
			'dMonth' => array(
				'required' => false,
				'name' => tra('Default Month'),
				'description' => tra('Choose the default month (mm, as numeric value) to use for the display. Numeric
					values here are 1-based, meaning January=1, February=2, etc'),
				'since' => '12.1',
				'default' => 0,
				'filter' => 'int',
			),
			'dDay' => array(
				'required' => false,
				'name' => tra('Default Day'),
				'description' => tra('Choose the default day (dd) to use for the display'),
				'since' => '12.1',
				'default' => 0,
				'filter' => 'int',
			),
			'fDayofWeek' => array(
				'required' => false,
				'name' => tra('First day of the Week'),
				'description' => tr('Choose the day that each week begins with, for the tracker calendar display.
					The value must be a number that represents the day of the week: Sunday=0, Monday=1, Tuesday=2,
					etc. Default: %0 (Sunday)', '<code>0</code>'),
				'since' => '12.1',
				'default' => 0,
				'filter' => 'int',
			),
			'weekends' => array(
				'required' => false,
				'name' => tra('Show Weekends'),
				'description' => tra('Display Saturdays and Sundays (shown by default)'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_trackercalendar($data, $params)
{
	static $id = 0;
	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('vendor_extra/fullcalendar-resourceviews/fullcalendar/fullcalendar.css');
	$headerlib->add_jsfile('vendor_extra/fullcalendar-resourceviews/fullcalendar/fullcalendar.min.js', true);

	$jit = new JitFilter($params);
	$definition = Tracker_Definition::get($jit->trackerId->int());
	$itemObject = Tracker_Item::newItem($jit->trackerId->int());

	if (! $definition) {
		return WikiParser_PluginOutput::userError(tr('Tracker not found.'));
	}

	$beginField = $definition->getFieldFromPermName($jit->begin->word());
	$endField = $definition->getFieldFromPermName($jit->end->word());

	if (! $beginField || ! $endField) {
		return WikiParser_PluginOutput::userError(tr('Fields not found.'));
	}

	$views = array();
	if (!empty($params['amonth']) and $params['amonth'] != 'y') {
		$amonth = 'n';
	} else {
		$amonth = 'y';
		$views[] = 'month';
	}
	if (!empty($params['aweek']) and $params['aweek'] != 'y') {
		$aweek = 'n';
	} else {
		$aweek = 'y';
		$views[] = 'agendaWeek';		
	}
	if (!empty($params['aday']) and $params['aday'] != 'y') {
		$aday = 'n';
	} else {
		$aday = 'y';
		$views[] = 'agendaDay';
	}

	$resources = array();
	if ($resourceField = $jit->resource->word()) {
		$field = $definition->getFieldFromPermName($resourceField);
		$resources = wikiplugin_trackercalendar_get_resources($field);

		if (!empty($params['rmonth']) and $params['rmonth'] != 'y') {
			$rmonth = 'n';
		} else {
			$rmonth = 'y';
			$views[] = 'resourceMonth';
		}
		if (!empty($params['rweek']) and $params['rweek'] != 'y') {
			$rweek = 'n';
		} else {
			$rweek = 'y';
			$views[] = 'resourceWeek';
		}
		if (!empty($params['rday']) and $params['rday'] != 'y') {
			$rday = 'n';
		} else {
			$rday = 'y';
			$views[] = 'resourceDay';
		}
	}
	
	// Define the default View (dView)
		if (!empty($params['dView'])) {
			$dView = $params['dView'];
		} else {
			$dView = 'month';
		}

	// Define the default date (dYear, dMonth, dDay)
		if (!empty($params['dYear'])) {
			$dYear = $params['dYear'];
		} else {
			$dYear = (int) date('Y');
		}
		if (!empty($params['dMonth']) and $params['dMonth'] > 0 and $params['dMonth'] < 13) {
			$dMonth = $params['dMonth'];
		} else {
			$dMonth = (int) date('n');
		}
		if (!empty($params['dDay']) and $params['dDay'] > 0 and $params['dDay'] < 32) {
			$dDay = $params['dDay'];
		} else {
			$dDay = (int) date('j');
		}

		global $prefs;

		if (!empty($params['fDayofWeek']) and $params['fDayofWeek'] > -1 and $params['fDayofWeek'] < 7) {
			$firstDayofWeek = $params['fDayofWeek'];
		} elseif ($prefs['calendar_firstDayofWeek'] !== 'user') {
			$firstDayofWeek = $prefs['calendar_firstDayofWeek'];
		} else {
			$firstDayofWeek = 0;
		}

	$params['addAllFields'] = empty($params['addAllFields']) ? 'y' : $params['addAllFields'];
	$params['useSessionStorage'] = empty($params['useSessionStorage']) ? 'y' : $params['useSessionStorage'];
	$params['weekends'] = empty($params['weekends']) ? 'y' : $params['weekends'];

	$smarty = TikiLib::lib('smarty');
	$smarty->assign(
		'trackercalendar',
		array(
			'id' => 'trackercalendar' . ++$id,
			'trackerId' => $jit->trackerId->int(),
			'begin' => $jit->begin->word(),
			'end' => $jit->end->word(),
			'resource' => $resourceField,
			'resourceList' => $resources,
			'coloring' => $jit->coloring->word(),
			'beginFieldName' => 'ins_' . $beginField['fieldId'],
			'endFieldName' => 'ins_' . $endField['fieldId'],
			'firstDayofWeek' => $firstDayofWeek,
			'views' => implode(',', $views),
			'viewyear' => $dYear,
			'viewmonth' => $dMonth,
			'viewday' => $dDay,
			'minHourOfDay' => 7,
			'maxHourOfDay' => 24,
			'addTitle' => tr('Insert'),
			'canInsert' => $itemObject->canModify(),
			'dView' => $dView,
			'body' => $data,
			'url' => $params['external'] === 'y' ? $params['url'] : '',
			'trkitemid' => $params['external'] === 'y' ? $params['trkitemid'] : '',
			'addAllFields' => $params['external'] === 'y' ? $params['addAllFields'] : '',
			'useSessionStorage' => $params['external'] === 'y' ? $params['useSessionStorage'] : '',
			'timeFormat' => $prefs['display_12hr_clock'] === 'y' ? 'h(:mm)TT' : 'HH:mm',
			'weekends' => $params['weekends'] === 'y' ? 1 : 0,
		)
	);
	return $smarty->fetch('wiki-plugins/trackercalendar.tpl');
}

function wikiplugin_trackercalendar_get_resources($field)
{
	$db = TikiDb::get();

	return $db->fetchAll('SELECT DISTINCT LOWER(value) as id, value as name FROM tiki_tracker_item_fields WHERE fieldId = ? ORDER BY  value', $field['fieldId']);
}

