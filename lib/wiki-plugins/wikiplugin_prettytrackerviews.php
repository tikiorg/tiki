<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


function wikiplugin_prettytrackerviews_info() {
	return array(
		'name' => tra('Pretty Tracker View Tracking'),
		'documentation' => tra('PluginPrettyTrackerViews'),			
		'description' => tra('Stores tiki.tracker.pretty.views attribute for trackeritem'),
		//'prefs' => array( '' ),
		'defaultfilter' => 'text',
		'params' => array (
			'record' => array (
				'required' => false,
				'name' => tra('Record'),
				'description' => tra('set to y to record view each time this is loaded'),
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'show' => array (
				'required' => false,
				'name' => tra('Show'),
				'description' => tra('set to n to hide showing of attribute'),
				'default' => 'y',
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
				'default' => ''
			),
		),
	);
}

function wikiplugin_prettytrackerviews( $data, $params ) {
	global $attributelib; require_once 'lib/attributes/attributelib.php'; 
	if (empty($params['itemId']) && !is_int($params['itemId'])) {
		return '';
	}
	if (isset($params['record']) && $params['record'] == 'y') {
		$attributes = $attributelib->get_attributes( 'trackeritem', $params['itemId'] );
		if (isset($attributes['tiki.tracker.pretty.views'])) {
			$value = $attributes['tiki.tracker.pretty.views'] + 1;
		} else {
			$value = 1;
		}	
		$attributelib->set_attribute( 'trackeritem', $params['itemId'], 'tiki.tracker.pretty.views', $value );
	}
	if (empty($params['show']) || $params['show'] == 'y') {
		if (!isset($value)) {
			$attributes = $attributelib->get_attributes( 'trackeritem', $params['itemId'] );
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

