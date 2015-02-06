<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_last_blog_posts_info()
{
	return array(
		'name' => tra('Newest Blog Posts'),
		'description' => tra('Lists the specified number of blogs posts from newest to oldest.'),
		'prefs' => array("feature_blogs"),
		'params' => array(
			'nodate' => array(
				'name' => tra('No date'),
				'description' => tra('If set to "y", the date of posts is not displayed in the module box.') . " " . tra('Default: "n".'),
			),
			'blogid' => array(
				'name' => tra('Blog identifier'),
				'description' => tra('If set to a blog identifier, restricts the blog posts to those in the identified blog.') . " " . tra('Example value: 13.') . " " . tra('Not set by default.'),
				'profile_reference' => 'blog',
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_blog_posts($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');

	$blogId = isset($module_params["blogid"]) ? $module_params["blogid"] : 0;
	$smarty->assign('blogid', $blogId);

	$perms = Perms::get(array( 'type' => 'blog', 'object' => $blogId ));
	TikiLib::lib('tiki')->get_perm_object($blogId, 'blog');

	$blog_posts = TikiLib::lib('blog')->list_blog_posts($blogId, $perms->blog_admin, 0, $mod_reference["rows"], 'created_desc', '', '', TikiLib::lib('tiki')->now);
	$smarty->assign('modLastBlogPosts', $blog_posts["data"]);
	$smarty->assign('nodate', isset($module_params["nodate"]) ? $module_params["nodate"] : 'n');

}
