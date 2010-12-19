<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* by robferguson
 *
 * FOOTNOTE plugin. Inserts a superscripted number where the plugin is written starting with one and counting up as the additional footnotes are added.
 *
 * Syntax:
 *
 * {FOOTNOTE()/}
 */
function wikiplugin_footnote_help()
{
	return tra('Inserts a superscripted footnote number next to text and takes in footnote as parameter')
						. ':<br />~np~{FOOTNOTE()}insert footnote here{FOOTNOTE}~/np~' 
						;
}

function wikiplugin_footnote_info()
{
	return array(
		'name' => tra('Footnote'),
		'documentation' => 'PluginFootnote',
		'description' => tra('Create automatically numbered footnotes (together with PluginFootnoteArea)'),
		'prefs' => array('wikiplugin_footnote'),
		'body' => tra('The footnote'),
		'icon' => 'pics/icons/text_horizontalrule.png',
		'params' => array(
			'sameas' => array(
				'required' => false,
				'name' => tra('Sameas'),
				'description' => tra('Tag to existing footnote'),
				'default' => ''
			),
			'checkDuplicate' => array(
				'required' => false,
				'name' => tra('CheckDuplicate'),
				'description' => tra('Check for duplicate footnotes'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			)
		)
	);
}

function wikiplugin_footnote($data, $params)
{
	if (! isset($GLOBALS['footnoteCount'])) {
		$GLOBALS['footnoteCount'] = 0;
	}

	if (empty($params)) {
		$GLOBALS['footnoteCount']++;
		$footnoteCount = $GLOBALS['footnoteCount'];
		$GLOBALS['footnotesData'][] = trim($data);
	} else {
		extract($params, EXTR_SKIP);
		if (!empty($sameas)) {
			$footnoteCount = $sameas;
		} else {
			if (ucfirst($checkDuplicate) == 'Y') {
				foreach($GLOBALS["footnotesData"] as $key => $value) {
					if ( strcmp(trim($data), $value) == 0 ) {
						$footnoteCount = $key + 1;
						break;
					}
				}
			}
		}   // else for if (!empty($sameas
	}    // else for if (empty($params

	$html = '{SUP()}'
				. "<a id=\"ref_footnote$footnoteCount\" href=\"#footnote$footnoteCount\">$footnoteCount</a>"
				.	'{SUP}';

	return $html;
}
