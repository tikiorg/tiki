<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_categories.php,v 1.21 2004-02-21 18:29:33 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_categories.php,v 1.21 2004-02-21 18:29:33 mose Exp $
//

// Initialization
require_once('tiki-setup.php');

include_once('lib/categories/categlib.php');
include_once('lib/filegals/filegallib.php');
include_once('lib/polls/polllib.php');
include_once('lib/tree/categ_admin_tree.php');
include_once('lib/directory/dirlib.php');
include_once('lib/trackers/trackerlib.php');


if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if ($feature_categories != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_categories");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_categories != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST["parentId"])) {
	$_REQUEST["parentId"] = 0;
}

$smarty->assign('parentId', $_REQUEST["parentId"]);

if (isset($_REQUEST["addpage"])) {
	check_ticket('admin-categories');
	// Here we categorize a page
	// $categlib->categorize_page($_REQUEST["pageName"],$_REQUEST["parentId"]);
	// add multiple pages at once
	foreach ($_REQUEST['pageName'] as $value) {
		$categlib->categorize_page($value, $_REQUEST["parentId"]);
	}
}

if (isset($_REQUEST["addpoll"])) {
	check_ticket('admin-categories');
	// Here we categorize a poll
	$categlib->categorize_poll($_REQUEST["pollId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addfaq"])) {
	check_ticket('admin-categories');
	// Here we categorize a faq
	$categlib->categorize_faq($_REQUEST["faqId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addtracker"])) {
	check_ticket('admin-categories');
	// Here we categorize a tracker
	$categlib->categorize_tracker($_REQUEST["trackerId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addquiz"])) {
	check_ticket('admin-categories');
	// Here we categorize a quiz
	$categlib->categorize_quiz($_REQUEST["quizId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addforum"])) {
	check_ticket('admin-categories');
	// Here we categorize a forum
	$categlib->categorize_forum($_REQUEST["forumId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addgallery"])) {
	check_ticket('admin-categories');
	// Here we categorize an image gallery
	$categlib->categorize_gallery($_REQUEST["galleryId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addfilegallery"])) {
	check_ticket('admin-categories');
	// Here we categorize a file gallery
	$categlib->categorize_file_gallery($_REQUEST["file_galleryId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addarticle"])) {
	check_ticket('admin-categories');
	// Here we categorize an article
	$categlib->categorize_article($_REQUEST["articleId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["addblog"])) {
	check_ticket('admin-categories');
	// Here we categorize a blog
	$categlib->categorize_blog($_REQUEST["blogId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["adddirectory"])) {
	check_ticket('admin-categories');
	// Here we categorize a directory category
	$categlib->categorize_directory($_REQUEST["directoryId"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["categId"])) {
	$info = $categlib->get_category($_REQUEST["categId"]);
} else {
	$_REQUEST["categId"] = 0;

	$info["name"] = '';
	$info["description"] = '';
}

if (isset($_REQUEST["removeObject"])) {
	check_ticket('admin-categories');
	$categlib->remove_object_from_category($_REQUEST["removeObject"], $_REQUEST["parentId"]);
}

if (isset($_REQUEST["removeCat"])) {
	check_ticket('admin-categories');
	$categlib->remove_category($_REQUEST["removeCat"]);
}

if (isset($_REQUEST["save"]) && isset($_REQUEST["name"]) && strlen($_REQUEST["name"]) > 0) {
	check_ticket('admin-categories');
	// Save
	if ($_REQUEST["categId"]) {
		$categlib->update_category($_REQUEST["categId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["parentId"]);
	} else {
		$categlib->add_category($_REQUEST["parentId"], $_REQUEST["name"], $_REQUEST["description"]);
	}

	$info["name"] = '';
	$info["description"] = '';
	$_REQUEST["categId"] = 0;
}

$smarty->assign('categId', $_REQUEST["categId"]);
$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);

// If the parent category is not zero get the category path
if ($_REQUEST["parentId"]) {
	$path = $categlib->get_category_path($_REQUEST["parentId"]);
	$p_info = $categlib->get_category($_REQUEST["parentId"]);
	$father = $p_info["parentId"];
} else {
	$path = "";
	$father = 0;
}

$smarty->assign('path', $path);
$smarty->assign('father', $father);
/*
// ---------------------------------------------------
// Convert $childrens
//$debugger->var_dump('$children');
$ctall = $categlib->get_all_categories_ext();
$tree_nodes = array();

foreach ($ctall as $c) {
	$tree_nodes[] = array(
		"id" => $c["categId"],
		"parent" => $c["parentId"],
		"data" => '<a class="catname" href="tiki-admin_categories.php?parentId=' . $c["categId"] . '" title="' . tra(
			'Child categories'). ':' . $c["children"] . ' ' . tra(
			'Objects in category'). ':' . $c["objects"] . '">' . $c["name"] . '</a>',
		"edit" =>
			'<a class="link" href="tiki-admin_categories.php?parentId=' . $c["parentId"] . '&amp;categId=' . $c["categId"] . '#editcreate" title="' . tra(
			'edit'). '"><img border="0" src="img/icons/edit.gif" /></a>',
		"remove" =>
			'<a class="link" href="tiki-admin_categories.php?parentId=' . $c["parentId"] . '&amp;removeCat=' . $c["categId"] . '" title="' . tra(
			'remove'). '"><img  border="0" src="img/icons2/delete.gif" /></a>',
		"children" => $c["children"],
		"objects" => $c["objects"]
	);
}

//$debugger->var_dump('$tree_nodes');
$tm = new CatAdminTreeMaker("admcat");
$res = $tm->make_tree($_REQUEST["parentId"], $tree_nodes);
$smarty->assign('tree', $res);
*/
// ---------------------------------------------------
function array_csort($marray, $column) {
	if (is_array($marray)) {
		$sortarr = array();
  	foreach ($marray as $key=>$row) { 
			$sortarr[$key] = $row[$column]; 
		}
 		array_multisort($sortarr, $marray); return $marray;
	} else {
		return array();
	}
}

$catree = $categlib->list_all_categories(0,-1,'name_asc','','',0);
$catree = array_csort($catree['data'],'categpath');
$smarty->assign('catree', $catree);

// var_dump($catree); 

// ---------------------------------------------------

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

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

if (isset($_REQUEST["find_objects"])) {
	$find_objects = $_REQUEST["find_objects"];
} else {
	$find_objects = '';
}

$smarty->assign('find_objects', $find_objects);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$smarty->assign_by_ref('find', $find);
$objects = $categlib->list_category_objects($_REQUEST["parentId"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('objects', $objects["data"]);

$cant_pages = ceil($objects["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($objects["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}
/*
$categories = $categlib->get_all_categories();
$smarty->assign_by_ref('categories', $categories);
*/
$galleries = $tikilib->list_galleries(0, -1, 'name_desc', 'admin', $find_objects);
$smarty->assign_by_ref('galleries', $galleries["data"]);

$file_galleries = $filegallib->list_file_galleries(0, -1, 'name_desc', 'admin', $find_objects);
$smarty->assign_by_ref('file_galleries', $file_galleries["data"]);

$forums = $tikilib->list_forums(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('forums', $forums["data"]);

$polls = $polllib->list_polls(0, -1, 'title_asc', $find_objects);
$smarty->assign_by_ref('polls', $polls["data"]);

$blogs = $tikilib->list_blogs(0, -1, 'title_asc', $find_objects);
$smarty->assign_by_ref('blogs', $blogs["data"]);

$pages = $tikilib->list_pages(0, -1, 'pageName_asc', $find_objects);
$smarty->assign_by_ref('pages', $pages["data"]);

$faqs = $tikilib->list_faqs(0, -1, 'title_asc', $find_objects);
$smarty->assign_by_ref('faqs', $faqs["data"]);

$quizzes = $tikilib->list_quizzes(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('quizzes', $quizzes["data"]);

$trackers = $trklib->list_trackers(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('trackers', $trackers["data"]);


$articles = $tikilib->list_articles(0, -1, 'title_asc', $find_objects, '', $user);
$smarty->assign_by_ref('articles', $articles["data"]);

$directories = $dirlib->dir_list_all_categories(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('directories', $directories["data"]);

ask_ticket('admin-categories');

// Display the template
$smarty->assign('mid', 'tiki-admin_categories.tpl');
$smarty->display("tiki.tpl");

?>
