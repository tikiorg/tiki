<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sup_info()
{
	return array(
		'name' => tra('Superscript'),
		'documentation' => 'PluginSup',
		'description' => tra('Apply superscript font to text'),
		'prefs' => array( 'wikiplugin_sup' ),
		'body' => tra('text'),
		'iconname' => 'superscript',
		'introduced' => 2,
		'filter' => 'wikicontent',
		'tags' => array( 'basic' ),
		'params' => array(
		),
	);
}

function wikiplugin_sup($data, $params)
{
	return "<sup>$data</sup>";
}
