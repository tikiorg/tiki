<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_blog_posts_most_commented_info()  {
		return array(
		'name' => tra('Most commented blog posts'),
		'description' => tra('Displays the specified number of the blog post comments most recently added.'),
		'prefs' => array( 'feature_blogs' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_blog_posts_most_commented( $mod_reference, $module_params ) {
	global $smarty;
	global $dbTiki;
	global $commentslib;
	if(!isset($commentslib)){
		include_once ('lib/commentslib.php');
		$commentslib = new Comments($dbTiki);
	}
	
	
	$result = $commentslib->order_comments_by_count('blog','', $mod_reference['rows']);
	$smarty->assign('modBlogPostsMostCommented', $result['data']);
}
