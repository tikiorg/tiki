<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_categories.php,v 1.51.2.1 2008-01-31 18:27:21 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/categories/categlib.php');
include_once('lib/filegals/filegallib.php');
include_once('lib/polls/polllib.php');
include_once('lib/tree/categ_admin_tree.php');
include_once('lib/directory/dirlib.php');
include_once('lib/trackers/trackerlib.php');
include_once('lib/commentslib.php');


if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if (!isset($commentslib)) {
	$commentslib = new Comments($dbTiki);
}

if ($prefs['feature_categories'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_categories");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_categories != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST["parentId"])) {
	$_REQUEST["parentId"] = 0;
}

$smarty->assign('parentId', $_REQUEST["parentId"]);

if (isset($_REQUEST["addpage"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a page
	// $categlib->categorize_page($_REQUEST["pageName"],$_REQUEST["parentId"]);
	// add multiple pages at once
	foreach ($_REQUEST['pageName'] as $value) {
		$categlib->categorize_page($value, $_REQUEST["parentId"]);
		$category=$categlib->get_category($_REQUEST["parentId"]);		
		$categorizedObject=$categlib->get_categorized_object('wiki page',$value);		
		// Notify the users watching this category.		
		$values= array("categoryId"=>$_REQUEST["parentId"], "categoryName"=>$category['name'], 
			"categoryPath"=>$categlib->get_category_path_string_with_root($_REQUEST["parentId"]),
			"description"=>$category['description'], "parentId" => $category['parentId'], 
			"parentName" => $categlib->get_category_name($category['parentId']),
			"action"=>"object entered category", "objectName"=>$categorizedObject['name'],
			"objectType"=>$categorizedObject['type'], "objectUrl"=>$categorizedObject['href']);		
		$categlib->notify($values);						
	}
}

if (isset($_REQUEST["addpoll"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a poll
	$categlib->categorize_poll($_REQUEST["pollId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('poll',$_REQUEST["pollId"]);	
	
}

if (isset($_REQUEST["addfaq"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a faq
	$categlib->categorize_faq($_REQUEST["faqId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('faq',$_REQUEST["faqId"]);
}

if (isset($_REQUEST["addtracker"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a tracker
	$categlib->categorize_tracker($_REQUEST["trackerId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('tracker',$_REQUEST["trackerId"]);
}

if (isset($_REQUEST["addquiz"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a quiz
	$categlib->categorize_quiz($_REQUEST["quizId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('quiz',$_REQUEST["quizId"]);
}

if (isset($_REQUEST["addforum"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a forum
	$categlib->categorize_forum($_REQUEST["forumId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('forum',$_REQUEST["forumId"]);
}

if (isset($_REQUEST["addgallery"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize an image gallery
	$categlib->categorize_gallery($_REQUEST["galleryId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('image gallery',$_REQUEST["galleryId"]);
}

if (isset($_REQUEST["addfilegallery"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a file gallery
	$categlib->categorize_file_gallery($_REQUEST["file_galleryId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('file gallery',$_REQUEST["file_galleryId"]);
}

if (isset($_REQUEST["addarticle"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize an article
	$categlib->categorize_article($_REQUEST["articleId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('article',$_REQUEST["articleId"]);
}

if (isset($_REQUEST["addblog"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a blog
	$categlib->categorize_blog($_REQUEST["blogId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('blog',$_REQUEST["blogId"]);
}

if (isset($_REQUEST["adddirectory"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a directory category
	$categlib->categorize_directory($_REQUEST["directoryId"], $_REQUEST["parentId"]);
	$categorizedObject=$categlib->get_categorized_object('directory',$_REQUEST["directoryId"]);
}

if ( isset($categorizedObject) && !isset($_REQUEST["addpage"]) ) {
	$category=$categlib->get_category($_REQUEST["parentId"]);		
	// Notify the users watching this category.		
	$values= array("categoryId"=>$_REQUEST["parentId"], "categoryName"=>$category['name'], 
		"categoryPath"=>$categlib->get_category_path_string_with_root($_REQUEST["parentId"]),
		"description"=>$category['description'], "parentId" => $category['parentId'], 
		"parentName" => $categlib->get_category_name($category['parentId']),
		"action"=>"object entered category", "objectName"=>$categorizedObject['name'],
		"objectType"=>$categorizedObject['type'], "objectUrl"=>$categorizedObject['href']);		
	$categlib->notify($values);					
}

if (isset($_REQUEST["categId"])) {
	$info = $categlib->get_category($_REQUEST["categId"]);
} else {
	$_REQUEST["categId"] = 0;

	$info["name"] = '';
	$info["description"] = '';
}

if (isset($_REQUEST["removeObject"])) {
	$area = 'delcategobject';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$category=$categlib->get_category($_REQUEST["parentId"]);		
		$categorizedObject=$categlib->get_categorized_object_via_category_object_id($_REQUEST["removeObject"]);		
		$categlib->remove_object_from_category($_REQUEST["removeObject"], $_REQUEST["parentId"]);
		// Notify the users watching this category.		
		$values= array("categoryId"=>$_REQUEST["parentId"], "categoryName"=>$category['name'], 
			"categoryPath"=>$categlib->get_category_path_string_with_root($_REQUEST["parentId"]),
			"description"=>$category['description'], "parentId" => $category['parentId'], 
			"parentName" => $categlib->get_category_name($category['parentId']),
			"action"=>"object leaved category", "objectName"=>$categorizedObject['name'],
			"objectType"=>$categorizedObject['type'], "objectUrl"=>$categorizedObject['href']);		
		$categlib->notify($values);				
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["removeCat"]) && ($info = $categlib->get_category($_REQUEST['removeCat']))) {
	$area = "delcateg";
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$categlib->remove_category($_REQUEST["removeCat"]);
	} else {
		$confirmation = tra('Click here to delete the category:').' '.$info['name'];
		key_get($area, $confirmation);
	}
}

if (isset($_REQUEST["save"]) && isset($_REQUEST["name"]) && strlen($_REQUEST["name"]) > 0) {
	check_ticket('admin-categories');
	// Save
	if ($categlib->exist_child_category($_REQUEST['parentId'], $_REQUEST['name'])) {
	  $errors[]= tra('You can not create a category with a name already existing at this level');
	} else if ($_REQUEST["categId"]) {
	        if ($_REQUEST['parentId'] == $_REQUEST['categId']) {
	            $smarty->assign('msg', tra("Category can`t be parent of itself"));
  	            $smarty->display("error.tpl");
	            die;
                }
		$categlib->update_category($_REQUEST["categId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["parentId"]);
	} else {
		$newcategId = $categlib->add_category($_REQUEST["parentId"], $_REQUEST["name"], $_REQUEST["description"]);
		if (isset($_REQUEST['assign_perms'])) {
			if ($_REQUEST['parentId'] == 0) {
				$userlib->inherit_global_permissions($newcategId, 'category');
			} else {
				$newcategpath = $categlib->get_category_path($newcategId);
				$numcats = count($newcategpath);
				$inherit_from_parent = FALSE;
				for ($i=$numcats-2; $i>=0; $i--) {
					if ($userlib->object_has_one_permission($newcategpath[$i]['categId'], 'category')) {
						$userlib->copy_object_permissions($newcategpath[$i]['categId'], $newcategId, 'category');
						$inherit_from_parent = TRUE;
						break 1;
					}
				}
				if (!$inherit_from_parent) {
					$userlib->inherit_global_permissions($newcategId, 'category');
				}
			}
		}
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
	$categ_name = $p_info['name'];
} else {
	$path = "";
	$father = 0;
	$categ_name = tra('Top');
}

$smarty->assign('path', $path);
$smarty->assign('father', $father);
$smarty->assign('categ_name', $categ_name);
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
//$catree = array_csort($catree['data'],'categpath'); not needed as array is already sorted when returned from categlib
foreach ($catree['data'] as $key=>$c) {
	foreach ($path as $p) {
		if ($p['categId'] == $c['categId']) {
			$catree['data'][$key]['incat'] = 'y';
			break;
		}
	}
}
$smarty->assign('catree', $catree['data']);

// var_dump($catree); 

// ---------------------------------------------------

$smarty->assign('assign_perms', 'checked');

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
$objects = $categlib->list_category_objects($_REQUEST["parentId"], $offset, $maxRecords, $sort_mode, '', $find, false);
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

$forums = $commentslib->list_forums(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('forums', $forums["data"]);

$polls = $polllib->list_polls(0, -1, 'title_asc', $find_objects);
$smarty->assign_by_ref('polls', $polls["data"]);

$blogs = $tikilib->list_blogs(0, -1, 'title_asc', $find_objects);
$smarty->assign_by_ref('blogs', $blogs["data"]);

$pages = $tikilib->list_pageNames(0, -1, 'pageName_asc', $find_objects);
$smarty->assign_by_ref('pages', $pages["data"]);

$faqs = $tikilib->list_faqs(0, -1, 'title_asc', $find_objects);
$smarty->assign_by_ref('faqs', $faqs["data"]);

$quizzes = $tikilib->list_quizzes(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('quizzes', $quizzes["data"]);

$trackers = $trklib->list_trackers(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('trackers', $trackers["data"]);


$articles = $tikilib->list_articles(0, -1, 'title_asc', $find_objects, '', $user, '', '', 'n');
$smarty->assign_by_ref('articles', $articles["data"]);

$directories = $dirlib->dir_list_all_categories(0, -1, 'name_asc', $find_objects);
$smarty->assign_by_ref('directories', $directories["data"]);

ask_ticket('admin-categories');
if (!empty($errors))
	$smarty->assign_by_ref('errors', $errors);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_categories.tpl');
$smarty->display("tiki.tpl");

?>
