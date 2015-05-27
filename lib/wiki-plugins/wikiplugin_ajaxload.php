<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_ajaxload_info() {

	return array(
		'name' => tra('AJAX Load'),
		'documentation' => 'PluginAJAXLoad',
		'description' => tra('Load data into a DIV using AJAX if JavaScript is enabled, or in an IFRAME if not.'),
		'prefs' => array('wikiplugin_ajaxload'),
		'format' => 'html',
		'icon' => 'img/icons/script_code_red.png',
		'validate' => 'all',
		'body' => tra('On Load JS'),
		'params' => array(
			'url' => array(
				'required' => true,
				'name' => tra('URL'),
				'description' => tra('Address of the data to load, e.g. "tiki-index_raw.php?page=Page+Name"'),
				'filter' => 'url',
			),
			'selector' => array(
				'required' => false,
				'name' => tra('Selector'),
				'description' => tra('jQuery selector to retrieve part of the page when using AJAX, e.g. "#page-data"'),
				'filter' => 'none',
				'default' => '',
			),
			'target' => array(
				'required' => false,
				'name' => tra('Target'),
				'description' => tra('Where to load the AJAX data into (when using iframe if JavaScript is disabled it will appear where the plugin is in the page)'),
				'filter' => 'none',
				'default' => '',
			),
			'id' => array(
				'required' => false,
				'name' => tra('Id'),
				'description' => tra('Id for the DIV or IFRAME.'),
				'filter' => 'text',
				'default' => '',
			),
			'class' => array(
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('Class for the DIV or IFRAME.'),
				'filter' => 'text',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('In pixels or percentage. Default value is 100%.'),
				'default' => '100%',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('In pixels or percentage. Default value is "auto".'),
				'default' => 'auto',
			),
		),
	);
}

function wikiplugin_ajaxload($data, $params) {
	global $prefs;
	static $instance = 0;
	$instance++;

	if (empty($params['url'])) {
		return '<span class="alert-danger">' . tra('Parameter URL missing') . '</span>';
	}

	$plugininfo = wikiplugin_ajaxload_info();
	$default = array();
	foreach ($plugininfo['params'] as $key => $param) {
		if (isset($param['default'])) {
			$default[$key] = $param['default'];
		}
	}
	$params = array_merge($default, $params);

	if ($params['id']) {
		$id = $params['id'];
	} else {
		$id = 'wp_ajaxload_' . $instance;
	}
	$attributes = empty($params['class']) ? '' : ' class="' . $params['class'] . '"';
	$attributes.= ' width="' . $params['width'] . '" height="' . $params['height'] . '"';

	if ($prefs['javascript_enabled'] === 'y') {

		if ($params['target']) {
			$html = '';
			$id = $params['target'];
		} else {
			$html = "<div id=\"$id\"$attributes></div>";
			$id = '#' . $id;
		}

		$selector = $params['selector'] ? 'data = $("' . $params['selector'] . '", data).html();' : '';
		$data = str_replace('<x>', '', $data);	// desanitize js

		TikiLib::lib('header')->add_jq_onready('
(function ($) {
	var $el = $("'.$id.'");
	$el.tikiModal(tr("Loading..."));
	$.ajax({
		url: "'.$params['url'] .'",
		dataType: "html",
		method: "GET"
	}).done(function(data) {
	  '. $selector . '
	  '. $data .'
	  $el.html(data);
	}).fail(function() {
	}).always(function () {
		$el.tikiModal();
	});
})(jQuery);');

	} else {		// no js

		$html = "<iframe src=\"{$params['url']}\" id=\"$id\"$attributes></iframe>";
	}

	return $html;
}
