<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_footnotearea_info()
{
	return array(
		'name' => tra('Footnote Area'),
		'documentation' => 'PluginFootnoteArea',
		'description' => tra('Create automatically numbered footnotes (together with PluginFootnote)'),
		'prefs' => array('wikiplugin_footnotearea'),
		'iconname' => 'superscript',
		'format' => 'html',
		'introduced' => 3,
		'params' => array(
        'class' => array(
          'required' => false,
          'name' => tra('Class'),
          'description' => tra('Filter footnotearea by footnote class'),
          'since' => '14.0',
          'default' => '',
          'filter' => 'alnum',
          'accepted' => tra('Valid CSS class'),
        ),
		),
	);
}

function wikiplugin_footnotearea($data, $params)
{

	if ( isset($params['class']) ) {
		$html = '<div class="footnotearea ' . $params['class'] . '">';
	} else {
		$html = '<div class="footnotearea">';
	}

	foreach ($GLOBALS["footnotesData"] as $number => $data) {
		if ( isset($params['class']) && $GLOBALS["footnotesClass"][$number] != $params['class'] ) {
			continue;
		}
		$class = "onefootnote";
		if (isset($GLOBALS["footnotesClass"][$number])){
			$class .= " ".$GLOBALS["footnotesClass"][$number];
		}
		$html .= '<div class="'.$class.'" id="footnote' . $number . '">';
		$html .= '<a href="#ref_footnote' . $number . '">'. $number . '.</a> ';
		$html .= '~/np~' . $data . '~np~';
		$html .= '</div>';
	}
	$html .= '</div>';
	
	return $html;
}
