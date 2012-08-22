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
			'default' => 'n',
			'help' => 'Namespace',
			'keywords' => 'Namespaces',
			'tags' => array('experimental'),
			'perspective' => false,
		),
		'namespace_separator' => array(
			'name' => tr('Namespace separator'),
			'size' => 5,
			'type' => 'text',
			'default' => ':',
			'keywords' => 'Namespaces',
			'perspective' => false,
		),
		'namespace_default' => array(
			'name' => tr('Default namespace'),
			'description' => tr('Namespace to use when creating wiki pages. Should be defined within perspectives.'),
			'type' => 'text',
			'default' => '',
		),
	);	
}
