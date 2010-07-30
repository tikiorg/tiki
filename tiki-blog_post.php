<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/blogs/bloglib.php');
include_once ('lib/wiki/editlib.php');
$smarty->assign('headtitle', tra('Edit Post'));
$access->check_feature('feature_blogs');
if ($prefs['feature_freetags'] == 'y') {
	include_once ('lib/freetag/freetaglib.php');
	
	if ($prefs['feature_multilingual'] == 'y') {
		$languages = array();
		$languages = $tikilib->list_languages();
		$smarty->assign_by_ref('languages', $languages);
		$smarty->assign('blog', 'y');
	}
}
if (isset($_REQUEST['blogId'])) {
	$blogId = $_REQUEST['blogId'];
	$blog_data = $bloglib->get_blog($blogId);
} else {
	$blogId = 0;
}
if ($tiki_p_blog_admin == 'y') {
	$blogsd = $bloglib->list_blogs(0, -1, 'created_desc', '');
	$blogs = $blogsd['data'];
} else {
	$blogs = $bloglib->list_blogs_user_can_post();
}
$smarty->assign_by_ref('blogs', $blogs);
if (count($blogs) == 0) {
	$smarty->assign('msg', tra("It isn't possible to post in any blog. You may need to create a blog first."));
	$smarty->display("error.tpl");
	die;
} elseif ($blogId == 0 && count($blogs) == 1) {
	$blogId = $blogs[0]['blogId'];
}
$smarty->assign('blogId', $blogId);
// Now check permissions to access this page
if (!($tiki_p_blog_admin == 'y' || (!empty($blogId) && $tiki_p_blog_post == 'y') || (!empty($blogId) && $blog_data['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $blogId, 'blog', 'tiki_p_blog_post')))) {
	$msg = "tiki_p_blog_admin: $tiki_p_blog_admin -- blogId: $blogId -- tiki_p_blog_post: $tiki_p_blog_post -- blog_data(public): " . $blog_data['public'] . " -- tikilib: " . $tikilib->user_has_perm_on_object($user, $blogId, 'blog', 'tiki_p_blog_post') . " -- user: $user ";
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot post"));
	$smarty->display("error.tpl");
	die;
}
if ($prefs['feature_wysiwyg'] == 'y' && ($prefs['wysiwyg_default'] == 'y' && !isset($_REQUEST['wysiwyg'])) || (isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y')) {
	$smarty->assign('wysiwyg', 'y');
} else {
	$smarty->assign('wysiwyg', 'n');
}
$postId = isset($_REQUEST["postId"]) ? $_REQUEST["postId"] : 0;
$smarty->assign('postId', $postId);
$smarty->assign('data', '');
$smarty->assign('created', $tikilib->now);
// Exit edit mode (without javascript)
if (isset($_REQUEST['cancel'])) header("location: tiki-view_blog.php?blogId=$blogId");
// Exit edit mode (with javascript)
$smarty->assign('referer', !empty($_REQUEST['referer']) ? $_REQUEST['referer'] : (empty($_SERVER['HTTP_REFERER']) ? 'tiki-view_blog.php?blogId=' . $blogId : $_SERVER['HTTP_REFERER']));
$blog_data = $bloglib->get_blog($blogId);
$smarty->assign_by_ref('blog_data', $blog_data);
if (isset($_REQUEST['remove_image'])) {
	$access->check_authenticity();
	$bloglib->remove_post_image($_REQUEST['remove_image']);
}
// If the articleId is passed then get the article data
if (isset($_REQUEST["postId"]) && $_REQUEST["postId"] > 0) {
	// Check permission
	$data = $bloglib->get_post($_REQUEST["postId"]);
	// If the blog is public and the user has posting permissions then he can edit
	// If the user owns the weblog then he can edit
	if (!$user || ($data["user"] != $user && $user != $blog_data["user"] && !($blog_data['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_post')))) {
		if ($tiki_p_blog_admin != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_admin')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied you cannot edit this post"));
			$smarty->display("error.tpl");
			die;
		}
	}
	if (empty($data["data"])) $data["data"] = '';
	$smarty->assign('data', $data["data"]);
	$smarty->assign('title', $data["title"]);
	$smarty->assign('created', $data["created"]);
	$smarty->assign('parsed_data', $tikilib->parse_data($data["data"]));
	$smarty->assign('blogpriv', $data["priv"]);
}
if ($postId) {
	check_ticket('blog');
	$post_images = $bloglib->get_post_images($postId);
	$smarty->assign_by_ref('post_images', $post_images);
	$cat_type = 'blog post';
	$cat_objid = $postId;

	if (isset($_REQUEST['lang'])) {
		$cat_lang = $_REQUEST['lang'];
	}

	include_once ('freetag_list.php');
}
$smarty->assign('preview', 'n');
if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
	}
}
$blogpriv = 'n';
$smarty->assign('blogpriv', 'n');
if (isset($_REQUEST["data"])) {
	if (($prefs['feature_wiki_allowhtml'] == 'y' and $tiki_p_use_HTML == 'y' and isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on")) {
		$edit_data = $_REQUEST["data"];
	} else {
		$edit_data = $_REQUEST["data"];
	}
} else {
	if (isset($data["data"])) {
		$edit_data = $data["data"];
	} else {
		$edit_data = '';
	}
	if (isset($data["priv"])) {
		$smarty->assign('blogpriv', $data["priv"]);
		$blogpriv = $data["priv"];
	}
}
// Handles switching editor modes
if (isset($_REQUEST['mode_normal']) && $_REQUEST['mode_normal']=='y') {
	// Parsing page data as first time seeing html page in normal editor
	$smarty->assign('msg', "Parsing html to wiki");
	$parsed = $editlib->parseToWiki($edit_data);
	$smarty->assign('data', $parsed);
	
} elseif (isset($_REQUEST['mode_wysiwyg']) && $_REQUEST['mode_wysiwyg']=='y') {
	// Parsing page data as first time seeing wiki page in wysiwyg editor
	$smarty->assign('msg', "Parsing wiki to html");
	$parsed = $editlib->parseToWysiwyg($edit_data);
	$smarty->assign('data', $parsed);
}

if (isset($_REQUEST["blogpriv"]) && $_REQUEST["blogpriv"] == 'on') {
	$smarty->assign('blogpriv', 'y');
	$blogpriv = 'y';
}
if (isset($_REQUEST["preview"])) {
	$parsed_data = $tikilib->apply_postedit_handlers($edit_data);
	$parsed_data = $tikilib->parse_data($parsed_data);
	$smarty->assign('data', $edit_data);
	$smarty->assign('parsed_data', $parsed_data);

	$post_info = array();
	$post_info['title'] = $_REQUEST['title'];
	$post_info['user'] = isset($data) ? $data['user'] : $user;
	$post_info['created'] = isset($data) ? $data['created'] : time();
	$post_info['avatar'] = isset($data) ? $data['avatar'] : '';
	$smarty->assign('post_info', $post_info);

	if ($prefs['feature_freetags'] == 'y') {
		$smarty->assign('taglist', $_REQUEST["freetag_string"]);
	}

	$smarty->assign('preview', 'y');
}
// remove images (permissions!)
if ((isset($_REQUEST['save']) || isset($_REQUEST['save_exit'])) && $prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory_blog'] == 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
	$contribution_needed = true;
	$smarty->assign('contribution_needed', 'y');
} else {
	$contribution_needed = false;
}
if ((isset($_REQUEST["save"]) || isset($_REQUEST['save_exit'])) && !$contribution_needed) {
	include_once ("lib/imagegals/imagegallib.php");
	$smarty->assign('individual', 'n');
	$tikilib->get_perm_object($_REQUEST["blogId"], 'blog');

	if ($tiki_p_blog_post != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied you cannot post"));
		$smarty->display("error.tpl");
		die;
	}

	if ($_REQUEST["postId"] > 0) {
		$data = $bloglib->get_post($_REQUEST["postId"]);
		$blog_data = $bloglib->get_blog($data["blogId"]);
		if (!$user || ($data["user"] != $user && $user != $blog_data["user"] && !($blog_data['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_post')))) {
			if ($tiki_p_blog_admin != 'y') {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra("Permission denied you cannot edit this post"));
				$smarty->display("error.tpl");
				die;
			}
		}
	}
	$edit_data = $imagegallib->capture_images($edit_data);
	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
	if (isset($_REQUEST["publish_Hour"])) {

	$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
	} else {
		$publishDate = $tikilib->now;
	}
	
	
	if ($_REQUEST["postId"] > 0) {
		$bloglib->update_post($_REQUEST["postId"], $_REQUEST["blogId"], $edit_data, $data["user"], $title, isset($_REQUEST['contributions']) ? $_REQUEST['contributions'] : '', $data['data'], $blogpriv, $publishDate);
		$postid = $_REQUEST["postId"];
	} else {
		if($blog_data['always_owner'] == 'y'){
			$author = $blog_data['user'];
		}
		else {
			$author = $user;
		}
		$postid = $bloglib->blog_post($_REQUEST["blogId"], $edit_data, $author, $title, isset($_REQUEST['contributions']) ? $_REQUEST['contributions'] : '', $blogpriv, $publishDate);
		$smarty->assign('postId', $postid);
	}
	// TAG Stuff
	$cat_type = 'blog post';
	$cat_objid = $postid;
	$cat_desc = substr($edit_data, 0, 200);
	$cat_name = $title;
	$cat_href = "tiki-view_blog_post.php?postId=" . urlencode($postid);
	$cat_lang = $_REQUEST['lang'];
	include_once ("freetag_apply.php");
	if (isset($_REQUEST['save_exit'])) {
		
		header ("location: tiki-view_blog_post.php?postId=$postid");

		die;
	}
	$parsed_data = $tikilib->apply_postedit_handlers($edit_data);
	$parsed_data = $tikilib->parse_data($parsed_data);
	$smarty->assign('data', $edit_data);
	if ($prefs['feature_freetags'] == 'y') {
		$smarty->assign('taglist', $_REQUEST["freetag_string"]);
	}
	$smarty->assign('title', isset($_REQUEST["title"]) ? $_REQUEST['title'] : '');
	$smarty->assign('parsed_data', $parsed_data);
}
if ($contribution_needed) {
	$smarty->assign('title', $_REQUEST["title"]);
	$smarty->assign('parsed_data', $tikilib->parse_data($_REQUEST['data']));
	$smarty->assign('data', $_REQUEST['data']);
	if ($prefs['feature_freetags'] == 'y') {
		$smarty->assign('taglist', $_REQUEST["freetag_string"]);
	}
}
$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'blogId',
	'postId'
);
include_once ('tiki-section_options.php');
include_once ("textareasize.php");
global $wikilib;
include_once ('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true, 'blogedit');
$smarty->assign_by_ref('plugins', $plugins);
if ($prefs['feature_contribution'] == 'y') {
	include_once ('contribution.php');
}
ask_ticket('blog');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the Index Template
$smarty->assign('mid', 'tiki-blog_post.tpl');
$smarty->display("tiki.tpl");
