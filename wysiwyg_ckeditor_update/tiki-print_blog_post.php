<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/blogs/bloglib.php');
$access->check_feature('feature_blogs');

if (!isset($_REQUEST["postId"])) {
	$smarty->assign('msg', tra("No post indicated"));
	$smarty->display("error.tpl");
	die;
}
$postId = $_REQUEST["postId"];
$post_info = $bloglib->get_post($_REQUEST["postId"]);
$smarty->assign('post_info', $post_info);
$smarty->assign('postId', $_REQUEST["postId"]);
$_REQUEST["blogId"] = $post_info["blogId"];
$blog_data = $bloglib->get_blog($_REQUEST['blogId']);
$smarty->assign('blog_data', $blog_data);
$smarty->assign('blogId', $_REQUEST["blogId"]);
//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = $tikilib->httpPrefix() . $parts['path'] . '?blogId=' . $_REQUEST['blogId'] . '&postId=' . $_REQUEST['postId'];
$uri2 = $tikilib->httpPrefix() . $parts['path'] . '/' . $_REQUEST['blogId'] . '/' . $_REQUEST['postId'];
$smarty->assign('uri', $uri);
$smarty->assign('uri2', $uri2);
if (!isset($_REQUEST['offset'])) $_REQUEST['offset'] = 0;
if (!isset($_REQUEST['sort_mode'])) $_REQUEST['sort_mode'] = 'created_desc';
if (!isset($_REQUEST['find'])) $_REQUEST['find'] = '';
$smarty->assign('offset', $_REQUEST["offset"]);
$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];
$parsed_data = $tikilib->parse_data($post_info["data"]);
$smarty->assign('parsed_data', $parsed_data);
$smarty->assign('individual', 'n');

$tikilib->get_perm_object($_REQUEST["blogId"], 'blog');
$access->check_permission('tiki_p_read_blog');

$blog_data = $bloglib->get_blog($_REQUEST["blogId"]);
$ownsblog = 'n';
if ($user && $user == $blog_data["user"]) {
	$ownsblog = 'y';
}
$smarty->assign('ownsblog', $ownsblog);
if (!$blog_data) {
	$smarty->assign('msg', tra("Blog not found"));
	$smarty->display("error.tpl");
	die;
}
if ($prefs['feature_blogposts_comments'] == 'y') {
	$comments_per_page = $prefs['blog_comments_per_page'];
	$thread_sort_mode = $prefs['blog_comments_default_ordering'];
	$comments_vars = array('postId', 'offset', 'find', 'sort_mode');
	$comments_prefix_var = 'post:';
	$comments_object_var = 'postId';
	include_once ("comments.php");
}
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'blog';
	$cat_objid = $_REQUEST['blogId'];
	include ('tiki-tc.php');
}
ask_ticket('print-blog-post');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->display("tiki-print_blog_post.tpl");
