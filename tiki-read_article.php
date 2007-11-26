<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-read_article.php,v 1.61.2.1 2007-11-26 15:27:17 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'cms';
require_once ('tiki-setup.php');
include_once ('lib/stats/statslib.php');

include_once ('lib/articles/artlib.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["articleId"])) {
	$smarty->assign('msg', tra("No article indicated"));

	$smarty->display("error.tpl");
	die;
}

//This is basicaly a copy of part of the freetag code from tiki-setup.php and should be only there. The problem is that the section name for articles is "cms" and the object name for article in the table tiki_objects is "article". Maybe it is a good idea to use "cms" on tiki_objects instead "article" and then this block of code can be removed. Another solution?
if ($prefs['feature_freetags'] == 'y') {
  include_once ('lib/freetag/freetaglib.php');
	$here = $sections[$section];

	if (isset($here['itemkey']) and isset($_REQUEST[$here['itemkey']])) {
		$tags = $freetaglib->get_tags_on_object($_REQUEST[$here['itemkey']], "article ".$_REQUEST[$here['key']]);
	} elseif (isset($here['key']) and isset($_REQUEST[$here['key']])) {
		$tags = $freetaglib->get_tags_on_object($_REQUEST[$here['key']], "article");
	} else {
		$tags = array();
	}
	$smarty->assign('freetags',$tags);
	$headerlib->add_cssfile('css/freetags.css');
}

// no need to check articleId; if it doesn't exist script would have died above
// if (isset($_REQUEST["articleId"])) {


	
	$artlib->add_article_hit($_REQUEST["articleId"]);

	$smarty->assign('articleId', $_REQUEST["articleId"]);
	$article_data = $tikilib->get_article($_REQUEST["articleId"]);

	if ($article_data === false) {
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
	}

	if (!$article_data) {
		$smarty->assign('msg', tra("Article not found"));
		$smarty->display("error.tpl");
		die;
	}


	if (($article_data["publishDate"] > $tikilib->now) && ($tiki_p_admin != 'y' && $tiki_p_admin_cms !='y') && ($article_data["type"] != 'Event')) {
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
	$smarty->assign('show_author', $article_data["show_author"]);
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
	$smarty->assign('reads', $article_data["nbreads"]);
	$smarty->assign('show_reads', $article_data["show_reads"]);
	$smarty->assign('size', $article_data["size"]);
	$smarty->assign('show_size', $article_data["show_size"]);

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
	$tikilib->parse_first( $article_data["body"], $preparsed, $noparsed );

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
	$tikilib->replace_preparse( $article_data["body"], $preparsed, $noparsed );

	$smarty->assign('body', $article_data["body"]);
	$smarty->assign('publishDate', $article_data["publishDate"]);
	$smarty->assign('show_pubdate', $article_data["show_pubdate"]);

	$smarty->assign('edit_data', 'y');

	$body = $article_data["body"];
	$heading = $article_data["heading"];

	$smarty->assign('parsed_body', $tikilib->parse_data($body));
	$smarty->assign('parsed_heading', $tikilib->parse_data($heading));
//}

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

if ($prefs['feature_article_comments'] == 'y') {
	$smarty->assign('comment_can_rate_article', $article_data["comment_can_rate_article"]); 
	$comments_per_page = $prefs['article_comments_per_page'];

	$thread_sort_mode = $prefs['article_comments_default_ordering'];
	$comments_vars = array('articleId');
	$comments_prefix_var = 'article:';
	$comments_object_var = 'articleId';
	include_once ("comments.php");
	if (isset($_REQUEST['show_comzone']) && $_REQUEST['show_comzone'] == 'y')
		$smarty->assign('show_comzone', 'y');
}

$objId = $_REQUEST['articleId'];
//$is_categorized = $categlib->is_categorized('article',$objId);
// $is_categorized should have been set above

// Display category path or not (like {catpath()})
if (isset($is_categorized) && $is_categorized) {
  $smarty->assign('is_categorized','y');
  if(isset($prefs['feature_categorypath']) and $prefs['feature_categories'] == 'y') {
    if ($prefs['feature_categorypath'] == 'y') {
      $cats = $categlib->get_object_categories('article',$objId);
      $display_catpath = $categlib->get_categorypath($cats);
      $smarty->assign('display_catpath',$display_catpath);
    }
  } 
  // Display current category objects or not (like {category()})
  if (isset($prefs['feature_categoryobjects']) and $prefs['feature_categories'] == 'y') {
    if ($prefs['feature_categoryobjects'] == 'y') {
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

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'article';

	$cat_objid = $_REQUEST["articleId"];
	include ('tiki-tc.php');
}

if ($prefs['feature_mobile'] =='y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_read_article($article_data, $pages);
}

if ($prefs['feature_multilingual'] == 'y' && $article_data['lang']) {
	include_once("lib/multilingual/multilinguallib.php");
	$trads = $multilinguallib->getTranslations('article', $article_data['articleId'], $article_data["title"], $article_data['lang']);
	$smarty->assign('trads', $trads);
}

ask_ticket('article-read');

//add a hit
$statslib->stats_hit($article_data["title"],"article",$article_data['articleId']);

// Display the Index Template
$smarty->assign('mid', 'tiki-read_article.tpl');
$smarty->display("tiki.tpl");

?>
