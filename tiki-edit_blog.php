<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
$bloglib = TikiLib::lib('blog');

$access->check_feature('feature_blogs');
$access->check_permission('tiki_p_create_blogs');

if (isset($_REQUEST["blogId"])) {
	$blogId = $_REQUEST["blogId"];
} else {
	$blogId = 0;
}

$smarty->assign('individual', 'n');

$tikilib->get_perm_object($blogId, 'blog');

$smarty->assign('blogId', $blogId);
$smarty->assign('title', '');
$smarty->assign('description', '');
$smarty->assign('public', 'y');
$smarty->assign('use_find', 'n');
$smarty->assign('add_date', 'y');
$smarty->assign('use_title', 'y');
$smarty->assign('use_title_in_post', 'y');
$smarty->assign('use_description', 'y');
$smarty->assign('use_breadcrumbs', 'n');
$smarty->assign('use_author', 'y');
$smarty->assign('allow_comments', 'y');
$smarty->assign('show_avatar', 'n');
$smarty->assign('show_related', 'n');
$smarty->assign('related_max', 5);
$smarty->assign('maxPosts', 25);
$smarty->assign('use_excerpt', 'n');
$smarty->assign('creator', $user);


if (!isset($created)) {
	$created=time();
	$smarty->assign('created', $created);
}

if (!isset($lastModif)) {
	$lastModif=time();
	$smarty->assign('lastModif', $lastModif);
}

if (isset($_REQUEST["blogId"]) && $_REQUEST["blogId"] > 0) {
	// Check permission
	$data = $bloglib->get_blog($_REQUEST["blogId"]);

	if ($data["user"] != $user || !$user) {
		if ($tiki_p_blog_admin != 'y') {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to edit this blog"));

			$smarty->display("error.tpl");
			die;
		}
	}

	$smarty->assign('title', $data["title"]);
	$smarty->assign('description', $data["description"]);
	$smarty->assign('public', $data["public"]);
	$smarty->assign('add_date', $data["add_date"]);
	$smarty->assign('use_title', $data["use_title"]);
	$smarty->assign('use_title_in_post', $data["use_title_in_post"]);
	$smarty->assign('use_description', $data["use_description"]);
	$smarty->assign('use_breadcrumbs', $data["use_breadcrumbs"]);
	$smarty->assign('use_author', $data["use_author"]);
	$smarty->assign('allow_comments', $data["allow_comments"]);
	$smarty->assign('show_avatar', $data["show_avatar"]);
	$smarty->assign('show_related', $data["show_related"]);
	$smarty->assign('related_max', $data["related_max"]);
	$smarty->assign('use_find', $data["use_find"]);
	$smarty->assign('maxPosts', $data["maxPosts"]);
	$smarty->assign('use_excerpt', $data["use_excerpt"]);
	$smarty->assign('creator', $data["user"]);
	$smarty->assign('alwaysOwner', $data["always_owner"]);

}

if (isset($_REQUEST["heading"]) and $tiki_p_edit_templates == 'y') {
	// Sanitization cleanup
	$heading = preg_replace('/st<x>yle="[^"]*"/', 'style_dangerous', $_REQUEST["heading"]);
} elseif (!isset($data["heading"])) {
	$heading = file_get_contents($smarty->get_filename('blog_heading.tpl'));
	if (!$heading) {
		$heading = '';
	}
} else {
	$heading = $data["heading"];
}

if (isset($_REQUEST["post_heading"]) and $tiki_p_edit_templates == 'y') {
	// Sanitization cleanup
	$post_heading = preg_replace('/st<x>yle="[^"]*"/', 'style_dangerous', $_REQUEST["post_heading"]);
} elseif (!isset($data["post_heading"])) {
	$post_heading = file_get_contents($smarty->get_filename('blog_post_heading.tpl'));
	if (!$post_heading) {
		$post_heading = '';
	}
} else {
	$post_heading = $data["post_heading"];
}

$smarty->assign_by_ref('heading', $heading);
$smarty->assign_by_ref('post_heading', $post_heading);
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);

$category_needed = false;
if (isset($_REQUEST["save"]) && $prefs['feature_categories'] == 'y' && $prefs['feature_blog_mandatory_category'] >=0 && (empty($_REQUEST['cat_categories']) || count($_REQUEST['cat_categories']) <= 0)) {
		$category_needed = true;
		$smarty->assign('category_needed', 'y');
} elseif (isset($_REQUEST["save"]) || isset($_REQUEST['preview'])) {
	check_ticket('edit-blog');
	if (isset($_REQUEST["public"]) && $_REQUEST["public"] == 'on') {
		$public = 'y';
	} else {
		$public = 'n';
	}

	$allow_comments = isset($_REQUEST["allow_comments"]) ? 'y' : 'n';
	$show_avatar = isset($_REQUEST['show_avatar']) ? 'y' : 'n';
	$show_related = isset($_REQUEST['show_related']) ? 'y' : 'n';
	$related_max = isset($_REQUEST['related_max']) ? $_REQUEST['related_max'] : 5;
	$use_excerpt = isset($_REQUEST['use_excerpt']) ? 'y' : 'n';
	$use_find = isset($_REQUEST['use_find']) ? 'y' : 'n';
	$use_title = isset($_REQUEST['use_title']) ? 'y' : 'n';
	$use_title_in_post = isset($_REQUEST['use_title_in_post']) ? 'y' : 'n';
	$use_description = isset($_REQUEST['use_description']) ? 'y' : 'n';
	$use_breadcrumbs = isset($_REQUEST['use_breadcrumbs']) ? 'y' : 'n';
	$use_author = isset($_REQUEST['use_author']) ? 'y' : 'n';
	$add_date = isset($_REQUEST['add_date']) ? 'y' : 'n';
	$alwaysOwner = isset($_REQUEST['alwaysOwner']) ? 'y' : 'n';

	if (isset($_REQUEST["save"])) {
		$bid = $bloglib->replace_blog(
			$_REQUEST["title"],
			$_REQUEST["description"], $_REQUEST["creator"], $public,
			$_REQUEST["maxPosts"], $_REQUEST["blogId"],
			$heading, $use_title, $use_title_in_post, $use_description, $use_breadcrumbs, $use_author, $add_date, $use_find,
			$allow_comments, $show_avatar, $alwaysOwner, $post_heading, $show_related, $related_max, $use_excerpt
		);

		$cat_type = 'blog';
		$cat_objid = $bid;
		$cat_desc = substr($_REQUEST["description"], 0, 200);
		$cat_name = $_REQUEST["title"];
		$cat_href = "tiki-view_blog.php?blogId=" . $cat_objid;
		include_once ("categorize.php");

		header("location: tiki-list_blogs.php?blogId=$bid");
		die;
	}
}

if (isset($_REQUEST['preview']) || $category_needed) {
	$smarty->assign('title', $_REQUEST["title"]);

	$smarty->assign('description', $_REQUEST["description"]);
	$smarty->assign('public', isset($_REQUEST["public"]) ? 'y' : 'n');
	$smarty->assign('use_find', isset($_REQUEST["use_find"]) ? 'y' : 'n');
	$smarty->assign('use_title', isset($_REQUEST["use_title"]) ? 'y' : 'n');
	$smarty->assign('use_title_in_post', isset($_REQUEST["use_title_in_post"]) ? 'y' : 'n');
	$smarty->assign('use_description', isset($_REQUEST["use_description"]) ? 'y' : 'n');
	$smarty->assign('use_breadcrumbs', isset($_REQUEST["use_breadcrumbs"]) ? 'y' : 'n');
	$smarty->assign('use_author', isset($_REQUEST["use_author"]) ? 'y' : 'n');
	$smarty->assign('show_avatar', isset($_REQUEST["show_avatar"]) ? 'y' : 'n');
	$smarty->assign('show_related', isset($_REQUEST["show_related"]) ? 'y' : 'n');
	$smarty->assign('related_max', isset($_REQUEST['related_max']) ? $_REQUEST['related_max'] : 5);
	$smarty->assign('use_excerpt', isset($_REQUEST['use_excerpt']) ? 'y' : 'n');
	$smarty->assign('add_date', isset($_REQUEST["add_date"]) ? 'y' : 'n');
	$smarty->assign('allow_comments', isset($_REQUEST["allow_comments"]) ? 'y' : 'n');
	$smarty->assign('maxPosts', $_REQUEST["maxPosts"]);
	$smarty->assign('heading', $heading);
	$smarty->assign('creator', $_REQUEST["creator"]);

	$smarty->assign(
		'blog_data', array(
			'title' => $_REQUEST["title"],
			'description' => $_REQUEST["description"],
			'creator' => $_REQUEST["creator"],
			'public' => $public,
			'maxPosts' => $_REQUEST["maxPosts"],
			'blogId' => $_REQUEST["blogId"],
			'heading' => $heading,
			'use_title' => $use_title,
			'use_title_in_post' => $use_title_in_post,
			'use_description' => $use_description,
			'use_breadcrumbs' => $use_breadcrumbs,
			'use_author' => $use_author,
			'add_date' => $add_date,
			'use_find' => $use_find,
			'allow_comments' => $allow_comments,
			'show_avatar' => $show_avatar,
			'always_owner' => $alwaysOwner,
			'post_heading' => $post_heading,
			'show_related' => $show_related,
			'related_max' => $related_max,
			'use_excerpt' => $use_excerpt
		)
	);

	// display heading preview
	$smarty->assign('show_blog_heading_preview', 'y');
	$cookietab = 2;
} else {
	$smarty->assign('show_blog_heading_preview', 'n');
}


$cat_type = 'blog';
$cat_objid = $blogId;
include_once ("categorize_list.php");

$defaultRows = 5;

ask_ticket('edit-blog');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-edit_blog.tpl');
$smarty->display("tiki.tpl");
