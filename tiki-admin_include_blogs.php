<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_blogs.php,v 1.7 2004-03-29 21:26:28 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}


if (isset($_REQUEST["blogset"]) && isset($_REQUEST["homeBlog"])) {
	check_ticket('admin-inc-blogs');
	$tikilib->set_preference("home_blog", $_REQUEST["homeBlog"]);
	$smarty->assign('home_blog', $_REQUEST["homeBlog"]);
}

if (isset($_REQUEST["blogfeatures"])) {
	check_ticket('admin-inc-blogs');
	if (isset($_REQUEST["feature_blog_rankings"]) && $_REQUEST["feature_blog_rankings"] == "on") {
		$tikilib->set_preference("feature_blog_rankings", 'y');

		$smarty->assign("feature_blog_rankings", 'y');
	} else {
		$tikilib->set_preference("feature_blog_rankings", 'n');

		$smarty->assign("feature_blog_rankings", 'n');
	}

	if (isset($_REQUEST["blog_spellcheck"]) && $_REQUEST["blog_spellcheck"] == "on") {
		$tikilib->set_preference("blog_spellcheck", 'y');

		$smarty->assign("blog_spellcheck", 'y');
	} else {
		$tikilib->set_preference("blog_spellcheck", 'n');

		$smarty->assign("blog_spellcheck", 'n');
	}

	if (isset($_REQUEST["feature_blog_comments"]) && $_REQUEST["feature_blog_comments"] == "on") {
		$tikilib->set_preference("feature_blog_comments", 'y');

		$smarty->assign("feature_blog_comments", 'y');
	} else {
		$tikilib->set_preference("feature_blog_comments", 'n');

		$smarty->assign("feature_blog_comments", 'n');
	}

	if (isset($_REQUEST["feature_blogposts_comments"]) && $_REQUEST["feature_blogposts_comments"] == "on") {
		$tikilib->set_preference("feature_blogposts_comments", 'y');

		$smarty->assign("feature_blogposts_comments", 'y');
	} else {
		$tikilib->set_preference("feature_blogposts_comments", 'n');

		$smarty->assign("feature_blogposts_comments", 'n');
	}

	$tikilib->set_preference("blog_list_order", $_REQUEST["blog_list_order"]);
	$tikilib->set_preference("blog_list_user", $_REQUEST["blog_list_user"]);
	$smarty->assign('blog_list_order', $_REQUEST["blog_list_order"]);
	$smarty->assign('blog_list_user', $_REQUEST['blog_list_user']);
}

if (isset($_REQUEST['bloglistconf'])) {
	check_ticket('admin-inc-blogs');
	if (isset($_REQUEST["blog_list_title"]) && $_REQUEST["blog_list_title"] == "on") {
		$tikilib->set_preference("blog_list_title", 'y');

		$smarty->assign("blog_list_title", 'y');
	} else {
		$tikilib->set_preference("blog_list_title", 'n');

		$smarty->assign("blog_list_title", 'n');
	}

	if (isset($_REQUEST["blog_list_description"]) && $_REQUEST["blog_list_description"] == "on") {
		$tikilib->set_preference("blog_list_description", 'y');

		$smarty->assign("blog_list_description", 'y');
	} else {
		$tikilib->set_preference("blog_list_description", 'n');

		$smarty->assign("blog_list_description", 'n');
	}

	if (isset($_REQUEST["blog_list_activity"]) && $_REQUEST["blog_list_activity"] == "on") {
		$tikilib->set_preference("blog_list_activity", 'y');

		$smarty->assign("blog_list_activity", 'y');
	} else {
		$tikilib->set_preference("blog_list_activity", 'n');

		$smarty->assign("blog_list_activity", 'n');
	}

	if (isset($_REQUEST["blog_list_visits"]) && $_REQUEST["blog_list_visits"] == "on") {
		$tikilib->set_preference("blog_list_visits", 'y');

		$smarty->assign("blog_list_visits", 'y');
	} else {
		$tikilib->set_preference("blog_list_visits", 'n');

		$smarty->assign("blog_list_visits", 'n');
	}

	if (isset($_REQUEST["blog_list_posts"]) && $_REQUEST["blog_list_posts"] == "on") {
		$tikilib->set_preference("blog_list_posts", 'y');

		$smarty->assign("blog_list_posts", 'y');
	} else {
		$tikilib->set_preference("blog_list_posts", 'n');

		$smarty->assign("blog_list_posts", 'n');
	}

	if (isset($_REQUEST["blog_list_lastmodif"]) && $_REQUEST["blog_list_lastmodif"] == "on") {
		$tikilib->set_preference("blog_list_lastmodif", 'y');

		$smarty->assign("blog_list_lastmodif", 'y');
	} else {
		$tikilib->set_preference("blog_list_lastmodif", 'n');

		$smarty->assign("blog_list_lastmodif", 'n');
	}

	if (isset($_REQUEST["blog_list_user"]) && $_REQUEST["blog_list_user"] == "on") {
		$tikilib->set_preference("blog_list_user", 'y');

		$smarty->assign("blog_list_user", 'y');
	} else {
		$tikilib->set_preference("blog_list_user", 'n');

		$smarty->assign("blog_list_user", 'n');
	}

	if (isset($_REQUEST["blog_list_created"]) && $_REQUEST["blog_list_created"] == "on") {
		$tikilib->set_preference("blog_list_created", 'y');

		$smarty->assign("blog_list_created", 'y');
	} else {
		$tikilib->set_preference("blog_list_created", 'n');

		$smarty->assign("blog_list_created", 'n');
	}
}

if (isset($_REQUEST["blogcomprefs"])) {
	check_ticket('admin-inc-blogs');
	if (isset($_REQUEST["blog_comments_per_page"])) {
		$tikilib->set_preference("blog_comments_per_page", $_REQUEST["blog_comments_per_page"]);

		$smarty->assign('blog_comments_per_page', $_REQUEST["blog_comments_per_page"]);
	}

	if (isset($_REQUEST["blog_comments_default_ordering"])) {
		$tikilib->set_preference("blog_comments_default_ordering", $_REQUEST["blog_comments_default_ordering"]);

		$smarty->assign('blog_comments_default_ordering', $_REQUEST["blog_comments_default_ordering"]);
	}
}
ask_ticket('admin-inc-blogs');

$blogs = $tikilib->list_blogs(0, -1, 'created_desc', '');
$smarty->assign_by_ref('blogs', $blogs["data"]);

?>
