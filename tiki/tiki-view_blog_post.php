<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_blog_post.php,v 1.17 2003-10-08 03:53:09 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/blogs/bloglib.php');

if (!isset($_REQUEST['blogId']) && !isset($_REQUEST['postId'])) {
	$parts = parse_url($_SERVER['REQUEST_URI']);

	$paths = explode('/', $parts['path']);
	$blogId = $paths[count($paths) - 2];
	$postId = $paths[count($paths) - 1];
	// So this is to process a trackback ping
	if (isset($_REQUEST['__mode'])) {
		// Build RSS listing trackback_from
		$pings = $bloglist->get_trackbacks_from($postId);
	}

	if (isset($_REQUEST['url'])) {
		// Add a trackback ping to the list of trackback_from
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';

		$excerpt = isset($_REQUEST['excerpt']) ? $_REQUEST['excerpt'] : '';
		$blog_name = isset($_REQUEST['blog_name']) ? $_REQUEST['blog_name'] : '';

		if ($bloglib->add_trackback_from($postId, $_REQUEST['url'], $title, $excerpt, $blog_name)) {
			print ('<?xml version="1.0" encoding="iso-8859-1"?>');

			print ('<response>');
			print ('<error>0</error>');
			print ('</response>');
		} else {
			print ('<?xml version="1.0" encoding="iso-8859-1"?>');

			print ('<response>');
			print ('<error>1</error>');
			print ('<message>Error trying to add ping for post</message>');
			print ('</response>');
		}

		die;
	}
}

if ($feature_blogs != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_blogs");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["postId"])) {
	$smarty->assign('msg', tra("No post indicated"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = httpPrefix(). $parts['path'] . '?blogId=' . $_REQUEST['blogId'] . '&postId=' . $_REQUEST['postId'];
$uri2 = httpPrefix(). $parts['path'] . '/' . $_REQUEST['blogId'] . '/' . $_REQUEST['postId'];
$smarty->assign('uri', $uri);
$smarty->assign('uri2', $uri2);

$postId = $_REQUEST["postId"];
$post_info = $bloglib->get_post($_REQUEST["postId"]);
$smarty->assign('post_info', $post_info);
$smarty->assign('postId', $_REQUEST["postId"]);
$_REQUEST["blogId"] = $post_info["blogId"];
$blog_data = $bloglib->get_blog($_REQUEST['blogId']);
$smarty->assign('blog_data', $blog_data);
$smarty->assign('blogId', $_REQUEST["blogId"]);

if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'created_desc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

$smarty->assign('offset', $_REQUEST["offset"]);
$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];

$parsed_data = $tikilib->parse_data($post_info["data"]);

if (!isset($_REQUEST['page']))
	$_REQUEST['page'] = 1;

$pages = $bloglib->get_number_of_pages($parsed_data);
$parsed_data = $bloglib->get_page($parsed_data, $_REQUEST['page']);
$smarty->assign('pages', $pages);

if ($pages > $_REQUEST['page']) {
	$smarty->assign('next_page', $_REQUEST['page'] + 1);
} else {
	$smarty->assign('next_page', $_REQUEST['page']);
}

if ($_REQUEST['page'] > 1) {
	$smarty->assign('prev_page', $_REQUEST['page'] - 1);
} else {
	$smarty->assign('prev_page', 1);
}

$smarty->assign('first_page', 1);
$smarty->assign('last_page', $pages);
$smarty->assign('page', $_REQUEST['page']);

$smarty->assign('parsed_data', $parsed_data);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["blogId"], 'blog')) {
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

if ($tiki_p_blog_admin == 'y') {
	$tiki_p_create_blogs = 'y';

	$smarty->assign('tiki_p_create_blogs', 'y');
	$tiki_p_blog_post = 'y';
	$smarty->assign('tiki_p_blog_post', 'y');
	$tiki_p_read_blog = 'y';
	$smarty->assign('tiki_p_read_blog', 'y');
}

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$blog_data = $tikilib->get_blog($_REQUEST["blogId"]);
$ownsblog = 'n';

if ($user && $user == $blog_data["user"]) {
	$ownsblog = 'y';
}

$smarty->assign('ownsblog', $ownsblog);

if (!$blog_data) {
	$smarty->assign('msg', tra("Blog not found"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($feature_blogposts_comments == 'y') {
	$comments_per_page = $blog_comments_per_page;

	$comments_default_ordering = $blog_comments_default_ordering;
	$comments_vars = array(
		'postId',
		'offset',
		'find',
		'sort_mode',
		'blogId'
	);

	$comments_prefix_var = 'post:';
	$comments_object_var = 'postId';
	include_once ("comments.php");
}

$section = 'blogs';
include_once ('tiki-section_options.php');

if ($feature_theme_control == 'y') {
	$cat_type = 'blog';

	$cat_objid = $_REQUEST['blogId'];
	include ('tiki-tc.php');
}

if ($user && $tiki_p_notepad == 'y' && $feature_notepad == 'y' && isset($_REQUEST['savenotepad'])) {
	$tikilib->replace_note($user,
		0, $post_info['title'] ? $post_info['title'] : date("d/m/Y [h:i]", $post_info['created']), $post_info['data']);
}

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_view_blog_post ($post_info);
}

// Display the template
$smarty->assign('mid', 'tiki-view_blog_post.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
