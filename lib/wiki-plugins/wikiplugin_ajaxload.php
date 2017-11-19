<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_ajaxload_info()
{

	return [
		'name' => tra('Ajax Load'),
		'documentation' => 'PluginAJAXLoad',
		'description' => tra('Load data into an HTML div using Ajax or in an iframe'),
		'prefs' => ['wikiplugin_ajaxload'],
		'format' => 'html',
		'iconname' => 'code_file',
		'introduced' => 14.1,
		'validate' => 'all',
		'body' => tra('JavaScript to run when the data is loaded, the incoming HTML is in a variable called data. You can modify that variable\'s contents to customise the HTML.'),
		'params' => [
			'url' => [
				'required' => true,
				'name' => tra('URL'),
				'description' => tr(
					'Address of the data to load, for example, %0tiki-index_raw.php?page=Page+Name%1',
					'<code>',
					'</code>'
				),
				'filter' => 'url',
				'since' => '14.1',
			],
			'selector' => [
				'required' => false,
				'name' => tra('Selector'),
				'description' => tr('jQuery selector to retrieve part of the page when using Ajax, for example,
					%0#page-data%1', '<code>', '</code>'),
				'filter' => 'none',
				'default' => '',
				'since' => '14.1',
			],
			'target' => [
				'required' => false,
				'name' => tra('Target'),
				'description' => tra('Where to load the Ajax data into (will create own DIV if not supplied. When using
					iframe if JavaScript is disabled it will appear where the plugin is in the page.'),
				'filter' => 'none',
				'default' => '',
				'since' => '14.1',
			],
			'id' => [
				'required' => false,
				'name' => tra('Id'),
				'description' => tra('HTML id for the div or iframe.'),
				'filter' => 'text',
				'default' => '',
				'since' => '14.1',
			],
			'class' => [
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('Class for the div or iframe.'),
				'filter' => 'text',
				'default' => '',
				'since' => '14.1',
			],
			'width' => [
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('In pixels or percentage. Default value is %0.', '<code>100%</code>'),
				'default' => '100%',
				'since' => '14.1',
			],
			'height' => [
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('In pixels or percentage. Default value is %0.', '<code>auto</code>'),
				'default' => 'auto',
				'since' => '14.1',
			],
			'absolutelinks' => [
				'required' => false,
				'name' => tra('Make Links Absolute'),
				'description' => tra('Convert relative links in the incoming data to be absolute. Default value is "All".'),
				'since' => '14.1',
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
				'options' => [
					['text' => tra('All'), 'value' => ''],
					['text' => tra('Images Only'), 'value' => 'src'],
					['text' => tra('Links Only'), 'value' => 'href'],
					['text' => tra('None'), 'value' => 'none'],
				],
			],
		],
	];
}

function wikiplugin_ajaxload($data, $params)
{
	global $prefs;
	static $instance = 0;
	$instance++;

	if (empty($params['url'])) {
		return '<span class="alert-danger">' . tra('Parameter URL missing') . '</span>';
	}

	$plugininfo = wikiplugin_ajaxload_info();
	$default = [];
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
	$attributes .= ' width="' . $params['width'] . '" height="' . $params['height'] . '"';

	if ($prefs['javascript_enabled'] === 'y') {
		if ($params['target']) {
			$html = '';
			$id = $params['target'];
		} else {
			$html = "<div id=\"$id\"$attributes></div>";
			$id = '#' . $id;
		}

		$js = $params['selector'] ? 'data = $("' . $params['selector'] . '", data).html();' : '';
		$data = str_replace('<x>', '', $data);	// desanitize js

		if ($params['absolutelinks'] !== 'none') {
			$parts = parse_url($params['url']);

			if ($parts) {
				$base = $parts['scheme'] . '://' .
					(! empty($parts['host']) ? $parts['host'] : '') .
					(! empty($parts['port']) ? ':' . $parts['port'] : '') .
					(! empty($parts['path']) ? pathinfo($parts['path'], PATHINFO_DIRNAME) : '');

				if (substr($base, -1) !== '/') {
					$base .= '/';
				}

				if ($params['absolutelinks'] === '') {
					$types = 'src|href';
				} else {
					$types = $params['absolutelinks'];
				}

				$js .= '	data = data.replace(/([\s-](?:' . $types . ')=["\'])(.*?)(["\'])/gi, function (match, start, url, end) {
		return start + (url.indexOf("://") === -1 ? "' . $base . '" : "") + url + end;
	});';
			}
		}

		TikiLib::lib('header')->add_jq_onready('
(function ($) {
	var $el = $("' . $id . '");
	$el.tikiModal(tr("Loading..."));
	$.ajax({
		url: "' . $params['url'] . '",
		dataType: "html",
		method: "GET"
	}).done(function(data) {
	  ' . $js . '
	  ' . $data . '
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
