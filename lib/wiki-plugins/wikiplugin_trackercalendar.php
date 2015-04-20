<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackercalendar_info()
{
	return array(
		'name' => tr('Tracker Calendar'),
		'description' => tr('Uses full calendar to render the content of a tracker.'),
		'prefs' => array('wikiplugin_trackercalendar', 'calendar_fullcalendar'),
		'format' => 'html',
		'params' => array(
			'trackerId' => array(
				'name' => tr('Tracker ID'),
				'description' => tr('Tracker to search from'),
				'required' => false,
				'default' => 0,
				'filter' => 'int',
				'profile_reference' => 'tracker',
			),
			'begin' => array(
				'name' => tr('Begin date field'),
				'description' => tr('Permanent name of the field to use for event begining'),
				'required' => true,
				'filter' => 'word',
			),
			'end' => array(
				'name' => tr('End date field'),
				'description' => tr('Permanent name of the field to use for event ending'),
				'required' => true,
				'filter' => 'word',
			),
			'resource' => array(
				'name' => tr('Resource descriptor field'),
				'description' => tr('Permanent name of the field to use as the resource indicator'),
				'required' => false,
				'filter' => 'word',
			),
			'coloring' => array(
				'name' => tr('Coloring discriminator field'),
				'description' => tr('Permanent name of the field to use to segment the information into color schemes.'),
				'required' => false,
				'filter' => 'word',
			),
			'amonth' => array(
				'required' => false,
				'name' => tra('Agenda by Months'),
				'description' => tra('Display the option to change the view to agenda by months'),
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
				'default' => 0,
				'filter' => 'int',
			),			
			'dMonth' => array(
				'required' => false,
				'name' => tra('Default Month'),
				'description' => tra('Choose the default month (mm, as numeric value) to use for the display. Numeric values here are 1-based, meaning January=1, February=2, etc'),
				'default' => 0,
				'filter' => 'int',
			),
			'dDay' => array(
				'required' => false,
				'name' => tra('Default Day'),
				'description' => tra('Choose the default day (dd) to use for the display'),
				'default' => 0,
				'filter' => 'int',
			),
			'fDayofWeek' => array(
				'required' => false,
				'name' => tra('First day of the Week'),
				'description' => tra('Choose the day that each week begins with, for the tracker calendar display. The value must be a number that represents the day of the week: Sunday=0, Monday=1, Tuesday=2, etc. Default: 0 (Sunday)'),
				'default' => 0,
				'filter' => 'int',
			),
		),
	);
}

function wikiplugin_trackercalendar($data, $params)
{
	static $id = 0;
	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('vendor_extra/fullcalendar-resourceviews/fullcalendar/fullcalendar.css');
	$headerlib->add_jsfile('vendor_extra/fullcalendar-resourceviews/fullcalendar/fullcalendar.min.js');

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
			'timeFormat' => $prefs['display_12hr_clock'] === 'y' ? 'h(:mm)TT' : 'HH:mm',
		)
	);
	return $smarty->fetch('wiki-plugins/trackercalendar.tpl');
}

function wikiplugin_trackercalendar_get_resources($field)
{
	$db = TikiDb::get();

	return $db->fetchAll('SELECT DISTINCT value as id, value as name FROM tiki_tracker_item_fields WHERE fieldId = ? ORDER BY  value', $field['fieldId']);
}

