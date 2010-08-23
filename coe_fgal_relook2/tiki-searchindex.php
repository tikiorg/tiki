<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
  array( 'staticKeyFilters' => array(
    'date' => 'digits',
    'maxRecords' => 'digits',
    'highlight' => 'xss',
    'where' => 'word',
    'searchLang' => 'word',
    'words' =>'xss',
    'boolean' =>'word',
    )
  )
);

$section = 'search';
require_once ('tiki-setup.php');
require_once ('lib/search/searchlib-tiki.php');
$auto_query_args = array('highlight', 'where');
$searchlib = new SearchLib;
$smarty->assign('headtitle', tra('Search'));

$access->check_feature('feature_search');
$access->check_permission('tiki_p_search');

if (isset($_REQUEST["highlight"]) && !empty($_REQUEST["highlight"])) {
	$_REQUEST["words"] = $_REQUEST["highlight"];
}
if ($prefs['feature_search_stats'] == 'y') {
	$searchlib->register_search(isset($_REQUEST["words"]) ? $_REQUEST["words"] : '');
}
if (!isset($_REQUEST["where"])) {
	$where = 'pages';
} else {
	$where = $_REQUEST["where"];
}
$smarty->assign('where', $where);
$filter = array();
if ($where == 'wikis') {
	$access->check_feature('feature_wiki');
}

if ($where == 'directory') {
	$access->check_feature('feature_directory');
	$access->check_permission('tiki_p_view_directory');
}

if ($where == 'faqs') {
	$access->check_feature('feature_faqs');
	$access->check_permission('tiki_p_view_faqs');
}

if ($where == 'forums') {
	$access->check_feature('feature_forums');
	$access->check_permission('tiki_p_forum_read');
	if (!empty($_REQUEST['forumId'])) {
		$filter['forumId'] = $_REQUEST['forumId'];
		global $commentslib;
		include ('lib/comments/commentslib.php');
		if (!isset($commentslib)) $commentslib = new Comments($dbTiki);
		$forum_info = $commentslib->get_forum($_REQUEST['forumId']);
		$where = 'forum';
		$smarty->assign_by_ref('where_forum', $forum_info['name']);
		$smarty->assign_by_ref('forumId', $_REQUEST['forumId']);
		$cant = '';
	}
}
if ($where == 'files') {
	$access->check_feature('feature_file_galleries');
}

if ($where == 'articles') {
	$access->check_feature('feature_articles');
}

if (($where == 'galleries' || $where == 'images')) {
	$access->check_feature('feature_galleries');
}

if (($where == 'blogs' || $where == 'posts')) {
	$access->check_feature('feature_blogs');
}

if (($where == 'trackers')) {
	$access->check_feature('feature_trackers');
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
$fulltext = $prefs['feature_search_fulltext'] == 'y';
if ((!isset($_REQUEST["words"])) || (empty($_REQUEST["words"]))) {
	$results = array('cant' => 0);
	$smarty->assign('words', '');
} else {
	$words = strip_tags($_REQUEST["words"]);
	$results = $searchlib->find($where, $words, $offset, $maxRecords, $fulltext, $filter);
	$smarty->assign('words', $words);
}
$smarty->assign('cant', $results['cant']);
$where_list = array('pages' => 'Entire Site');
if ($prefs['feature_wiki'] == 'y') {
	$where_list['wikis'] = tra('Wiki Pages');
}
if ($prefs['feature_galleries'] == 'y') {
	$where_list['galleries'] = tra('Galleries');
	$where_list['images'] = tra('Images');
}
if ($prefs['feature_file_galleries'] == 'y') {
	$where_list['files'] = tra('Files');
}
if ($prefs['feature_forums'] == 'y') {
	$where_list['forums'] = tra('Forums');
}
if ($prefs['feature_faqs'] == 'y') {
	$where_list['faqs'] = tra('Faqs');
}
if ($prefs['feature_blogs'] == 'y') {
	$where_list['blogs'] = tra('Blogs');
	$where_list['posts'] = tra('Blog Posts');
}
if ($prefs['feature_directory'] == 'y') {
	$where_list['directory'] = tra('Directory');
}
if ($prefs['feature_articles'] == 'y') {
	$where_list['articles'] = tra('Articles');
}
if ($prefs['feature_trackers'] == 'y') {
	$where_list['trackers'] = tra('Trackers');
}
$smarty->assign_by_ref('where_list', $where_list);
$smarty->assign_by_ref('results', $results["data"]);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('searchNoResults', !isset($_REQUEST['words'])); // false is default
$smarty->assign('mid', 'tiki-searchindex.tpl');
$smarty->display("tiki.tpl");
