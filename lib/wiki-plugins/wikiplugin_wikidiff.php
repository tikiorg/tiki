<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_wikidiff.php 57961 2016-03-17 20:01:56Z jonnybradley $

function wikiplugin_wikidiff_info()
{
	global $prefs;

	return array(
		'name' => tra('Wiki Diff'),
		'documentation' => 'PluginWikidiff',
		'description' => tra('Display the differences between two wiki objects'),
		'prefs' => array( 'wikiplugin_wikidiff', 'feature_wiki' ),
		'iconname' => 'code-fork',
		'introduced' => 16.0,
		'format' => 'html',
		'extraparams' => true,
		'params' => array(
			'object_id' => array(
				'required' => true,
				'name' => tra('Object Id'),
				'description' => tra('Object to do a diff on (page name for wiki pages)'),
				'since' => 16.0,
				'default' => '',
				'filter' => 'text',
			),
			'object_type' => array(
				'required' => false,
				'name' => tra('Object Type'),
				'description' => tra('Object type (wiki pages only)'),
				'since' => 16.0,
				'default' => 'wiki page',
				'filter' => 'text',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Wiki Page'), 'value' => 'wiki page'),
				)
			),
			'oldver' => array(
				'required' => true,
				'name' => tra('Old version'),
				'description' => tra('Integer for old version number, or date') ,
				'since' => 16.0,
				'filter' => 'text',
				'default' => '',
			),
			'newver' => array(
				'required' => false,
				'name' => tra('New version'),
				'description' => tra('Integer for old version number, or date') . ' - ' . tra('Leave empty for current version') ,
				'since' => 16.0,
				'filter' => 'text',
				'default' => '',
			),
			'diff_style' => array(
				'required' => false,
				'name' => tra('Diff Style'),
				'description' => tr('Defaults to "diff style" preference if empty'),
				'since' => '16.0',
				'filter' => 'text',
				'default' => $prefs['default_wiki_diff_style'],
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('HTML diff'), 'value' => 'htmldiff'),
					array('text' => tra('Side-by-side diff'), 'value' => 'sidediff'),
					array('text' => tra('Side-by-side diff by characters'), 'value' => 'sidediff-char'),
					array('text' => tra('Inline diff'), 'value' => 'inlinediff'),
					array('text' => tra('Inline diff by characters'), 'value' => 'inlinediff-char'),
					array('text' => tra('Full side-by-side diff'), 'value' => 'sidediff-full'),
					array('text' => tra('Full side-by-side diff by characters'), 'value' => 'sidediff-full-char'),
					array('text' => tra('Full inline diff'), 'value' => 'inlinediff-full'),
					array('text' => tra('Full inline diff by characters'), 'value' => 'inlinediff-full-char'),
					array('text' => tra('Unified diff'), 'value' => 'unidiff'),
					array('text' => tra('Side-by-side view'), 'value' => 'sideview'),
				),
			),
		)
	);
}

function wikiplugin_wikidiff($data, $params)
{
	// TODO refactor: defaults for plugins?
	$defaults = array();
	$plugininfo = wikiplugin_wikidiff_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults["$key"] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_wikidiff');


	$ret = smarty_function_wikidiff($params, $smarty);
	return $ret;
}
