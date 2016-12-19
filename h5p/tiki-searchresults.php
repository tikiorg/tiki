<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
				'date' => 'digits',
				'maxRecords' => 'digits',
				'highlight' => 'text',
				'where' => 'word',
				'find' => 'text',
				'searchLang' => 'word',
				'words' =>'text',
				'boolean' =>'word',
				'forumId' => 'digits',
				'name' => 'word',
				'galleryId' => 'digits',
				'categId' => 'digits',
				'offset' => 'digits',								
		)
	)
);

$section = 'search';
require_once ('tiki-setup.php');
require_once ('lib/search/searchlib-mysql.php');
$auto_query_args = array('highlight', 'where', 'initial', 'maxRecords', 'sort_mode', 'find', 'searchLang', 'words', 'boolean', 'categId' );
$searchlib = new SearchLib;
$access->check_feature('feature_search_fulltext');
$access->check_permission('tiki_p_search');

if (!empty($_REQUEST["highlight"])) {
	$_REQUEST["words"] = $_REQUEST["highlight"];
} else if (!empty($_REQUEST['find'])) {
	$_REQUEST['words'] = $_REQUEST['find'];
}
if ($prefs['feature_search_stats'] == 'y') {
	$searchlib->register_search(isset($_REQUEST["words"]) ? $_REQUEST["words"] : '');
}
if (empty($_REQUEST["where"])) {
	$where = 'pages';
} else {
	$where = $_REQUEST["where"];
}
$find_where = 'find_' . $where;
$smarty->assign('where', $where);
if ($where == 'wikis') {
	$where_label = 'wiki pages';
} else {
	$where_label = $where;
}
$smarty->assign('where_label', $where_label);
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
		$commentslib = TikiLib::lib('comments');
		$forum_info = $commentslib->get_forum($_REQUEST['forumId']);
		$where = 'forum';
		$smarty->assign_by_ref('where_forum', $forum_info['name']);
		$smarty->assign_by_ref('forumId', $_REQUEST['forumId']);
		$cant = '';
	}
}

if ($where == 'files') {
	$access->check_feature('feature_file_galleries');
	if (!empty($_REQUEST['galleryId'])) {
		$filter['galleryId'] = $_REQUEST['galleryId'];
	}
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

$categId = 0;
if ($prefs['feature_categories'] == 'y') {
	if (!empty($_REQUEST['cat_categories'])) {
		$categId = $_REQUEST['cat_categories'];
		if (count($_REQUEST['cat_categories']) > 1) {
			unset($_REQUEST['categId']);
		} else {
			$_REQUEST['categId'] = $_REQUEST['cat_categories'][0];
		}
	} else {
		$_REQUEST['cat_categories'] = array();
	}
	$selectedCategories = $_REQUEST['cat_categories'];
	$smarty->assign('findSelectedCategoriesNumber', count($_REQUEST['cat_categories']));
	if (!empty($_REQUEST['categId'])) {
		$categId = $_REQUEST['categId'];
		$selectedCategories = array((int) $categId);
		$smarty->assign('find_categId', $_REQUEST['categId']);
	}

	$categlib = TikiLib::lib('categ');
	$categories = $categlib->getCategories();
	$smarty->assign_by_ref('categories', $categories);
	$smarty->assign('cat_tree', $categlib->generate_cat_tree($categories, true, $selectedCategories));
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST['searchLang'])) {
	$searchLang = $_REQUEST['searchLang'];
} elseif ($prefs['search_default_interface_language'] == 'y' && $prefs['feature_multilingual'] == 'y') {
	$searchLang = $prefs['language'];
} else {
	$searchLang = '';
}
$smarty->assign_by_ref('searchLang', $searchLang);
$smarty->assign_by_ref('offset', $offset);
$fulltext = $prefs['feature_search_fulltext'] == 'y';
if (isset($_REQUEST['boolean']) && ($_REQUEST['boolean'] == 'on' || $_REQUEST['boolean'] == 'y')) {
	$boolean = 'y';
} else {
	$boolean = 'n';
}
$smarty->assign_by_ref('boolean', $boolean);
if (!isset($_REQUEST['date'])) $_REQUEST['date'] = 0;
$smarty->assign('date', $_REQUEST['date']);
if (!isset($_REQUEST["words"]) || empty($_REQUEST["words"])) {
	$results = array('cant' => 0);
	$smarty->assign('words', '');
} else {
	$words = strip_tags($_REQUEST["words"]);
	if (!method_exists($searchlib, $find_where)) {
		$find_where = "find_pages";
	}
	if ($where == 'wikis') {
		$results = $searchlib->$find_where($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $_REQUEST['date'], $searchLang, $categId);
	} elseif ($where == 'articles' || $find_where == 'find_pages') {
		$results = $searchlib->$find_where($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $_REQUEST['date'], $categId, $searchLang);
	} else {
		$results = $searchlib->$find_where($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $_REQUEST['date'], $categId);
	}
	$smarty->assign('words', $words);
}
$smarty->assign('cant', $results['cant']);
$where_list = array('pages' => 'Entire Site');
if ($prefs['feature_calendar'] == 'y') {
	$where_list['calendars'] = tra('Calendar Items');
}
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
if (($where == 'wikis' || $where == 'articles') && $prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$langLib = TikiLib::lib('language');
	$languages = $langLib->list_languages(false, 'y');
	$smarty->assign_by_ref('languages', $languages);
}

if (isset($results['data']) && is_array($results['data'])) {
	array_walk(
		$results['data'],
		function (& $entry) {
			if (strpos($entry['href'], '?') !== false) {
				$entry['href'] .= '&highlight=' . rawurlencode($_REQUEST['words']);
			} else {
				$entry['href'] .= '?highlight=' . rawurlencode($_REQUEST['words']);
			}
		}
	);
}

$smarty->assign_by_ref('where_list', $where_list);
$smarty->assign_by_ref('results', $results["data"]);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
ask_ticket('searchresults');
$smarty->assign('searchNoResults', !isset($_REQUEST['words'])); // false is default
$smarty->assign('mid', 'tiki-searchresults.tpl');
$smarty->display("tiki.tpl");
