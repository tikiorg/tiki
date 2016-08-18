<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_timeline_info()
{
	return array(
		'name' => tra('Timeline'),
		'format' => 'html',
		'documentation' => 'PluginTimeline',
		'description' => tra('Display a timeline'),
		'prefs' => array( 'wikiplugin_timeline' ),
		'iconname' => 'history',
		'introduced' => 8,
		'tags' => array( 'experimental' ),
		'params' => array(
			'scope' => array(
				'required' => false,
				'name' => tr('Scope'),
				'description' => tr('Display the event list items represented in the page. (%0all%1, %0center%1, or
					a custom CSS selector)', '<code>', '</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => 'center',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Width of the timeline as CSS units (default: %0)', '<code>100%</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '100%',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('Height of the timeline as CSS units (default: %0)', '<code>400px</code>'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '400px',
			),
			//these two parameters don't seem to be used so commenting out to avoid confusion for users
/*			'lower' => array(
				'required' => true,
				'name' => tra('Lower Bound'),
				'description' => tra('Date from which element should be displayed. Date must be provided in YYYY-MM-DD HH:mm:ss format.'),
				'filter' => 'datetime',
				'default' => '',
				'accepted' => 'Date in YYYY-MM-DD HH:mm:ss format',
			),
			'upper' => array(
				'required' => true,
				'name' => tra('Upper Bound'),
				'description' => tra('Date until which element should be displayed. Date must be provided in YYYY-MM-DD HH:mm:ss format.'),
				'filter' => 'datetime',
				'default' => '',
				'accepted' => 'Date in YYYY-MM-DD HH:mm:ss format',
			),*/
			'scale1' => array(
				'required' => false,
				'name' => tra('Primary Scale Unit'),
				'description' => tra('Unit of time to use for the primary scale (default is Month)'),
				'since' => '8.0',
				'filter' => 'alpha',
				'default' => 'month',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Hour'), 'value' => 'hour'),
					array('text' => tra('Day'), 'value' => 'day'),
					array('text' => tra('Week'), 'value' => 'week'),
					array('text' => tra('Month'), 'value' => 'month'),
					array('text' => tra('Year'), 'value' => 'year'),
					array('text' => tra('Decade'), 'value' => 'decade'),
					array('text' => tra('Century'), 'value' => 'century'),
				)
			),
			'scale2' => array(
				'required' => false,
				'name' => tra('Secondary Scale Unit'),
				'description' => tra('Unit of time to use for the secondary scale'),
				'since' => '8.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Hour'), 'value' => 'hour'),
					array('text' => tra('Day'), 'value' => 'day'),
					array('text' => tra('Week'), 'value' => 'week'),
					array('text' => tra('Month'), 'value' => 'month'),
					array('text' => tra('Year'), 'value' => 'year'),
					array('text' => tra('Decade'), 'value' => 'decade'),
					array('text' => tra('Century'), 'value' => 'century'),
				)
			),
		),
	);
}

function wikiplugin_timeline($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_escape');

	$default = array('scale1' => 'month', 'width' => '100%', 'height' => '400px');
	$params = array_merge($default, $params);

	$width = smarty_modifier_escape($params['width']);
	$height = smarty_modifier_escape($params['height']);
	$scope = smarty_modifier_escape(wp_timeline_getscope($params));

	$headerlib = TikiLib::lib('header');
	$headerlib->add_jsfile('lib/simile_tiki/tiki-timeline.js');

	$headerlib->add_jq_onready(
		'// TODO set up datasource - get data from {list} output or calendar events
					var ttl_eventData = { events: [], dateTimeFormat: ""};
					setTimeout( function(){
						ttlInit("ttl_timeline", ttl_eventData,"' . $params['scale1'] . '","' . $params['scale2'] . '");
					}, 1000);
					'
	);
	return '<div class="timeline-container" data-marker-filter="' . $scope . '" style="width: ' . $width . '; height: ' . $height . ';"></div>';
}

function wp_timeline_getscope($params)
{
	$scope = 'center';
	if (isset($params['scope'])) {
		$scope = $params['scope'];
	}

	switch ($scope) {
		case 'center':
			return '#tiki-center .eventlist';
		case 'all':
			return '.eventlist';
		default:
			return $scope;
	}
}
