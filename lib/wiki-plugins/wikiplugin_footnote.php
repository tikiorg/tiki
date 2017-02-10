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
				'filter' => 'int',
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
		)
	);
}

function wikiplugin_footnote($data, $params)
{
	if (! isset($GLOBALS['footnoteCount'])) {
		$GLOBALS['footnoteCount'] = 0;	// Footnote displayed number
		$GLOBALS['footnotesData'] = array();	// Data associated to footnote number
		$GLOBALS['footnotesClass'] = array();	// Class associated to footnote number
		$GLOBALS['footnotesCountTag'] = array();	// Number associated to footnote Tag
		$GLOBALS['footnotesDuplicates'] = array();	// Footnote number for given data and class
	}
	if ( ! isset($params["class"]) ) $params["class"] = '';

	if (! empty($data)) {
		$data = trim($data);
		// Check if data is duplicate for the given class
		if (! isset($GLOBALS['footnotesDuplicates'][$data][$params["class"]]) ) {
			// New footnote
			$GLOBALS['footnoteCount']++;
			$GLOBALS['footnotesData'][$GLOBALS['footnoteCount']] = $data;
			$GLOBALS['footnotesDuplicates'][$data][$params["class"]] = $GLOBALS['footnoteCount'];
			$GLOBALS['footnotesClass'][$GLOBALS['footnoteCount']] = $params["class"];
		} else {
			// Duplicate footnote: Footnote content already exists as footnote "number" for present class
			// Nothing to do
		}
		$number = $GLOBALS['footnotesDuplicates'][$data][$params["class"]];
		if ( isset($params["tag"]) ) {
			// Keep track of where data can be found for this Tag
			$GLOBALS['footnotesCountTag'][$params["tag"]] = $number;
		}
	} elseif (isset($params['sameastag'])) {
		if ( isset($GLOBALS['footnotesCountTag'][$params["sameastag"]]) ) {
			$number = $GLOBALS['footnotesCountTag'][$params["sameastag"]];
		} else {
			// The tag does not exist (yet) !!!!
			return '~np~' . "<sup >" . tra('Error: Tag not found in any previous footnote') . "</sup>" . '~/np~';
		}
	} elseif (isset($params['sameas'])) {
		$number = $params['sameas'];
	}

	$class= ' class="'.$params["class"].'"';

	// Recursive code to handle nested footnotes
	if (preg_match('/{FOOTNOTE\([\w\W]*?}[\w\W]*{FOOTNOTE}/i',$data,$match)) {
		global $tikilib;
		$tikilib->parse_data($match[0]);
	}

	$html = '~np~' . "<sup class=\"footnote$number\"><a id=\"ref_footnote$number\" href=\"#footnote$number\"$class>[$number]</a></sup>" . '~/np~';

	return $html;
}
