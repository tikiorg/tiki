<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_categories_list()
{
	return [
		'categories_used_in_tpl' => [
			'name' => tra('Provides the current categories to Smarty templates'),
			'description' => tra('When enabled, the $objectCategoryIds variable is set to the identifiers of the categories of the object being viewed. This allows showing alternate content depending on the categories of the current object, but reduces performance.'),
			'type' => 'flag',
			'perspective' => false,
			'help' => 'http://themes.tiki.org/Template+Tricks',
			'dependencies' => [
				'feature_categories',
			],
			'default' => 'n',
		],
		'categories_add_class_to_body_tag' => [
			'name' => tra('Categories to add as CSS classes to <body>'),
			'description' => tra('Pages in selected categories will have a class with syntax like "cat_catname" added to the body tag.'),
			'separator' => ',',
			'type' => 'text',
			'size' => '15',
			'dependencies' => [
				'feature_categories', 'categories_used_in_tpl',
			],
			'profile_reference' => 'category',
			'default' => [''], //empty string needed to keep preference from setting unexpectedly
		],
		'categories_cache_refresh_on_object_cat' => [
			'name' => tra('Clear cache upon category change'),
			'description' => tra('A cache is used to avoid having to fetch all categories from db every time; this clears the cache when an object is categorized to keep count up to date.'),
			'type' => 'flag',
			'dependencies' => [
				'feature_categories',
			],
			'default' => 'y',
		],
	];
}
