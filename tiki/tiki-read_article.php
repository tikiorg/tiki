<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-read_article.php,v 1.31 2004-06-10 09:46:48 sylvieg Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');
include_once('lib/categories/categlib.php');

if ($feature_articles != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_read_article != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["articleId"])) {
	$smarty->assign('msg', tra("No article indicated"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["articleId"])) {
	$artlib->add_article_hit($_REQUEST["articleId"]);

	$smarty->assign('articleId', $_REQUEST["articleId"]);
	$article_data = $tikilib->get_article($_REQUEST["articleId"]);

	if (!$article_data) {
		$smarty->assign('msg', tra("Article not found"));

		$smarty->display("error.tpl");
		die;
	}

	if ($userlib->object_has_one_permission($article_data["topicId"], 'topic')) {
		if (!$userlib->object_has_permission($user, $article_data["topicId"], 'topic', 'tiki_p_topic_read')) {
			$smarty->assign('msg', tra("Permision denied"));

			$smarty->display("error.tpl");
			die;
		}
	}

	if (($article_data["publishDate"] > date("U")) && ($tiki_p_admin != 'y') && ($article_data["type"] != 'Event')) {
		$smarty->assign('msg', tra("Article is not published yet"));

		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('arttitle', $article_data["title"]);
	$smarty->assign('topline', $article_data["topline"]);
	$smarty->assign('show_topline', $article_data["show_topline"]);
	$smarty->assign('subtitle', $article_data["subtitle"]);
	$smarty->assign('show_subtitle', $article_data["show_subtitle"]);
	$smarty->assign('linkto', $article_data["linkto"]);
	$smarty->assign('show_linkto', $article_data["show_linkto"]);
	$smarty->assign('image_caption', $article_data["image_caption"]);
	$smarty->assign('show_image_caption', $article_data["show_image_caption"]);
	$smarty->assign('lang', $article_data["lang"]);
	$smarty->assign('show_lang', $article_data["show_lang"]);
	$smarty->assign('authorName', $article_data["authorName"]);
	$smarty->assign('topicId', $article_data["topicId"]);
	$smarty->assign('type', $article_data["type"]);
	$smarty->assign('rating', $article_data["rating"]);
	$smarty->assign('entrating', $article_data["entrating"]);
	$smarty->assign('useImage', $article_data["useImage"]);
	$smarty->assign('isfloat', $article_data["isfloat"]);
	$smarty->assign('image_name', $article_data["image_name"]);
	$smarty->assign('image_type', $article_data["image_type"]);
	$smarty->assign('image_size', $article_data["image_size"]);
	$smarty->assign('image_x', $article_data["image_x"]);
	$smarty->assign('image_y', $article_data["image_y"]);
	$smarty->assign('image_data', urlencode($article_data["image_data"]));
	$smarty->assign('reads', $article_data["reads"]);
	$smarty->assign('size', $article_data["size"]);

	if (strlen($article_data["image_data"]) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}

	$smarty->assign('heading', $article_data["heading"]);

	if (!isset($_REQUEST['page']))
		$_REQUEST['page'] = 1;

	// Get ~pp~, ~np~ and <pre> out of the way. --rlpowell, 24 May 2004
	$preparsed = array();
	$noparsed = array();
	$tikilib->parse_pp_np( $article_data["body"], $preparsed, $noparsed );

	$pages = $artlib->get_number_of_pages($article_data["body"]);
	$article_data["body"] = $artlib->get_page($article_data["body"], $_REQUEST['page']);
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
	$smarty->assign('pagenum', $_REQUEST['page']);

	// Put ~pp~, ~np~ and <pre> back. --rlpowell, 24 May 2004
	$tikilib->parse_pp_np( $article_data["body"], $preparsed, $noparsed );

	$smarty->assign('body', $article_data["body"]);
	$smarty->assign('publishDate', $article_data["publishDate"]);
	$smarty->assign('edit_data', 'y');

	$body = $article_data["body"];
	$heading = $article_data["heading"];

	$smarty->assign('parsed_body', $tikilib->parse_data($body));
	$smarty->assign('parsed_heading', $tikilib->parse_data($heading));
}

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

if ($feature_article_comments == 'y') {
	$smarty->assign('comment_can_rate_article', $article_data["comment_can_rate_article"]); 
	$comments_per_page = $article_comments_per_page;

	$comments_default_ordering = $article_comments_default_ordering;
	$comments_vars = array('articleId');
	$comments_prefix_var = 'article:';
	$comments_object_var = 'articleId';
	include_once ("comments.php");
}

$objId = $_REQUEST["articleId"];
$is_categorized = $categlib->is_categorized('article',$objId);

// Display category path or not (like {catpath()})
if ($is_categorized) {
  $smarty->assign('is_categorized','y');
  if(isset($feature_categorypath) and $feature_categories == 'y') {
    if ($feature_categorypath == 'y') {
      $cats = $categlib->get_object_categories('article',$objId);
      $display_catpath = $categlib->get_categorypath($cats);
      $smarty->assign('display_catpath',$display_catpath);
    }
  } 
  // Display current category objects or not (like {category()})
  if (isset($feature_categoryobjects) and $feature_categories == 'y') {
    if ($feature_categoryobjects == 'y') {
      $catids = $categlib->get_object_categories('article', $objId);
      $display_catobjects = $categlib->get_categoryobjects($catids);
      $smarty->assign('display_catobjects',$display_catobjects);
    }
  } 
} else {
  $smarty->assign('is_categorized','n');
}

$section = 'cms';
include_once ('tiki-section_options.php');

if ($feature_theme_control == 'y') {
	$cat_type = 'article';

	$cat_objid = $_REQUEST["articleId"];
	include ('tiki-tc.php');
}

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_read_article($article_data, $pages);
}

if ($feature_multilingual == 'y' && $article_data['lang']) {
	include_once("lib/multilingual/multilinguallib.php");
	$trads = $multilinguallib->getTranslations('article', $article_data['articleId'], $article_data["title"], $article_data['lang']);
	$smarty->assign('trads', $trads);
}

ask_ticket('article-read');

// Display the Index Template
$smarty->assign('mid', 'tiki-read_article.tpl');
$smarty->assign('show_page_bar', 'n');
$smarty->display("tiki.tpl");

?>
