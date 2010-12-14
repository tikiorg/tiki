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

$access->check_feature('feature_blogs');

$blogId = isset($_REQUEST['blogId']) ? $_REQUEST['blogId'] : 0;

// Now check which blogs the user has permission to post (if any)
if ($tiki_p_blog_admin == 'y') {
	$blogsd = $bloglib->list_blogs(0, -1, 'created_desc', '');
	$blogs = $blogsd['data'];
} else {
	$blogs = $bloglib->list_blogs_user_can_post();
}

$smarty->assign_by_ref('blogs', $blogs);

// If user doesn't have permission to post in any blog display error message
if (count($blogs) == 0) {
	$smarty->assign('msg', tra("It isn't possible to post in any blog. You may need to create a blog first."));
	$smarty->display("error.tpl");
	die;
} elseif ($blogId == 0 && count($blogs) == 1) {
	$blogId = $blogs[0]['blogId'];
}

if ($blogId > 0) {
	$blog_data = $bloglib->get_blog($blogId);
	$smarty->assign_by_ref('blog_data', $blog_data);
}

$postId = isset($_REQUEST["postId"]) ? $_REQUEST["postId"] : 0;

if ($postId > 0) {
	$data = $bloglib->get_post($_REQUEST["postId"]);

	// If the blog is public and the user has posting permissions then he can edit
	// If the user owns the weblog then he can edit
	if (!$user || ($data["user"] != $user && $user != $blog_data["user"] && !($blog_data['public'] == 'y' && $tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_post')))) {
		if ($tiki_p_blog_admin != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_admin')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to edit this post"));
			$smarty->display("error.tpl");
			die;
		}
	}
	if(isset($data['wysiwyg']) && !isset($_REQUEST['wysiwyg'])) {
		$_REQUEST['wysiwyg'] = $data['wysiwyg'];
	}
}

$smarty->assign('headtitle', tra('Edit Post'));
$smarty->assign('blogId', $blogId);
$smarty->assign('postId', $postId);

if (isset($_REQUEST["publish_Hour"])) {
	$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
} else {
	$publishDate = $tikilib->now;
}

if ($prefs['feature_freetags'] == 'y') {
	include_once ('lib/freetag/freetaglib.php');
	
	if ($prefs['feature_multilingual'] == 'y') {
		$languages = array();
		$languages = $tikilib->list_languages();
		$smarty->assign_by_ref('languages', $languages);
		$smarty->assign('blog', 'y');
	}
}

// Exit edit mode (without javascript)
if (isset($_REQUEST['cancel'])) {
	header("location: tiki-view_blog.php?blogId=$blogId");
}

// Exit edit mode (with javascript)
$smarty->assign('referer', !empty($_REQUEST['referer']) ? $_REQUEST['referer'] : (empty($_SERVER['HTTP_REFERER']) ? 'tiki-view_blog.php?blogId=' . $blogId : $_SERVER['HTTP_REFERER']));

if (isset($_REQUEST['remove_image'])) {
	$access->check_authenticity();
	$bloglib->remove_post_image($_REQUEST['remove_image']);
}

if ($prefs['feature_wysiwyg'] == 'y' && ($prefs['wysiwyg_default'] == 'y' && !isset($_REQUEST['wysiwyg'])) || (isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y')) {
	$smarty->assign('wysiwyg', 'y');
	$is_wysiwyg = TRUE;
} else {
	$smarty->assign('wysiwyg', 'n');
	$is_wysiwyg = FALSE;
}

if ($postId > 0) {
	if (empty($data["data"])) $data["data"] = '';

	$smarty->assign('post_info', $data);
	$smarty->assign('data', $data['data']);
	$smarty->assign('parsed_data', $tikilib->parse_data($data['data']), array('is_html' => $is_wysiwyg));
	$smarty->assign('blogpriv', $data['priv']);

	check_ticket('blog');
	$post_images = $bloglib->get_post_images($postId);
	$smarty->assign_by_ref('post_images', $post_images);
	$cat_type = 'blog post';
	$cat_objid = $postId;

	if (isset($_REQUEST['lang'])) {
		$cat_lang = $_REQUEST['lang'];
	}

}
include_once ('freetag_list.php');

$smarty->assign('preview', 'n');
if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
	}
}

$blogpriv = 'n';
$smarty->assign('blogpriv', 'n');

if (isset($_REQUEST["data"])) {
	$edit_data = $_REQUEST["data"];
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
	$post_info = array();
	$parsed_data = $tikilib->apply_postedit_handlers($edit_data);
	$parsed_data = $tikilib->parse_data($parsed_data, array('is_html' => $is_wysiwyg));
	$smarty->assign('data', $edit_data);
	$post_info['parsed_data'] = $parsed_data;

	$post_info['title'] = $_REQUEST['title'];
	$post_info['excerpt'] = $_REQUEST['excerpt'];
	$post_info['user'] = isset($data) ? $data['user'] : $user;
	$post_info['created'] = $publishDate;
	$post_info['avatar'] = isset($data) ? $data['avatar'] : '';

	if ($prefs['feature_freetags'] == 'y' && isset($_REQUEST['freetag_string'])) {
		$tags = $freetaglib->dumb_parse_tags($_REQUEST['freetag_string']);
		$smarty->assign('tags', $tags);
		$post_info['freetags'] = $tags;
		$smarty->assign('taglist', $_REQUEST["freetag_string"]);
	}
	$smarty->assign('post_info', $post_info);

	$smarty->assign('preview', 'y');
}

if (isset($_REQUEST['save']) && $prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory_blog'] == 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
	$contribution_needed = true;
	$smarty->assign('contribution_needed', 'y');
} else {
	$contribution_needed = false;
}

if (isset($_REQUEST['save']) && !$contribution_needed) {
	include_once ("lib/imagegals/imagegallib.php");
	$smarty->assign('individual', 'n');

	$edit_data = $imagegallib->capture_images($edit_data);
	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
	
	if ($postId > 0) {
		$bloglib->update_post($postId, $_REQUEST["blogId"], $edit_data, $_REQUEST['excerpt'], $data["user"], $title, isset($_REQUEST['contributions']) ? $_REQUEST['contributions'] : '', $blogpriv, $publishDate, $is_wysiwyg);
	} else {
		if ($blog_data['always_owner'] == 'y') {
			$author = $blog_data['user'];
		} else {
			$author = $user;
		}
		$postId = $bloglib->blog_post($_REQUEST["blogId"], $edit_data, $_REQUEST['excerpt'], $author, $title, isset($_REQUEST['contributions']) ? $_REQUEST['contributions'] : '', $blogpriv, $publishDate, $is_wysiwyg);
		$smarty->assign('postId', $postId);
	}

	// TAG Stuff
	$cat_type = 'blog post';
	$cat_objid = $postId;
	$cat_desc = substr($edit_data, 0, 200);
	$cat_name = $title;
	$cat_href = "tiki-view_blog_post.php?postId=" . urlencode($postId);
	$cat_lang = $_REQUEST['lang'];
	include_once ("freetag_apply.php");

	require_once('tiki-sefurl.php');	
	$url = filter_out_sefurl("tiki-view_blog_post.php?postId=$postId", $smarty, 'blogpost');
	header ("location: $url");
	die;
}

if ($contribution_needed) {
	$smarty->assign('title', $_REQUEST["title"]);
	$smarty->assign('parsed_data', $tikilib->parse_data($_REQUEST['data'], array('is_html' => $is_wysiwyg)));
	$smarty->assign('data', $_REQUEST['data']);
	if ($prefs['feature_freetags'] == 'y') {
		$smarty->assign('taglist', $_REQUEST["freetag_string"]);
	}
}

include_once ('tiki-section_options.php');

if ($prefs['feature_contribution'] == 'y') {
	include_once ('contribution.php');
}

ask_ticket('blog');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-blog_post.tpl');
$smarty->display("tiki.tpl");
