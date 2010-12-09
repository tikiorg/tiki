<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/blogs/bloglib.php');

$auto_query_args = array(
	'postId',
	'blogId',
	'offset',
	'find',
	'sort_mode',
	'page',
	'mode',
	'show_comments'
);

$access->check_feature('feature_blogs');

if (empty($_REQUEST["postId"])) {
	$smarty->assign('msg', tra('No post indicated'));
	$smarty->display('error.tpl');
	die;
} else {
	$postId = $_REQUEST['postId'];
}

$post_info = $bloglib->get_post($postId);
if (!$post_info) {
	$smarty->assign('msg', tra("Post not found"));
	$smarty->display("error.tpl");
	die;
}

$blogId = $post_info['blogId'];

$blog_data = $bloglib->get_blog($blogId);
if (!$blog_data) {
	$smarty->assign('msg', tra("Blog not found"));
	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object($blogId, 'blog');

$access->check_permission('tiki_p_read_blog');

$ownsblog = 'n';
if ($user && $user == $blog_data["user"]) {
	$ownsblog = 'y';
}

if ($ownsblog == 'n' && $tiki_p_blog_admin != 'y' && $post_info["priv"] == 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to view this blog post while it is marked as private"));
	$smarty->display("error.tpl");
	die;
}
if ($ownsblog == 'n' && $tiki_p_blog_admin != 'y' && $post_info['created'] > $tikilib->now) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display("error.tpl");
	die;
}	
$post_info['adjacent'] = $bloglib->_get_adjacent_posts($blogId, $post_info['created'], $tiki_p_blog_admin == 'y'? null: $tikilib->now, $user);

if(isset($post_info['priv']) && ($post_info['priv'] == 'y')) {
	$post_info['title'] .= ' (' . tra("private") . ')';
}

if ($prefs['feature_freetags'] == 'y') {
	// Get Tags
	include_once ('lib/freetag/freetaglib.php');
	$post_info['freetags'] = $freetaglib->get_tags_on_object($postId, "blog post");

	if ($blog_data['show_related'] == 'y' && !empty($post_info['freetags'])) {
		$post_info['related_posts'] = $bloglib->get_related_posts($postId, $blog_data['related_max']);
	}
}

if ($prefs['feature_categories'] == 'y') {
	$cat_type = 'blog post';
	$cat_objid = $postId;
	require_once('categorize_list.php');	
}

$smarty->assign('ownsblog', $ownsblog);
$post_info['data'] = TikiLib::htmldecode($post_info['data']);
$smarty->assign('postId', $postId);
$smarty->assign('blog_data', $blog_data);
$smarty->assign('blogId', $blogId);
$smarty->assign('headtitle', $post_info['title'] . ' : ' . $blog_data['title']);

if (!isset($_REQUEST['offset'])) $_REQUEST['offset'] = 0;
if (!isset($_REQUEST['sort_mode'])) $_REQUEST['sort_mode'] = 'created_desc';
if (!isset($_REQUEST['find'])) $_REQUEST['find'] = '';
$smarty->assign('offset', $_REQUEST["offset"]);
$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];
$parsed_data = $tikilib->parse_data($post_info["data"], array('is_html' => ($post_info["wysiwyg"]=='y'?TRUE:FALSE)));
if (!isset($_REQUEST['page'])) $_REQUEST['page'] = 1;
$pages = $bloglib->get_number_of_pages($parsed_data);
$post_info['parsed_data'] = $bloglib->get_page($parsed_data, $_REQUEST['page']);
$post_info['pages'] = $pages;
if ($pages > $_REQUEST['page']) {
	$post_info['next_page'] = $_REQUEST['page'] + 1;
} else {
	$post_info['next_page'] = $_REQUEST['page'];
}
if ($_REQUEST['page'] > 1) {
	$post_info['prev_page'] = $_REQUEST['page'] - 1;
} else {
	$post_info['prev_page'] = 1;
}
$post_info['first_page'] = 1;
$post_info['last_page'] = $pages;
$post_info['pagenum'] = $_REQUEST['page'];
$smarty->assign('post_info', $post_info);

if ($prefs['feature_blogposts_comments'] == 'y') {
	$comments_per_page = $prefs['blog_comments_per_page'];
	$thread_sort_mode = $prefs['blog_comments_default_ordering'];
	$comments_vars = array(
		'postId',
		'offset',
		'find',
		'sort_mode',
		'blogId'
	);
	$comments_prefix_var = 'blog post:';
	$comments_object_var = 'postId';
	include_once ("comments.php");
}

$cat_type = 'blog';
$cat_objid = $blogId;
include_once ('tiki-section_options.php');
if ($user && $prefs['feature_notepad'] == 'y' && $tiki_p_notepad == 'y' && isset($_REQUEST['savenotepad'])) {
	check_ticket('view-blog-post');
	$tikilib->replace_note($user, 0, $post_info['title'] ? $post_info['title'] : $tikilib->date_format("%d/%m/%Y [%H:%M]", $post_info['created']) , $post_info['data']);
}
if ($prefs['feature_mobile'] == 'y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");
	HAWTIKI_view_blog_post($post_info);
}

ask_ticket('view-blog-post');
// Display the template
$smarty->assign('mid', 'tiki-view_blog_post.tpl');
$smarty->display("tiki.tpl");
