<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
		),
	);
}

function wikiplugin_trackercalendar($data, $params)
{
	static $id = 0;
	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('lib/fullcalendar/fullcalendar.css');
	$headerlib->add_jsfile('lib/fullcalendar/fullcalendar.min.js');

	$jit = new JitFilter($params);
	$definition = Tracker_Definition::get($jit->trackerId->int());

	if (! $definition) {
		return WikiParser_PluginOutput::userError(tr('Tracker not found.'));
	}

	$beginField = $definition->getFieldFromPermName($jit->begin->word());
	$endField = $definition->getFieldFromPermName($jit->end->word());

	if (! $beginField || ! $endField) {
		return WikiParser_PluginOutput::userError(tr('Fields not found.'));
	}

	$views = array('month', 'agendaWeek', 'agendaDay');

	$resources = array();
	if ($resourceField = $jit->resource->word()) {
		$field = $definition->getFieldFromPermName($resourceField);
		$resources = wikiplugin_trackercalendar_get_resources($field);
		$views[] = 'resourceDay';
		$views[] = 'resourceWeek';
		$views[] = 'resourceMonth';
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('trackercalendar', array(
		'id' => 'trackercalendar' . ++$id,
		'trackerId' => $jit->trackerId->int(),
		'begin' => $jit->begin->word(),
		'end' => $jit->end->word(),
		'resource' => $resourceField,
		'resourceList' => $resources,
		'beginFieldName' => 'ins_' . $beginField['fieldId'],
		'endFieldName' => 'ins_' . $endField['fieldId'],
		'firstDayofWeek' => 0,
		'views' => implode(',', $views),
		'viewyear' => (int) date('Y'),
		'viewmonth' => (int) date('n'),
		'viewday' => (int) date('j'),
		'minHourOfDay' => 7,
		'maxHourOfDay' => 20,
		'addTitle' => tr('Insert'),
	));
	return $smarty->fetch('wiki-plugins/trackercalendar.tpl');
}

function wikiplugin_trackercalendar_get_resources($field)
{
	$db = TikiDb::get();

	return $db->fetchAll('SELECT DISTINCT value as id, value as name FROM tiki_tracker_item_fields WHERE fieldId = ? ORDER BY  value', $field['fieldId']);
}

