<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_home_list() {
	global $tikilib;

	$allblogs = $tikilib->list_blogs(0, -1, 'created_desc', '');
	$listblogs = array();

	if ($allblogs['cant'] > 0) {
		foreach ($allblogs['data'] as $blog) {
			$listblogs[ $blog['blogId'] ] = $blog['title'];
		}
	} else {
		$listblogs[''] = tra('No blog available');
	}

	return array(
		'home_blog' => array(
			'name' => tra('Home Blog (main blog)'),
			'type' => 'list',
			'options' => $listblogs,
		),
	);
}
