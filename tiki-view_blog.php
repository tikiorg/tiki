<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-view_blog.php,v 1.65.2.1 2007-12-07 05:56:38 mose Exp $
// 2009-02-23 SEWilco
// Added blogTitle parameter for access by title.
$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/blogs/bloglib.php');
if ($prefs['feature_freetags'] == 'y') {
	include_once ('lib/freetag/freetaglib.php');
}
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}
if ($prefs['feature_blogs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_blogs");
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST["blogTitle"])) {
	$blog_data = $tikilib->get_blog_by_title(trim(trim($_REQUEST["blogTitle"]) , "\x22\x27"));
	if ((!empty($blog_data)) && (!empty($blog_data["blogId"]))) {
		$_REQUEST["blogId"] = $blog_data["blogId"];
	}
}
if (!isset($_REQUEST["blogId"])) {
	$smarty->assign('msg', tra("No blog indicated"));
	$smarty->display("error.tpl");
	die;
}
$tikilib->get_perm_object( $_REQUEST["blogId"], 'blog' ); 

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you can not view this section"));
	$smarty->display("error.tpl");
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
	$smarty->display("error.tpl");
	die;
}
$bloglib->add_blog_hit($_REQUEST["blogId"]);
$smarty->assign('blogId', $_REQUEST["blogId"]);
$smarty->assign('title', $blog_data["title"]);
$smarty->assign('heading', $blog_data["heading"]);
$smarty->assign('use_title', $blog_data["use_title"]);
$smarty->assign('use_find', $blog_data["use_find"]);
$smarty->assign('allow_comments', $blog_data["allow_comments"]);
$smarty->assign('show_avatar', $blog_data["show_avatar"]);
$smarty->assign('description', $blog_data["description"]);
$smarty->assign('created', $blog_data["created"]);
$smarty->assign('lastModif', $blog_data["lastModif"]);
$smarty->assign('posts', $blog_data["posts"]);
$smarty->assign('public', $blog_data["public"]);
$smarty->assign('hits', $blog_data["hits"]);
$smarty->assign('creator', $blog_data["user"]);
$smarty->assign('activity', $blog_data["activity"]);
if (isset($_REQUEST["remove"])) {
	$data = $bloglib->get_post($_REQUEST["remove"]);
	if ($user && $blog_data['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_post')) {
		$data["user"] = $user;
	}
	if ($ownsblog == 'n') {
		if (!$user || $data["user"] != $user) {
			if ($tiki_p_blog_admin != 'y') {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra("Permission denied you cannot remove the post"));
				$smarty->display("error.tpl");
				die;
			}
		}
	}
	$area = 'delpost';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$bloglib->remove_post($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}
// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
// Get a list of last changes to the blog database
$date_min = isset($_REQUEST['date_min']) ? $_REQUEST['date_min'] : '';
$date_max = isset($_REQUEST['date_max']) ? $_REQUEST['date_max'] : $tikilib->now;
$listpages = $bloglib->list_blog_posts($_REQUEST["blogId"], $offset, $blog_data["maxPosts"], $sort_mode, $find, $date_min, $date_max);
$temp_max = count($listpages["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$listpages["data"][$i]["parsed_data"] = $tikilib->parse_data($bloglib->get_page($listpages["data"][$i]["data"], 1));
	if ($prefs['feature_freetags'] == 'y') { // And get the Tags for the posts
		$listpages["data"][$i]["freetags"] = $freetaglib->get_tags_on_object($listpages["data"][$i]["postId"], "blog post");
	}
}
$maxRecords = $blog_data["maxPosts"];
$smarty->assign('maxRecords', $maxRecords);
// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages', $listpages["data"]);
$smarty->assign_by_ref('cant', $listpages["cant"]);
if ($prefs['feature_blog_comments'] == 'y') {
	$comments_per_page = $prefs['blog_comments_per_page'];
	$thread_sort_mode = $prefs['blog_comments_default_ordering'];
	$comments_vars = array(
		'blogId'
	);
	$comments_prefix_var = 'blog:';
	$comments_object_var = 'blogId';
	include_once ("comments.php");
}
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'blog';
	$cat_objid = $_REQUEST['blogId'];
	include ('tiki-tc.php');
}
if ($user && $prefs['feature_notepad'] == 'y' && $tiki_p_notepad == 'y' && isset($_REQUEST['savenotepad'])) {
	check_ticket('blog');
	$post_info = $bloglib->get_post($_REQUEST['savenotepad']);
	$tikilib->replace_note($user, 0, $post_info['title'] ? $post_info['title'] : $tikilib->date_format("%d/%m/%Y [%H:%M]", $post_info['created']) , $post_info['data']);
}
if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_event'])) {
		check_ticket('blog');
		if ($_REQUEST['watch_action'] == 'add') {
			$tikilib->add_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'blog', $blog_data['title'], "tiki-view_blog.php?blogId=" . $_REQUEST['blogId']);
		} else {
			$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'blog');
		}
	}
	$smarty->assign('user_watching_blog', 'n');
	if ($user && $tikilib->user_watches($user, 'blog_post', $_REQUEST['blogId'], 'blog')) {
		$smarty->assign('user_watching_blog', 'y');
	}
	// Check, if the user is watching this blog by a category.
	if ($prefs['feature_categories'] == 'y') {
		$watching_categories_temp = $categlib->get_watching_categories($_REQUEST['blogId'], 'blog', $user);
		$smarty->assign('category_watched', 'n');
		if (count($watching_categories_temp) > 0) {
			$smarty->assign('category_watched', 'y');
			$watching_categories = array();
			foreach($watching_categories_temp as $wct) {
				$watching_categories[] = array(
					"categId" => $wct,
					"name" => $categlib->get_category_name($wct)
				);
			}
			$smarty->assign('watching_categories', $watching_categories);
		}
	}
}
if ($prefs['feature_mobile'] == 'y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");
	HAWTIKI_view_blog($listpages, $blog_data);
}
if ($prefs['feature_actionlog'] == 'y') {
	include_once ('lib/logs/logslib.php');
	$logslib->add_action('Viewed', $_REQUEST['blogId'], 'blog', '');
}
ask_ticket('blog');
// Display the template
$smarty->assign('mid', 'tiki-view_blog.tpl');
$smarty->display("tiki.tpl");
