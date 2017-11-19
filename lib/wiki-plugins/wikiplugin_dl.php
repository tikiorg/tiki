<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_dl_info()
{
	return [
		'name' => tra('Definition List'),
		'documentation' => 'PluginDL',
		'description' => tra('Create a definition list'),
		'prefs' => ['wikiplugin_dl'],
		'body' => tr('One entry per line. Each line is in %0Term: Definition%1 format.', '<code>', '</code>'),
		'iconname' => 'list',
		'tags' => [ 'basic' ],
		'introduced' => 1,
		'params' => [
			'type' => [
				'required' => false,
				'name' => tra('List Type'),
				'description' => tra('Type of definition list (left-aligned or horizontal).'),
				'since' => '16.0',
				'filter' => 'text',
				'safe' => true,
				'advanced' => false,
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Standard (left-aligned)'), 'value' => 's'],
					['text' => tra('Horizontal (inline) '), 'value' => 'h'],
				],
				'default' => '',
			],
		],
	];
}

function wikiplugin_dl($data, $params)
{
	global $tikilib;

	global $replacement;
	if (isset($param)) {
		extract($params, EXTR_SKIP);
	}
	if (isset($params["type"])) {
		$dlt = $params["type"];
		if ($dlt == "horizontal" or $dlt == "dl-horizontal" or $dlt == "horiz" or $dlt == "h" or $dlt == "inline") {
			$result = '<dl class="dl-horizontal">';
		}
		if ($dlt == "left" or $dlt == "vertical" or $dlt == "standard" or $dlt == "s") {
			$result = '<dl>';
		}
	} else {
		$result = '<dl>';
	}
	$lines = explode("\n", $data);

	foreach ($lines as $line) {
		$parts = explode(":", $line, 2);

		if (isset($parts[0]) && isset($parts[1])) {
			$result .= '<dt>' . $parts[0] . '</dt><dd>' . $parts[1] . '</dd>';
		} else {
			$result .= '<dt>' . $line . '</dt>';
		}
	}

	$result .= '</dl>';
	return $result;
}
