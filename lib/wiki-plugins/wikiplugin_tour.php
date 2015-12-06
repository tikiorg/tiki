<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_tour_info()
{
	return array(
		'name' => tra('Tour'),
		'documentation' => 'PluginTour',
		'description' => tra('Quick and easy way to build your product tours with Bootstrap popovers'),
		'prefs' => array( 'wikiplugin_tour' ),
		'iconname' => 'information',
		'introduced' => 15,
		'body' => tra('Content of the step'),
		'format' => 'html',
		'params' => array(
			'element' => array(
				'name' => tra('Element'),
				'required' => false,
				'description' => tra('Element to show the popup on; if empty, use the plugin location itself'),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
			'title' => array(
				'name' => tra('Title'),
				'required' => false,
				'description' => tra('Title of the step'),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
			'next' => array(
				'name' => tra('Next'),
				'required' => false,
				'description' => tra('Index of the step to show after this one, starting from 0, -1 for the last one'),
				'since' => '15.0',
				'filter' => 'int',
				'default' => '',
			),
			'prev' => array(
				'name' => tra('Previous'),
				'required' => false,
				'description' => tra('Index of the step to show before this one, starting from 0, -1 not to show'),
				'since' => '15.0',
				'filter' => 'int',
				'default' => '',
			),
			'path' => array(
				'name' => tra('Path'),
				'required' => false,
				'description' => tra('Path to the page on which the step should be shown'),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
			'start' => array(
				'name' => tra('Start'),
				'required' => false,
				'description' => tra('Start the tour on page load?'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
		),
	);
}


function wikiplugin_tour($data, $params)
{
	static $id = 0;
	$unique = 'wptour_' . ++$id;
	static $wp_tour = array('steps' => array());

	$headerlib = TikiLib::lib('header');
	$headerlib->add_jsfile('vendor/sorich87/bootstrap-tour/build/js/bootstrap-tour.js')
			->add_cssfile('vendor/sorich87/bootstrap-tour/build/css/bootstrap-tour.css');

	$defaults = array();
	$plugininfo = wikiplugin_tour_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	// non changing init js in ransk 11 and 13 (the tour definition goes in 12)
	$headerlib->add_jq_onready('var tour;
', 11);

	if ($params['start'] === 'y') {
		$headerlib->add_jq_onready('
if (tour) {
	// Start the tour
	tour.restart();
} else {
	console.log("Warning: Tour not initialized, the last step needs to have parameter next set to -1");
}
', 13);
	}
	unset($params['start']);


	$html = '';

	if (empty($params['element'])) {
		$params['element'] = "#$unique";
		$html = '<span id="' . $unique . '"></span>';
	}
	$params['content'] = TikiLib::lib('parser')->parse_data($data);

	$wp_tour['steps'][] = array_filter($params);

	if ($params['next'] == -1 || $params['path']) {
		$js = '// Instance the tour
tour = new Tour(' . json_encode($wp_tour) . ');
';
		$headerlib->add_jq_onready($js, 12);
	}

	return $html;
}
