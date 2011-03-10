<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/tree/categ_admin_tree.php');

$access->check_feature('feature_categories');
$access->check_permission('tiki_p_admin_categories');

// Check for parent category or set to 0 if not present
if (!empty($_REQUEST['parentId']) && !($info = $categlib->get_category($_REQUEST['parentId']))) {
	$smarty->assign('msg', 'Incorrect param'.' parentId');
	$smarty->display('error.tpl');
	die;
}	

if (!isset($_REQUEST["parentId"])) {
	$_REQUEST["parentId"] = 0;
}
$smarty->assign('parentId', $_REQUEST["parentId"]);

if (!empty($_REQUEST['unassign'])) {
	$access->check_authenticity(tra('Are you sure you want to unassign the objects of this category: ') . htmlspecialchars($info['name']));
	$categlib->unassign_all_objects($_REQUEST['parentId']);
}
if (!empty($_REQUEST['move_to']) && !empty($_REQUEST['toId'])) {
	check_ticket('admin-categories');
	if (!$categlib->get_category($_REQUEST['toId'])) {
		$smarty->assign('msg', 'Incorrect param'.' toId');
		$smarty->display('error.tpl');
		die;
	}
	$categlib->move_all_objects($_REQUEST['parentId'], $_REQUEST['toId']);
}
if (!empty($_REQUEST['copy_from']) && !empty($_REQUEST['to'])) {
	check_ticket('admin-categories');
	if (!$categlib->get_category($_REQUEST['to'])) {
		$smarty->assign('msg', 'Incorrect param'.' fromId');
		$smarty->display('error.tpl');
		die;
	}
	$categlib->assign_all_objects($_REQUEST['parentId'], $_REQUEST['to']);
}
if (isset($_REQUEST["addpage"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a page
	// $categlib->categorize_page($_REQUEST["pageName"],$_REQUEST["parentId"]);
	// add multiple pages at once
	foreach($_REQUEST['pageName'] as $value) {
		$categlib->categorize_page($value, $_REQUEST["parentId"]);
		$category = $categlib->get_category($_REQUEST["parentId"]);
		$categorizedObject = $categlib->get_categorized_object('wiki page', $value);
		// Notify the users watching this category.
		$values = array(
			"categoryId" => $_REQUEST["parentId"],
			"categoryName" => $category['name'],
			"categoryPath" => $categlib->get_category_path_string_with_root($_REQUEST["parentId"]) ,
			"description" => $category['description'],
			"parentId" => $category['parentId'],
			"parentName" => $categlib->get_category_name($category['parentId']) ,
			"action" => "object entered category",
			"objectName" => $categorizedObject['name'],
			"objectType" => $categorizedObject['type'],
			"objectUrl" => $categorizedObject['href']
		);
		$categlib->notify($values);
	}
}
if (isset($_REQUEST["addpoll"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a poll
	$categlib->categorize_poll($_REQUEST["pollId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('poll', $_REQUEST["pollId"]);
}
if (isset($_REQUEST["addfaq"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a faq
	$categlib->categorize_faq($_REQUEST["faqId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('faq', $_REQUEST["faqId"]);
}
if (isset($_REQUEST["addtracker"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a tracker
	$categlib->categorize_tracker($_REQUEST["trackerId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('tracker', $_REQUEST["trackerId"]);
}
if (isset($_REQUEST["addquiz"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a quiz
	$categlib->categorize_quiz($_REQUEST["quizId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('quiz', $_REQUEST["quizId"]);
}
if (isset($_REQUEST["addforum"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a forum
	$categlib->categorize_forum($_REQUEST["forumId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('forum', $_REQUEST["forumId"]);
}
if (isset($_REQUEST["addgallery"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize an image gallery
	$categlib->categorize_gallery($_REQUEST["galleryId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('image gallery', $_REQUEST["galleryId"]);
}
if (isset($_REQUEST["addfilegallery"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a file gallery
	$categlib->categorize_file_gallery($_REQUEST["file_galleryId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('file gallery', $_REQUEST["file_galleryId"]);
}
if (isset($_REQUEST["addarticle"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize an article
	$categlib->categorize_article($_REQUEST["articleId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('article', $_REQUEST["articleId"]);
}
if (isset($_REQUEST["addblog"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a blog
	$categlib->categorize_blog($_REQUEST["blogId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('blog', $_REQUEST["blogId"]);
}
if (isset($_REQUEST["adddirectory"]) && $_REQUEST["parentId"] != 0) {
	check_ticket('admin-categories');
	// Here we categorize a directory category
	$categlib->categorize_directory($_REQUEST["directoryId"], $_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object('directory', $_REQUEST["directoryId"]);
}
if (isset($categorizedObject) && !isset($_REQUEST["addpage"])) {
	$category = $categlib->get_category($_REQUEST["parentId"]);
	// Notify the users watching this category.
	$values = array(
		"categoryId" => $_REQUEST["parentId"],
		"categoryName" => $category['name'],
		"categoryPath" => $categlib->get_category_path_string_with_root($_REQUEST["parentId"]) ,
		"description" => $category['description'],
		"parentId" => $category['parentId'],
		"parentName" => $categlib->get_category_name($category['parentId']) ,
		"action" => "object entered category",
		"objectName" => $categorizedObject['name'],
		"objectType" => $categorizedObject['type'],
		"objectUrl" => $categorizedObject['href']
	);
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
	$access->check_authenticity();
	$category = $categlib->get_category($_REQUEST["parentId"]);
	$categorizedObject = $categlib->get_categorized_object_via_category_object_id($_REQUEST["removeObject"]);
	$categlib->remove_object_from_category($_REQUEST["removeObject"], $_REQUEST["parentId"]);
	// Notify the users watching this category.
	$values = array(
		"categoryId" => $_REQUEST["parentId"],
		"categoryName" => $category['name'],
		"categoryPath" => $categlib->get_category_path_string_with_root($_REQUEST["parentId"]) ,
		"description" => $category['description'],
		"parentId" => $category['parentId'],
		"parentName" => $categlib->get_category_name($category['parentId']) ,
		"action" => "object leaved category",
		"objectName" => $categorizedObject['name'],
		"objectType" => $categorizedObject['type'],
		"objectUrl" => $categorizedObject['href']
	);
	$categlib->notify($values);
}
if (isset($_REQUEST["removeCat"]) && ($info = $categlib->get_category($_REQUEST['removeCat']))) {
	$access->check_authenticity(tra('Click here to delete the category:') . ' ' . htmlspecialchars($info['name']));
	$categlib->remove_category($_REQUEST["removeCat"]);
}
if (isset($_REQUEST["save"]) && isset($_REQUEST["name"]) && strlen($_REQUEST["name"]) > 0) {
	check_ticket('admin-categories');
	// Save
	if ($_REQUEST["categId"]) {
		if ($_REQUEST['parentId'] == $_REQUEST['categId']) {
			$smarty->assign('msg', tra("Category can`t be parent of itself"));
			$smarty->display("error.tpl");
			die;
		}
		$categlib->update_category($_REQUEST["categId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["parentId"]);
	} else if ($categlib->exist_child_category($_REQUEST['parentId'], $_REQUEST['name'])) {
		$errors[] = tra('You can not create a category with a name already existing at this level');
	} else {
		$newcategId = $categlib->add_category($_REQUEST["parentId"], $_REQUEST["name"], $_REQUEST["description"]);
	}
	$info["name"] = '';
	$info["description"] = '';
	$_REQUEST["categId"] = 0;
}
if (isset($_REQUEST['import']) && isset($_FILES['csvlist']['tmp_name'])) {
	check_ticket('admin-categories');
	$fhandle = fopen($_FILES['csvlist']['tmp_name'], 'r');
	if (!$fhandle) {
		$smarty->assign('msg', tra("The file is not a CSV file or has not a correct syntax"));
		$smarty->display("error.tpl");
		die;
	}
	$fields = fgetcsv($fhandle, 1000);
	if (!$fields[0]) {
		$smarty->assign('msg', tra('The file is not a CSV file or has not a correct syntax'));
		$smarty->display('error.tpl');
		die;
	}
	if ($fields[0] != 'category' || $fields[1] != 'description' || $fields[2] != 'parent') {
		$smarty->assign('msg', tra('The file does not have the required header:') . ' category, description, parent');
		$smarty->display('error.tpl');
		die;
	}
	while (!feof($fhandle)) {
		$data = fgetcsv($fhandle, 1000);
		if (!empty($data)) {
			$temp_max = count($fields);
			if ($temp_max > 1 && strtolower($data[2]) != 'top' && !empty($data[2])) {
				$parentId = $categlib->get_category_id($data[2]);
				if (empty($parentId)) {
					$smarty->assign('msg', tra('Incorrect param') . ' ' . $data[2]);
					$smarty->display('error.tpl');
					die;
				}
			} else {
				$parentId = 0;
			}
			if (!$categlib->exist_child_category($parentId, $data[0])) {
				$newcategId = $categlib->add_category($parentId, $data[0], $data[1]);
				if (empty($newcategId)) {
					$smarty->assign('msg', tra('Incorrect param') . ' ' . $data[0]);
					$smarty->display('error.tpl');
					die;
				}
			}
		}
	}
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
// ---------------------------------------------------
function array_csort($marray, $column) {
	if (is_array($marray)) {
		$sortarr = array();
		foreach($marray as $key => $row) {
			$sortarr[$key] = $row[$column];
		}
		array_multisort($sortarr, $marray);
		return $marray;
	} else {
		return array();
	}
}
$catree = $categlib->list_all_categories(0, -1, 'name_asc', '', '', 0);
//$catree = array_csort($catree['data'],'categpath'); not needed as array is already sorted when returned from categlib
if (is_array($path)) {
	foreach($catree['data'] as $key => $c) {
		foreach($path as $p) {
			if ($p['categId'] == $c['categId']) {
				$catree['data'][$key]['incat'] = 'y';
				break;
			}
		}
	}
}
$smarty->assign('catree', $catree['data']);
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
$smarty->assign('offset', $offset);
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

function admin_categ_assign( &$max, $data_key, $data = null ) {
	global $smarty;

	if( is_null( $data ) ) {
		$data = array( 'data' => array(), 'cant' => 0 );
	}

	$smarty->assign( $data_key, $data['data'] );
	$smarty->assign( 'cant_' . $data_key, $data['cant'] );

	$max = max( $max, $data['cant'] );
}

$articles = $galleries = $file_galleries = $forums = $polls = $blogs = $pages = $faqs = $quizzes = $trackers = $directories = $objects = null;

$maxRecords = $prefs['maxRecords'];

$smarty->assign('find_objects', $find_objects);
$smarty->assign('sort_mode', $sort_mode);
$smarty->assign('find', $find);

$objects = $categlib->list_category_objects($_REQUEST["parentId"], $offset, $maxRecords, $sort_mode, '', $find, false);

if( $prefs['feature_galleries'] == 'y' ) {
	$galleries = $tikilib->list_galleries($offset, $maxRecords, 'name_desc', 'admin', $find_objects);
}

if( $prefs['feature_file_galleries'] == 'y' ) {
	include_once ('lib/filegals/filegallib.php');
	$file_galleries = $filegallib->list_file_galleries($offset, $maxRecords, 'name_desc', 'admin', $find_objects, $prefs['fgal_root_id']);
}

if( $prefs['feature_forums'] == 'y' ) {
	include_once ('lib/comments/commentslib.php');
	if (!isset($commentslib)) {
		$commentslib = new Comments($dbTiki);
	}
	$forums = $commentslib->list_forums($offset, $maxRecords, 'name_asc', $find_objects);
}

if( $prefs['feature_polls'] == 'y' ) {
	include_once ('lib/polls/polllib.php');
	$polls = $polllib->list_polls($offset, $maxRecords, 'title_asc', $find_objects);
}

if( $prefs['feature_blogs'] == 'y' ) {
	require_once('lib/blogs/bloglib.php');
	$blogs = $bloglib->list_blogs($offset, $maxRecords, 'title_asc', $find_objects);
}

if( $prefs['feature_wiki'] == 'y' ) {
	$pages = $tikilib->list_pageNames($offset, $maxRecords, 'pageName_asc', $find_objects);
	//TODO for all other object types
	$pages_not_in_cat = array();
	foreach($pages['data'] as $pg) {
		$found = false;
		foreach ($objects['data'] as $obj) {
			if ($obj['type'] == 'wiki page' && $obj['itemId'] == $pg['pageName']) {
				$found = true;
				break;
			} 
		}
		if (!$found) {
			$pages_not_in_cat[] = $pg;
		}
	}
	$pages['cant'] = $pages['cant']- count($pages['data']) + count($pages_not_in_cat);
	$pages['data'] = $pages_not_in_cat;
}

if( $prefs['feature_faqs'] == 'y' ) {
	$faqs = $tikilib->list_faqs($offset, $maxRecords, 'title_asc', $find_objects);
}

if( $prefs['feature_quizzes'] == 'y' ) {
	$quizzes = $tikilib->list_quizzes($offset, $maxRecords, 'name_asc', $find_objects);
}

if( $prefs['feature_trackers'] == 'y' ) {
	include_once ('lib/trackers/trackerlib.php');
	$trackers = $trklib->list_trackers($offset, $maxRecords, 'name_asc', $find_objects);
}

if( $prefs['feature_articles'] == 'y' ) {
	global $artlib; require_once 'lib/articles/artlib.php';
	$articles = $artlib->list_articles($offset, $maxRecords, 'title_asc', $find_objects, '', '', $user, '', '', 'n');
}

if( $prefs['feature_directory'] == 'y' ) {
	include_once ('lib/directory/dirlib.php');
	$directories = $dirlib->dir_list_all_categories($offset, $maxRecords, 'name_asc', $find_objects);
}

$maximum = 0;
admin_categ_assign( $maximum, 'objects', $objects );
admin_categ_assign( $maximum, 'galleries', $galleries );
admin_categ_assign( $maximum, 'file_galleries', $file_galleries );
admin_categ_assign( $maximum, 'forums', $forums );
admin_categ_assign( $maximum, 'polls', $polls );
admin_categ_assign( $maximum, 'blogs', $blogs );
admin_categ_assign( $maximum, 'pages', $pages );
admin_categ_assign( $maximum, 'faqs', $faqs );
admin_categ_assign( $maximum, 'quizzes', $quizzes );
admin_categ_assign( $maximum, 'trackers', $trackers );
admin_categ_assign( $maximum, 'articles', $articles );
admin_categ_assign( $maximum, 'directories', $directories );

$smarty->assign( 'maxRecords', $maxRecords );
$smarty->assign( 'offset', $offset );
$smarty->assign( 'maximum', $maximum );

ask_ticket('admin-categories');
if (!empty($errors)) $smarty->assign('errors', $errors);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_categories.tpl');
$smarty->display("tiki.tpl");
