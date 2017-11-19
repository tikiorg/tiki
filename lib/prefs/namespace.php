<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_namespace_list()
{
	return [
		'namespace_enabled' => [
			'name' => tr('Namespace'),
			'description' => tr('Enable namespaces feature for wiki pages.'),
			'type' => 'flag',
			'default' => 'n',
			'help' => 'Namespaces',
			'keywords' => 'Namespaces',
			'tags' => ['experimental'],
			'perspective' => false,
		],
		'namespace_separator' => [
			'name' => tr('Namespace separator'),
			'description' => tra('Select the character, symbol, or text to use as the namespace separator.'),
			'size' => 5,
			'type' => 'text',
			'default' => ':_:',
			'keywords' => 'Namespaces',
			'perspective' => false,
		],
		'namespace_default' => [
			'name' => tr('Default namespace'),
			'description' => tr('Namespace to use when creating wiki pages. Should be defined within perspectives.'),
			'type' => 'text',
			'default' => '',
			'detail' => tra('This should only be set for perspectives, and not globally.'),
		],
		'namespace_indicator_in_structure' => [
			'name' => tra('Hide namespace indicator in structure path'),
			'description' => tra('Hide namespace indicator in structure path.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'namespace_indicator_in_page_title' => [
			'name' => tra('Hide namespace indicator in page title'),
			'description' => tra('Hide namespace indicator in page title.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'namespace_force_links' => [
			'name' => tra('Force all non-namespace page links to the same namespace'),
			'description' => tra('If the current page is in a namespace, all links without a namespace will have it added automatically'),
			'type' => 'flag',
			'default' => 'n',
		],
	];
}
