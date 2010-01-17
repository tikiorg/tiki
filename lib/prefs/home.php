<?php

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
