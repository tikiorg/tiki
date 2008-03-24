<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_blogs.php,v 1.20.2.1 2007-10-20 05:21:41 pkdille Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


if (isset($_REQUEST["blogset"]) && isset($_REQUEST["homeBlog"])) {
	check_ticket('admin-inc-blogs');
	$tikilib->set_preference("home_blog", $_REQUEST["homeBlog"]);
}

if (isset($_REQUEST["blogfeatures"])) {
	check_ticket('admin-inc-blogs');
	$pref_toggles = array(
	"feature_blog_rankings",
	"blog_spellcheck",
	"feature_blog_comments",
        "feature_blogposts_comments",
	"feature_blog_heading",
	"feature_trackbackpings",
	"feature_blogposts_pings"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	simple_set_value ("blog_list_order");
	simple_set_value ("blog_list_user");
	simple_set_value ('feature_blog_mandatory_category');
}

if (isset($_REQUEST['bloglistconf'])) {
	check_ticket('admin-inc-blogs');
	$pref_toggles = array(
	"blog_list_title",
	"blog_list_description",
	"blog_list_activity",
	"blog_list_visits",
	"blog_list_posts",
	"blog_list_created",
	"blog_list_lastmodif"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	simple_set_value ("blog_list_user");
	simple_set_int("blog_list_title_len");
}

if (isset($_REQUEST["blogcomprefs"])) {
	check_ticket('admin-inc-blogs');
	simple_set_value ("blog_comments_per_page");
	simple_set_value ("blog_comments_default_ordering");
}
if ($prefs['feature_categories'] == 'y') {
	include_once('lib/categories/categlib.php');
	$catree = $categlib->get_all_categories();
	$smarty->assign('catree', $catree);
}
ask_ticket('admin-inc-blogs');

$blogs = $tikilib->list_blogs(0, -1, 'created_desc', '');
$smarty->assign_by_ref('blogs', $blogs["data"]);

?>
