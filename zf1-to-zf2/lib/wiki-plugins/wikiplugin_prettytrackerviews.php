<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


function wikiplugin_prettytrackerviews_info()
{
	return array(
		'name' => tra('Pretty Tracker View Tracking'),
		'documentation' => tra('PluginPrettyTrackerViews'),
		'description' => tra('Store tiki.tracker.pretty.views attribute for a trackeritem'),
		'prefs' => array('wikiplugin_prettytrackerviews', 'feature_trackers'),
		'defaultfilter' => 'text',
		'tags' => array( 'experimental' ),
		'iconname' => 'trackers',
		'introduced' => 7,
		'params' => array (
			'record' => array (
				'required' => false,
				'name' => tra('Record'),
				'description' => tr('Set to %0y%1 to record view each time this is loaded', '<code>', '</code>'),
				'since' => '7.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'show' => array (
				'required' => false,
				'name' => tra('Show'),
				'description' => tr('Set to %0n%1 to hide showing of attribute', '<code>', '</code>'),
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'itemId' => array (
				'required' => true,
				'name' => tra('Item ID'),
				'description' => tra('Set to ID of tracker item'),
				'since' => '7.0',
				'default' => '',
				'filter' => 'digits',
				'profile_reference' => 'tracker_item',
			),
		),
	);
}

function wikiplugin_prettytrackerviews( $data, $params )
{
	$attributelib = TikiLib::lib('attribute');
	if (empty($params['itemId']) && !is_int($params['itemId'])) {
		return '';
	}
	if (isset($params['record']) && $params['record'] == 'y') {
		$attributes = $attributelib->get_attributes('trackeritem', $params['itemId']);
		if (isset($attributes['tiki.tracker.pretty.views'])) {
			$value = $attributes['tiki.tracker.pretty.views'] + 1;
		} else {
			$value = 1;
		}
		$attributelib->set_attribute('trackeritem', $params['itemId'], 'tiki.tracker.pretty.views', $value);
	}
	if (empty($params['show']) || $params['show'] == 'y') {
		if (!isset($value)) {
			$attributes = $attributelib->get_attributes('trackeritem', $params['itemId']);
			if (isset($attributes['tiki.tracker.pretty.views'])) {
				$value = $attributes['tiki.tracker.pretty.views'];
			} else {
				$value = 0;
			}
		}
		return $value;
	}
	return '';
}
