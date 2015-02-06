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
function module_top_blog_posters_info()
{
	return array(
		'name' => tra('Top Blog Posters'),
		'description' => tra('Displays the specified number of users who posted to blogs, starting with the one having most posts.'),
		'prefs' => array('feature_blogs'),
		'params' => array(
			'blogId' => array(
				'name' => tra('Blog ID'),
				'description' => tra('Limit to a blog'),
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
function module_top_blog_posters($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$cond = null;
	if (!empty($module_params['blogId'])) {
		$cond = $module_params['blogId'];
	}
	$bloggers =  TikiLib::lib('blog')->top_bloggers($mod_reference['rows'], $cond);

	$smarty->assign_by_ref('modTopBloggers', $bloggers['data']);
}
