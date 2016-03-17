<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_blogs_list()
{
	return array(
		'blogs_feature_copyrights' => array(
			'name' => tra('Blogs'),
            'description' => tra('Allows for addition of individual copyright notices on blog posts'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
			'default' => 'n',
		),
	);
}
