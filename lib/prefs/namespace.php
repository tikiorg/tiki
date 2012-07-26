<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_namespace_list()
{
	return array(
		'namespace_enabled' => array(
			'name' => tr('Namespace'),
			'description' => tr('Enable namespaces for wiki pages.'),
			'type' => 'flag',
			'default' => 'y',
		),
		'namespace_separator' => array(
			'name' => tr('Namespace separator'),
			'size' => 5,
			'type' => 'text',
			'default' => '::',
		),
	);	
}
