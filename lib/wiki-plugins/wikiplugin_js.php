<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_js_info()
{
	return array(
		'name' => tra('JavaScript'),
		'documentation' => 'PluginJS',
		'description' => tra('Add JavaScript code'),
		'prefs' => array( 'wikiplugin_js' ),
		'body' => tra('JavaScript code'),
		'validate' => 'all',
		'filter' => 'rawhtml_unsafe',
		'icon' => 'img/icons/script_code_red.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'file' => array(
				'required' => false,
				'name' => tra('File'),
				'description' => tra('JavaScript filename'),
				'filter' => 'url',
				'default' => '',
			),
		),
	);
}
function wikiplugin_js($data, $params)
{
	extract($params, EXTR_SKIP);
	if (isset($file)) {
		$ret =  "~np~<script type=\"text/javascript\" src=\"$file\"></script> ~/np~";
	} else {
		$ret = '';
	}
	if ($data) {
		$ret .= "~np~<script type=\"text/javascript\">".$data."</script>~/np~"; 
	}
	return $ret;
}
