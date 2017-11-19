<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Tiki-Wiki plugin example
 */
function wikiplugin_foo_info()
{
	return [
		'name' => tra('Foo'),
		'description' => tra('Sample plugin.'),
		'prefs' => ['wikiplugin_example'],
		'body' => tra('text'),
		'params' => [
			'face' => [
				'required' => true,
				'name' => tra('Face'),
				'description' => tra('Font family to use.'),
			],
			'size' => [
				'required' => true,
				'name' => tra('Size'),
				'description' => tra('As defined by CSS.'),
			],
		],
	];
}

function wikiplugin_foo($data, $params)
{
	extract($params, EXTR_SKIP);

	$ret = "foo face=$face size=$size :: $data";
	return $ret;
}
