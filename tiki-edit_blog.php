<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-edit_blog.php,v 1.39.2.1 2007-11-08 21:38:33 ricks99 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'blogs';
require_once ('tiki-setup.php');

include_once ('lib/blogs/bloglib.php');

$smarty->assign('headtitle',tra('Create Blog'));

if ($prefs['feature_blogs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_blogs");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_create_blogs != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot create or edit blogs"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["blogId"])) {
	$blogId = $_REQUEST["blogId"];
} else {
	$blogId = 0;
}

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($blogId, 'blog')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'blogs');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["blogId"], 'blog', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

$smarty->assign('blogId', $blogId);
$smarty->assign('title', '');
$smarty->assign('description', '');
$smarty->assign('public', 'n');
$smarty->assign('use_find', 'y');
$smarty->assign('use_title', 'y');
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

if (isset($_REQUEST["heading"])and $tiki_p_edit_templates) {
	$heading = $_REQUEST["heading"];
} else {
	$n = $smarty->get_filename('blog-heading.tpl', 'r');
	@$fp = fopen($n, 'r');
	if ($fp) {
		$heading = fread($fp, filesize($n));
		@fclose($fp);
	} else
		$heading = '';
}

$smarty->assign_by_ref('heading', $heading);
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);

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
	$smarty->assign('allow_comments', $data["allow_comments"]);
	$smarty->assign('show_avatar',$data["show_avatar"]);
	$smarty->assign('use_find', $data["use_find"]);
	$smarty->assign('maxPosts', $data["maxPosts"]);
	$smarty->assign('heading', $data["heading"]);
	$smarty->assign('creator', $data["user"]);

}

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

	// 'heading' was assumed set. -rlpowell
	$heading = isset($_REQUEST['heading']) ? $_REQUEST['heading'] : '';

	$bid = $bloglib->replace_blog($_REQUEST["title"],
	    $_REQUEST["description"], $_REQUEST["creator"], $public,
	    $_REQUEST["maxPosts"], $_REQUEST["blogId"],
	    $heading, $use_title, $use_find,
	    $allow_comments, $show_avatar);

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
	$smarty->assign('allow_comments', isset($_REQUEST["allow_comments"]) ? 'y' : 'n');
	$smarty->assign('maxPosts', $_REQUEST["maxPosts"]);
	$smarty->assign('heading', $heading);
	$smarty->assign('creator', $_REQUEST["creator"]);
}


$sections = 'blogs';
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

?>
