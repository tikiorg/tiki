<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sort_info()
{
	return [
		'name' => tra('Sort'),
		'documentation' => 'PluginSort',
		'description' => tra('Sort lines of text'),
		'prefs' => [ 'wikiplugin_sort' ],
		'body' => tra('Data to sort, one entry per line.'),
		'filter' => 'text',
		'iconname' => 'sort-desc',
		'introduced' => 1,
		'tags' => [ 'basic' ],
		'params' => [
			'sort' => [
				'required' => false,
				'name' => tra('Order'),
				'description' => tra('Set the sort order of lines of content (default is ascending)'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'asc',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Ascending'), 'value' => 'asc'],
					['text' => tra('Descending'), 'value' => 'desc'],
					['text' => tra('Reverse'), 'value' => 'reverse'],
					['text' => tra('Shuffle'), 'value' => 'shuffle']
				]
			]
		]
	];
}

function wikiplugin_sort($data, $params)
{
	global $tikilib;

	extract($params, EXTR_SKIP);

	$sort = (isset($sort)) ? $sort : "asc";

	$lines = preg_split("/\n+/", $data, -1, PREG_SPLIT_NO_EMPTY); // separate lines into array
	// $lines = array_filter( $lines, "chop" ); // remove \n
	srand((float) microtime() * 1000000); // needed for shuffle;

	if ($sort == "asc") {
		natcasesort($lines);
	} elseif ($sort == "desc") {
		natcasesort($lines);
		$lines = array_reverse($lines);
	} elseif ($sort == "reverse") {
		$lines = array_reverse($lines);
	} elseif ($sort == "shuffle") {
		shuffle($lines);
	}

	reset($lines);

	if (is_array($lines)) {
		$data = implode("\n", $lines);
	}

	$data = trim($data);
	return $data;
}
