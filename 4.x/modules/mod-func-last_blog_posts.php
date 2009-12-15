<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_blog_posts_info() {
	return array(
		'name' => tra('Last blog posts'),
		'description' => tra('Lists the specified number of blogs posts from newest to oldest.'),
		'prefs' => array("feature_blogs"),
		'params' => array(
			'nodate' => array(
				'name' => tra('No date'),
				'description' => tra('If set to "y", the date of posts is not displayed in the module box.') . " " . tra('Default: "n".')
			),
			'blogid' => array(
				'name' => tra('Blog identifier'),
				'description' => tra('If set to a blog identifier, restricts the blog posts to those in the identified blog.') . " " . tra('Example value: 13.') . " " . tra('Not set by default.')
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_blog_posts( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	
	$blogId = isset($module_params["blogid"]) ? $module_params["blogid"] : -1;
	$smarty->assign('blogid', $blogId);
	$ranking = $tikilib->list_posts(0, $mod_reference["rows"], 'created_desc', '', $blogId);
	$smarty->assign('modLastBlogPosts', $ranking["data"]);
}
