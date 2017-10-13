<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_footnote_info()
{
	return array(
		'name' => tra('Footnote'),
		'documentation' => 'PluginFootnote',
		'description' => tra('Create automatically numbered footnotes (together with PluginFootnoteArea)'),
		'prefs' => array('wikiplugin_footnote'),
		'body' => tra('The footnote'),
		'iconname' => 'superscript',
		'filter' => 'wikicontent',
		'format' => 'html',
		'introduced' => 3,
		'params' => array(
			'class' => array(
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('Add class to footnotearea'),
				'since' => '14.0',
				'default' => '',
				'filter' => 'alnumspace',
				'accepted' => tra('Valid CSS class'),
			),
		)
	);
}

function wikiplugin_footnote($data, $params)
{
	global $footnotes;
	$smarty = TikiLib::lib('smarty');

	if (! isset($footnotes['lists'])) {   // if this is the first time the script has run, initialise
		$footnotes['count'] = 0;
		$footnotes['nest'] = 0;
		$footnotes['lists'] = array();    // data for general footnotes
	}

	$data = trim($data);
	if (empty($data)) {
		return '<sup>' . tra('Error: Empty footnote') . '</sup>';
	}
	
	$footnotes['count']++;                      // keep a record of how many times footones is called to generate unique id's

	// Create an array of classes to be applied
	$classes = (isset($params['class'])) ? explode(' ',trim($params["class"])) : array();
	if ($footnotes['nest'] > 0)   // if we are in a nested footnote, add a nested class
		$classes[] ='footnest'.$footnotes['nest'];

	//set the current list to create
	$list = '.def.';                            // Set the default to illegal class name to prevent conflicts
	foreach ($classes as $class) {
		if (isset($footnotes['lists'][$class])) {
			$list = $class;                         // set list the the first occurrence, if there happens to be multiplies.
			break;
		}
	}

	// wow, thats a mouth full, lets make it a little more pleasing to the eyes.
	$footnote = &$footnotes['lists'][$list]['entry'];

	// set the current number of list entries
	$listNum = count($footnote)+1;

	$footnote[$listNum]['unique'] = $footnotes['count'];
	$footnote[$listNum]['class'] = implode(' ',$classes);

	$footnotes['nest']++;
	$footnote[$listNum]['data'] = TikiLib::lib('parser')->parse_data_plugin($data,true);
	$footnotes['nest']--;


	$smarty->assign('uniqueId',$footnote[$listNum]['unique']);
	$smarty->assign('unique',$footnote[$listNum]['unique']);
	$smarty->assign('listNum',$listNum);
	$smarty->assign('class',$footnote[$listNum]['class']);
	return $smarty->fetch('templates/wiki-plugins/wikiplugin_footnote.tpl');
}
