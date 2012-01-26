<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_visited_blogs_info() {
	return array(
		'name' => tra('Most visited blogs'),
		'description' => tra('Displays the specified number of blogs with links to them, from the most visited one to the least.'),
		'prefs' => array( 'feature_blogs' ),
		'params' => array(
			'showlastpost' => array(
				'name' => tra('Show Last Post'),
				'description' => 'y|n',
				'required' => false,
				'filter' => 'alpha'
			),
			'sort_mode' => array(
				'name' => tra('Sort Mode'),
				'description' => tra('Sort Mode'),
				'required' => false,
				'filter' => 'word'
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_visited_blogs( $mod_reference, $module_params ) {
	global $smarty;
	global $bloglib; require_once('lib/blogs/bloglib.php');
	$with = '';
	if (isset($mod_reference['params']['showlastpost']) && $mod_reference['params']['showlastpost'] == 'y') {
		$with = array('showlastpost'=>'y');
	}
	if (empty($mod_reference['sort_mode'])) {
		$mod_reference['sort_mode'] = 'hits_desc';
	}
	$ranking = $bloglib->list_blogs(0, $mod_reference['rows'], $mod_reference['sort_mode'], '', 'blog', $with);
	
	$smarty->assign('modTopVisitedBlogs', $ranking['data']);
}
