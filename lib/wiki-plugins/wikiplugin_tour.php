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
			'placement' => array(
				'name' => tra('Placement'),
				'required' => false,
				'description' => tra('The placement of the popup'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => 'right',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Top'), 'value' => 'top'),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Bottom'), 'value' => 'bottom'),
					array('text' => tra('Left'), 'value' => 'left'),
				),
			),
			'orphan' => array(
				'name' => tra('Orphan'),
				'required' => false,
				'description' => tra('Setting to true removes the pointer on the popup and centers it on the page'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'backdrop' => array(
				'name' => tra('Backdrop'),
				'required' => false,
				'description' => tra('Show a dark backdrop behind the popover and its element, highlighting the current step.'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'show_once' => array(
				'name' => tra('Only Show Once'),
				'required' => false,
				'description' => tra('Set to y to only show once. tour_id should also be set if there are multiple tours.'),
				'since' => '15.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),
			'tour_id' => array(
				'name' => tra('Tour ID'),
				'required' => false,
				'description' => tra('Set a tour ID to be able to only show the tour once.'),
				'since' => '15.0',
				'filter' => 'text',
				'default' => 'default',
			),
			'show_restart_button' => array(
				'name' => tra('Restart Button'),
				'required' => false,
				'description' => tra('Display a button to restart the tour. Enter the text to appear on the button.'),
				'since' => '15.0',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_tour($data, $params)
{
	$defaults = array();
	$plugininfo = wikiplugin_tour_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	$cookie_id = 'tour'.md5($params['tour_id']);
	if (getCookie($cookie_id, 'tours') == 'y') {
		$dontStart = true;
	} else {
		$dontStart = false;

		if ($params['show_once'] === 'y') {
			setCookieSection($cookie_id, 'y', 'tours');
		}
	}

	static $id = 0;
	$unique = 'wptour_' . ++$id;
	static $wp_tour = array('steps' => array());

	if (!isset($wp_tour['start'])) {
		$wp_tour['start'] = $params['start'];
	}

	$headerlib = TikiLib::lib('header');
	$headerlib->add_jsfile('vendor/sorich87/bootstrap-tour/build/js/bootstrap-tour.js')
			->add_cssfile('vendor/sorich87/bootstrap-tour/build/css/bootstrap-tour.css');

	// non changing init js in ransk 11 and 13 (the tour definition goes in 12)
	$headerlib->add_jq_onready('var tour;
', 11);

	if ($wp_tour['start'] === 'y' && ! $dontStart) {
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
	unset($params['tour_id']);

	$html = '';

	$params['orphan']   = ($params['orphan'] === 'y');
	$params['backdrop'] = ($params['backdrop'] === 'y');

	if (empty($params['element']) && !$params['orphan']) {
		$params['element'] = "#$unique";
		$html = '<span id="' . $unique . '"></span>';
		if (!empty($params['show_restart_button'])) {
			$smarty = TikiLib::lib('smarty');
			$smarty->loadPlugin('smarty_function_button');
			$html .= smarty_function_button([
					'_text' => tra($params['show_restart_button']),
					'_id' => $unique . '_restart',
					'href' => '#',
				], $smarty);
			$headerlib->add_jq_onready('$("#' . $unique . '_restart").click(function() {
	tour.goTo(0);
	tour.restart();
	return false;
});', 13);
		}
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

