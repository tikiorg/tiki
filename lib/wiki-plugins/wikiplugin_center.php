<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_center_info()
{
	return [
		'name' => tra('Center'),
		'documentation' => 'PluginCenter',
		'description' => tra('Center text'),
		'prefs' => ['wikiplugin_center'],
		'body' => tra('text'),
		'iconname' => 'align-center',
		'filter' => 'wikicontent',
		'tags' => [ 'basic' ],
		'introduced' => 1,
		'params' => [
		],
	];
}

function wikiplugin_center($data, $params)
{
	$data = '<div style="text-align:center">' . trim($data) . '</div>';
	return $data;
}
