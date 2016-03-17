<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
$bloglib = TikiLib::lib('blog');

$auto_query_args = array(
	'blogId'
);

if ($prefs['feature_freetags'] == 'y') {
	$freetaglib = TikiLib::lib('freetag');
}

if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$access->check_feature('feature_blogs');

if (isset($_REQUEST["blogTitle"])) {
	$blog_data = $bloglib->get_blog_by_title(trim(trim($_REQUEST["blogTitle"]), "\x22\x27"));
	if ((!empty($blog_data)) && (!empty($blog_data["blogId"]))) {
		$_REQUEST["blogId"] = $blog_data["blogId"];
	}
}
if (!isset($_REQUEST["blogId"])) {
	$smarty->assign('msg', tra("No blog indicated"));
	$smarty->display("error.tpl");
	die;
}
$tikilib->get_perm_object($_REQUEST["blogId"], 'blog');


$access->check_permission('tiki_p_read_blog', '', 'blog', $_REQUEST["blogId"]);

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
$bloglib->add_blog_hit($_REQUEST["blogId"]);
$smarty->assign('blogId', $_REQUEST["blogId"]);
$blog_data["blogId"] = $_REQUEST["blogId"];
$smarty->assign('title', $blog_data["title"]);
$smarty->assign('headtitle', $blog_data['title'] . ' : ' . $blog_data['description']);
$blog_data["headtitle"] = $blog_data['title'] . ' : ' . $blog_data['description'];
$smarty->assign('heading', $blog_data["heading"]);
$smarty->assign('use_author', $blog_data["use_author"]);
$smarty->assign('add_date', $blog_data["add_date"]);
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
$smarty->assign('use_excerpt', $blog_data["use_excerpt"]);
$smarty->assign('blog_data', $blog_data);
if (isset($_REQUEST["remove"])) {
	$data = $bloglib->get_post($_REQUEST["remove"]);
	if ($user && $blog_data['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_post')) {
		$data["user"] = $user;
	}
	if ($ownsblog == 'n') {
		if (!$user || $data["user"] != $user) {
			$access->check_permission('tiki_p_blog_admin');
		}
	}
	$access->check_authenticity();
	$bloglib->remove_post($_REQUEST["remove"]);
}
// This script can receive the threshold
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
$listpages = $bloglib->list_blog_posts($_REQUEST["blogId"], true, $offset, $blog_data["maxPosts"], $sort_mode, $find, $date_min, $date_max);
//Keep track of month of last viewed posts for months_links module foldable display
$_SESSION['blogs_last_viewed_month'] = TikiLib::date_format("%Y-%m", $date_max);

$maxRecords = $blog_data["maxPosts"];
$smarty->assign('maxRecords', $maxRecords);
// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages', $listpages["data"]);
$smarty->assign_by_ref('cant', $listpages["cant"]);
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'blog';
	$cat_objid = $_REQUEST['blogId'];
	include ('tiki-tc.php');
}
if ($user && $prefs['feature_notepad'] == 'y' && $tiki_p_notepad == 'y' && isset($_REQUEST['savenotepad'])) {
	check_ticket('blog');
	$post_info = $bloglib->get_post($_REQUEST['savenotepad']);
	$tikilib->replace_note($user, 0, $post_info['title'] ? $post_info['title'] : $tikilib->date_format("%d/%m/%Y [%H:%M]", $post_info['created']), $post_info['data']);
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
			foreach ($watching_categories_temp as $wct) {
				$watching_categories[] = array(
					"categId" => $wct,
					"name" => $categlib->get_category_name($wct)
				);
			}
			$smarty->assign('watching_categories', $watching_categories);
		}
	}
}

if ($prefs['feature_actionlog'] == 'y') {
	$logslib->add_action('Viewed', $_REQUEST['blogId'], 'blog', '');
}
ask_ticket('blog');
// Display the template
$smarty->assign('mid', 'tiki-view_blog.tpl');
$smarty->display("tiki.tpl");
