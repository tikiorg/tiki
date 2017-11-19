<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_file_list()
{
	return [
		'file_galleries_comments_per_page' => [
			'name' => tra('Default number per page'),
			'description' => tra('Number of comments per page'),
			'type' => 'text',
			'size' => '5',
			'units' => tra('comments'),
			'default' => 10,
		],
		'file_galleries_comments_default_ordering' => [
			'name' => tra('Default order'),
			'description' => tra('default ordering algorithm'),
			'type' => 'list',
			'options' => [
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			],
			'default' => 'points_desc',
		],
		'file_galleries_use_jquery_upload' => [
			'name' => tra('Use jQuery upload'),
			'description' => tra('Use the improved Tiki 15+ upload page'),
			'type' => 'flag',
			'default' => 'y',
			'dependencies' => [
				'feature_file_galleries',
				'feature_jquery_ui',
			],
		],
		'file_galleries_redirect_from_image_gallery' => [
			'name' => tra('Redirect to file gallery'),
			'description' => tra('If enabled, redirect all requests to images that were migrated from the image gallery to the corresponding file in the file gallery'),
			'type' => 'flag',
			'default' => 'n',
		],
	];
}
