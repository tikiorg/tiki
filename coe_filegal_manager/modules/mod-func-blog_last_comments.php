<?php
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_blog_last_comments_info() {
	return array(
		'name' => tra('Last blog post comments'),
		'description' => tra('Displays the specified number of the blog post comments most recently added.'),
		'prefs' => array( 'feature_blogs' ),
		'params' => array(
			'moretooltips' => array(
				'name' => tra('More in tooltips'),
				'description' => tra('If set to "y", the post on which a comment is made is not displayed in the module box, but instead moved in the item\'s tooltip.') . " " . tr('Default: "n".')
			),
			'nodate' => array(
				'name' => tra('No date'),
				'description' => tra('If set to "y", the date at which comments were posted is not displayed in the module box.') . " " . tr('Default: "n".')
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_blog_last_comments( $mod_reference, $module_params ) {
	global $bloglib, $smarty;
	include_once ('lib/blogs/bloglib.php');
	$comments = $bloglib->list_blog_post_comments('y', $mod_reference["rows"]);
	
	$smarty->assign('comments', $comments['data']);
	$smarty->assign('moretooltips', isset($module_params['moretooltips']) ? $module_params['moretooltips'] : 'n');
	$smarty->assign('nodate', isset($module_params['nodate']) ? $module_params['nodate'] : 'n');
}
