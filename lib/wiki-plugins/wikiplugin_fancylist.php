<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fancylist_info()
{
	return array(
		'name' => tra('Fancy List'),
		'documentation' => 'PluginFancyList',
		'description' => tra('Create a fancy-looking list'),
		'prefs' => array('wikiplugin_fancylist'),
		'body' => tra('One item per line starting with anything followed by ")".'),
		'icon' => 'img/icons/text_list_bullets.png',
		'tags' => array( 'basic' ),		
		'params' => array(
		 	'div' => array(
			 	'required' => false,
				'name' => tra('Use Div'),
				'description' => tra('Use the HTML tag div instead of the HTML tag for lists (ol)'),
				'default' => '',
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
				'default' => '',
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
		$result = '~np~<div class="fancylist'.($class ? " $class" : "").'">~/np~';
		$count=1;
	} else {
			$result = '~np~<ol class="fancylist'.(isset($class) ? " $class" : "").'">~/np~';
	}
	// split data by lines (trimed whitespace from start and end)
	$lines = preg_split("/\n/", trim($data));
	foreach ($lines as $line) {
		// replace all before and including the ")"
		$part = preg_replace("/[\w]+\)(.*)/", "$1", $line);
		if (isset($div)) {
			$result .= '~np~<div><span class='.count.'>'.$count.'</span><p>~/np~' . $part . '~np~</p></div>~/np~';
			$count++;
		} else {
			$result .= '~np~<li><p>~/np~' . $part . '~np~</p></li>~/np~';
		}
	}
	if (isset($div)) {
		$result .= '~np~</div>~/np~';
	} else {
	 	$result .= '~np~</ol>~/np~';
	}
	return $result;
}
