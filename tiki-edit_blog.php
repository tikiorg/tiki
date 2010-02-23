<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/blogs/bloglib.php');

$smarty->assign('headtitle',tra('Create Blog'));
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
$smarty->assign('public', 'n');
$smarty->assign('use_find', 'y');
$smarty->assign('use_title', 'y');
$smarty->assign('add_date', 'y');
$smarty->assign('use_author', 'y');
$smarty->assign('allow_comments', 'y');
$smarty->assign('show_avatar', 'n');
$smarty->assign('maxPosts', 10);
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
	$data = $tikilib->get_blog($_REQUEST["blogId"]);

	if ($data["user"] != $user || !$user) {
		if ($tiki_p_blog_admin != 'y') {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied you cannot edit this blog"));

			$smarty->display("error.tpl");
			die;
		}
	}

	$smarty->assign('title', $data["title"]);
	$smarty->assign('description', $data["description"]);
	$smarty->assign('public', $data["public"]);
	$smarty->assign('use_title', $data["use_title"]);
	$smarty->assign('add_date', $data["add_date"]);
	$smarty->assign('use_author', $data["use_author"]);
	$smarty->assign('allow_comments', $data["allow_comments"]);
	$smarty->assign('show_avatar',$data["show_avatar"]);
	$smarty->assign('use_find', $data["use_find"]);
	$smarty->assign('maxPosts', $data["maxPosts"]);
	$smarty->assign('creator', $data["user"]);
	$smarty->assign('alwaysOwner', $data["always_owner"]);

}

if (isset($_REQUEST["heading"]) and $tiki_p_edit_templates == 'y') {
	// Sanatization cleanup
	$heading = preg_replace('/st<x>yle="[^"]*"/', 'style_dangerous', $_REQUEST["heading"]);
} elseif (!isset($data["heading"])) {
	$n = $smarty->get_filename('blog-heading.tpl', 'r');
	@$fp = fopen($n, 'r');
	if ($fp) {
		$heading = fread($fp, filesize($n));
		@fclose($fp);
	} else
		$heading = '';
} else {
	$heading = $data["heading"];
}

$smarty->assign_by_ref('heading', $heading);
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);

$category_needed = false;
if (isset($_REQUEST["save"]) && $prefs['feature_categories'] == 'y' && $prefs['feature_blog_mandatory_category'] >=0 && (empty($_REQUEST['cat_categories']) || count($_REQUEST['cat_categories']) <= 0)) {
		$category_needed = true;
		$smarty->assign('category_needed', 'y');
} elseif (isset($_REQUEST["save"])) {
	check_ticket('edit-blog');
	if (isset($_REQUEST["public"]) && $_REQUEST["public"] == 'on') {
		$public = 'y';
	} else {
		$public = 'n';
	}

	$use_title = isset($_REQUEST['use_title']) ? 'y' : 'n';
	$allow_comments = isset($_REQUEST["allow_comments"]) ? 'y' : 'n';
	$show_avatar = isset($_REQUEST['show_avatar']) ? 'y' : 'n';	
	$use_find = isset($_REQUEST['use_find']) ? 'y' : 'n';
	$use_author = isset($_REQUEST['use_author']) ? 'y' : 'n';
	$add_date = isset($_REQUEST['add_date']) ? 'y' : 'n';
	$alwaysOwner = isset($_REQUEST['alwaysOwner']) ? 'y' : 'n';

	$bid = $bloglib->replace_blog($_REQUEST["title"],
	    $_REQUEST["description"], $_REQUEST["creator"], $public,
	    $_REQUEST["maxPosts"], $_REQUEST["blogId"],
	    $heading, $use_title, $use_author, $add_date, $use_find,
	    $allow_comments, $show_avatar, $alwaysOwner);

	$cat_type = 'blog';
	$cat_objid = $bid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-view_blog.php?blogId=" . $cat_objid;
	include_once ("categorize.php");

	header ("location: tiki-list_blogs.php?blogId=$bid");
	die;
}

if (isset($_REQUEST['preview']) || $category_needed) {
	$smarty->assign('title', $_REQUEST["title"]);

	$smarty->assign('description', $_REQUEST["description"]);
	$smarty->assign('public', isset($_REQUEST["public"]) ? 'y' : 'n');
	$smarty->assign('use_find', isset($_REQUEST["use_find"]) ? 'y' : 'n');
	$smarty->assign('use_title', isset($_REQUEST["use_title"]) ? 'y' : 'n');
	$smarty->assign('use_author', isset($_REQUEST["use_author"]) ? 'y' : 'n');
	$smarty->assign('add_date', isset($_REQUEST["add_date"]) ? 'y' : 'n');
	$smarty->assign('allow_comments', isset($_REQUEST["allow_comments"]) ? 'y' : 'n');
	$smarty->assign('maxPosts', $_REQUEST["maxPosts"]);
	$smarty->assign('heading', $heading);
	$smarty->assign('creator', $_REQUEST["creator"]);
}


$cat_type = 'blog';
$cat_objid = $blogId;
include_once ("categorize_list.php");

$defaultRows = 5;
include_once("textareasize.php");

ask_ticket('edit-blog');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-edit_blog.tpl');
$smarty->display("tiki.tpl");
