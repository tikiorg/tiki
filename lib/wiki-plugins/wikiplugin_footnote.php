<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		'icon' => 'img/icons/text_horizontalrule.png',
		'filter' => 'wikicontent',
		'params' => array(
			'sameas' => array(
				'required' => false,
				'name' => tra('Sameas'),
				'description' => tra('Tag to existing footnote'),
				'default' => '',
				'filter' => 'int',
			),
			'class' => array(
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('Add class to footnotearea'),
				'default' => '',
				'filter' => 'text',
			),
		)
	);
}

function wikiplugin_footnote($data, $params)
{
	if (! isset($GLOBALS['footnoteCount'])) {
		$GLOBALS['footnoteCount'] = 0;
		$GLOBALS['footnotesData'] = array();
        $GLOBALS['footnotesClass'] = array();
	}

	if (! empty($data)) {
		$data = trim($data);
		if (! isset($GLOBALS['footnotesData'][$data])) {
            $GLOBALS['footnoteCount']++;
			$GLOBALS['footnotesData'][$GLOBALS['footnoteCount']] = $data;
		    $GLOBALS['footnotesClass'][$GLOBALS['footnoteCount']] = $params["class"];
        
        }

		$number = $GLOBALS['footnoteCount'];
	} elseif (isset($params['sameas'])) {
		$number = $params['sameas'];
	}
    if (isset($params["class"])){
    $class= ' class="'.$params["class"].'"';
    }
	$html = '{SUP()}~np~' . "<a id=\"ref_footnote$number\" href=\"#footnote$number\"$class>$number</a>" . '~/np~{SUP}';

	return $html;
}