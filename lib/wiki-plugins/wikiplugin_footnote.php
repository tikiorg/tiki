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
			'tag' => array(
				'required' => false,
				'name' => tra('Tag'),
				'description' => tra('Tag footnote with unique identifier'),
				'since' => '15.0',
				'default' => '',
				'filter' => 'alnum',
				'accepted' => tra('One word made of alphanumeric characters'),
			),
			'sameastag' => array(
				'required' => false,
				'name' => tra('Same as Tag'),
				'description' => tra('Tag to existing footnote by its Tag'),
				'since' => '15.0',
				'default' => '',
				'filter' => 'alnum',
				'accepted' => tra('One word made of alphanumeric characters'),
			),
			'sameas' => array(
				'required' => false,
				'name' => tra('Same as'),
				'description' => tra('Tag to existing footnote number'),
				'since' => '5.0',
				'default' => '',
				'filter' => 'alnum',
				'accepted' => tra('tag name (since 17) or footnote number'),
			),
			'class' => array(
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('Add class to footnotearea'),
				'since' => '14.0',
				'default' => '',
				'filter' => 'alnumspace',
				'accepted' => tra('Valid CSS class'),
			),
			'scheme' => array(
				'required' => false,
				'name' => tra('Scheme'),
				'description' => tra('Segregate footnotes by class in footnotearea. Apply different numbering style (optional)'),
				'since' => '17.0',
				'default' => '',
				'filter' => 'text',
				'accepted' => tra('Scheme strings (ClassName:((Number Style|numStyle))). Multiples may be separated by | (roman-upper:className|decimal)'),
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
		$footnotes['tag'] = array();      // record of tags and associated footnote numbers
		$footnotes['lists'] = array();    // data for general footnotes
		$footnotes['lists']['.def.']['listType'] = 'decimal';    // set the default display type for lists
	}

	if (isset($params['scheme']))
		setScheme($params['scheme']);
	
	$data = trim($data);
	if (! empty($data)) {
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

		if (isset($params["tag"]) && !isset($footnotes['tag'][$params["tag"]])) {  // do nothing if duplicate tag
			// Keep track of where data can be found for this Tag
			$footnotes['tag'][$params["tag"]]['class'] = $list;
			$footnotes['tag'][$params["tag"]]['num'] = $listNum;
			$footnote[$listNum]['unique'] = $params["tag"];
		}else
			$footnote[$listNum]['unique'] = $footnotes['count'];

		$footnote[$listNum]['class'] = implode(' ',$classes);

		$footnotes['nest']++;
		$footnote[$listNum]['data'] = TikiLib::lib('parser')->parse_data_plugin($data,true);
		$footnotes['nest']--;


		$smarty->assign('uniqueId',$footnote[$listNum]['unique']);
		$smarty->assign('unique',$footnote[$listNum]['unique']);
		$smarty->assign('listNum',$listNum);
		$smarty->assign('class',$footnote[$listNum]['class']);
		$smarty->assign('listType',$footnotes['lists'][$list]['listType']);
		return $smarty->fetch('templates/wiki-plugins/wikiplugin_footnote.tpl');
	} else {                             // if there is no data
		if (isset($params['sameastag']))
			$sameas = $params['sameastag'];
		elseif (isset($params['sameas']))
			$sameas = $params['sameas'];
		if (isset($sameas)) {
			if (isset($footnotes['tag'][$sameas])) {
				$listNum =$footnotes['tag'][$sameas]['num'];
				$uniqueId = $sameas . '-' .(@count($footnotes['lists'][ $footnotes['tag'][$sameas]['class'] ]['entry'][$listNum]['sameas'])+1);
				$footnotes['lists'][$footnotes['tag'][$sameas]['class']]['entry'][$listNum]['sameas'][] = $uniqueId;
				$smarty->assign('listNum',$listNum);
				$smarty->assign('uniqueId', $uniqueId);
				$smarty->assign('unique',$sameas);
				$smarty->assign('listType',$footnotes['lists'][$footnotes['tag'][$sameas]['class']]['listType']);
				$smarty->assign('class',$footnotes['lists'][$footnotes['tag'][$sameas]['class']]['entry'][$listNum]['class']);
			} elseif ((string)(int)$sameas === (string)$sameas) {   // else if the value is a integer
				$smarty->assign('listNum',$sameas);// legacy support for number pointing.
				$smarty->assign('listType',$footnotes['lists']['.def.']['listType']);
				if (isset($footnotes['lists']['.def.']['entry'][$sameas])) {    // if the entry already exists
					$uniqueId = $sameas . '-' . (count($footnotes['lists']['.def.']['entry'][$sameas]['sameas'])+ 1);
					$footnotes['lists']['.def.']['entry'][$sameas]['sameas'][] = $uniqueId;
					$smarty->assign('uniqueId',$uniqueId);
					$smarty->assign('unique', $footnotes['lists']['.def.']['entry'][$sameas]['unique']);
					$smarty->assign('class', $footnotes['lists']['.def.']['entry'][$sameas]['class']);
				}else{                 // legacy support. These values use to be static
					$smarty->assign('unique', $sameas);
					$smarty->assign('uniqueId', '');
					$smarty->assign('class', '');
				}
			} else {
				// The tag does not exist (yet) !!!!
				return '<sup>' . tra('Error: Tag not found in any previous footnote') . '</sup>';
			}
			return $smarty->fetch('templates/wiki-plugins/wikiplugin_footnote.tpl');
		}
	}

	return '';
}

function setScheme($rawScheme)
{
	global $footnotes;
	$classes = explode('|', $rawScheme);
	foreach ($classes as $class) {
		$scheme = explode(':', $class);
		if (!isset($scheme[1]))
			$scheme[1] = '.def.';                      // if no class specified, use the default list
		$footnotes['lists'][$scheme[1]]['listType'] = $scheme[0];
	}
}
