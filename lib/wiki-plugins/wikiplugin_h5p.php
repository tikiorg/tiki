<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_h5p_info()
{
	return [
		'name' => tra('H5P'),
		'documentation' => 'PluginH5P',
		'description' => tra(''),
		'prefs' => ['wikiplugin_h5p', 'h5p_enabled'],
		'iconname' => 'html',
		'format' => 'html',
		'introduced' => 16,
		'params' => [
			'fileId' => [
				'required' => false,
				'name' => tra('File ID'),
				'description' => tr('The H5P file in a file gallery'),
				'since' => '17.0',
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'file',
				'area' => 'fgal_picker_id',
				'type' => 'fileId',
			],
		],
	];
}

function wikiplugin_h5p($data, $params)
{
	global $page;
	static $instance = 0;
	$instance++;

	$smarty = TikiLib::lib('smarty');

	$smarty->loadPlugin('smarty_function_service_inline');

	$params['controller'] = 'h5p';
	$params['action'] = 'embed';

	if (! empty($page)) {	// only wiki pages for now
		$params['page'] = $page;
		$params['index'] = $instance;
	}

	return smarty_function_service_inline($params, $smarty);
}

function wikiplugin_h5p_rewrite($data, $params, $context)
{
	if (! empty($params['fileId'])) {
		return "{h5p fileId=\"{$params['fileId']}\"}";
	} else {
		return "{h5p}";
	}
}

