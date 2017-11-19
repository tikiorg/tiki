<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_footnotearea_info()
{
	return [
		'name' => tra('Footnote Area'),
		'documentation' => 'PluginFootnoteArea',
		'description' => tra('Create automatically numbered footnotes (together with PluginFootnote)'),
		'prefs' => ['wikiplugin_footnote'],
		'iconname' => 'superscript',
		'format' => 'html',
		'introduced' => 3,
		'params' => [
			'class' => [
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('Filter footnotearea by footnote class'),
				'since' => '17.0',
				'default' => '',
				'filter' => 'alnum',
				'accepted' => tra('Valid CSS class'),
			],
		],
	];
}

function wikiplugin_footnotearea($data, $params)
{
	global $footnotes;
	$smarty = TikiLib::lib('smarty');

	$html = '';
	if (isset($params['class'])) {                                       // if class was given
		if (isset($footnotes['lists'][$params['class']])) {        // if the class exists
			$html = genFootnoteArea($params['class'], $footnotes['lists'][$params['class']]);
			unset($footnotes['lists'][$params['class']]['entry']);
		}
	} else {                                                        // if no params are given, render in default way
		foreach ($footnotes['lists'] as $listName => $list) {
			$html .= genFootnoteArea($listName, $list);
		}
		unset($footnotes['lists']);
	}

	return $html;
}

/**
 *
 * Generate footnote area HTML, based upon a given class ( and data)
 *
 * @param $listName string the name of the class
 * @param $list array the array of the class to turn into HTML
 *
 * @return string
 */

function genFootnoteArea($listName, $list)
{

	$smarty = TikiLib::lib('smarty');
	if ($listName === '.def.') {
		$smarty->assign('listName', '');                     // if default, dont include a class name
	} else {
		$smarty->assign('listName', ' ' . $listName);                // if we are in a list, fix spacing up nice
	}
	$smarty->assign('footnotes', $list['entry']);

	return $smarty->fetch('templates/wiki-plugins/wikiplugin_footnotearea.tpl');
}
