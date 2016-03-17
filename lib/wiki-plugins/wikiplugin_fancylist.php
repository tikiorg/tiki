<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fancylist_info()
{
	return array(
		'name' => tra('Fancy List'),
		'documentation' => 'PluginFancyList',
		'description' => tra('Create a formatted numbered list'),
		'prefs' => array('wikiplugin_fancylist'),
		'body' => tra('One item per line starting with anything followed by ")".'),
		'iconname' => 'list',
		'introduced' => 3,
		'tags' => array( 'basic' ),
		'params' => array(
			'div' => array(
				'required' => false,
				'name' => tra('Use Div'),
				'description' => tra('Use the HTML div tag instead of the HTML ordered list tag (ol)'),
				'since' => '3.0',
				'default' => '',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'class' => array(
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('CSS class for the fancylist'),
				'since' => '3.0',
				'default' => '',
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_fancylist($data, $params)
{
	global $tikilib;
	global $replacement;
	if (isset($params)) {
		extract($params, EXTR_SKIP);
	}
	if (isset($div)) {
		$result = '<div class="fancylist'.($class ? " $class" : "").'">';
		$count=1;
	} else {
			$result = '<ol class="fancylist'.(isset($class) ? " $class" : "").'">';
	}
	// split data by lines (trimed whitespace from start and end)
	$lines = preg_split("/\n/", trim($data));
	foreach ($lines as $line) {
		// replace all before and including the ")"
		$part = preg_replace("/^[\w]+\)(.*)/", "$1", $line);
		if (isset($div)) {
			$result .= '<div><span class='.count.'>'.$count.'</span><p>' . $part . '</p></div>';
			$count++;
		} else {
			$result .= '<li><p>' . $part . '</p></li>';
		}
	}
	if (isset($div)) {
		$result .= '</div>';
	} else {
		$result .= '</ol>';
	}
	return $result;
}
