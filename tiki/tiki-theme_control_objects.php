<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-theme_control_objects.php,v 1.3 2003-10-08 03:53:09 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/filegals/filegallib.php');

function correct_array(&$arr, $id, $name) {
	for ($i = 0; $i < count($arr); $i++) {
		$arr[$i]['objId'] = $arr[$i][$id];

		$arr[$i]['objName'] = $arr[$i][$name];
	}
}

if ($feature_theme_control != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_theme_control");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$styles = array();
$h = opendir("styles/");

while ($file = readdir($h)) {
	if (strstr($file, "css")) {
		$styles[] = $file;
	}
}

closedir ($h);
$smarty->assign_by_ref('styles', $styles);
$find_objects = '';
$types = array(
	'image gallery',
	'file gallery',
	'forum',
	'blog',
	'wiki page',
	'faq',
	'quiz',
	'article'
);

$smarty->assign('types', $types);

if (!isset($_REQUEST['type']))
	$_REQUEST['type'] = 'wiki page';

$smarty->assign('type', $_REQUEST['type']);

switch ($_REQUEST['type']) {
case 'image gallery':
	$objects = $tikilib->list_galleries(0, -1, 'name_desc', 'admin', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'galleryId', 'name');
	break;

case 'file gallery':
	$objects = $filegallib->list_file_galleries(0, -1, 'name_desc', 'admin', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'galleryId', 'name');
	break;

case 'forum':
	$objects = $tikilib->list_forums(0, -1, 'name_asc', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'forumId', 'name');
	break;

case 'blog':
	$objects = $tikilib->list_blogs(0, -1, 'title_asc', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'blogId', 'title');
	break;

case 'wiki page':
	$objects = $tikilib->list_pages(0, -1, 'pageName_asc', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'pageName', 'pageName');
	break;

case 'faq':
	$objects = $tikilib->list_faqs(0, -1, 'title_asc', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'faqId', 'title');
	break;

case 'quiz':
	$objects = $tikilib->list_quizzes(0, -1, 'name_asc', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'quizId', 'name');
	break;

case 'article':
	$objects = $tikilib->list_articles(0, -1, 'title_asc', $find_objects, '', $user);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'articleId', 'title');
	break;

default:
	break;
}

$smarty->assign_by_ref('objects', $objects);

if (isset($_REQUEST['assign'])) {
	list($id, $name) = explode('|', $_REQUEST['objdata']);

	$tcontrollib->tc_assign_object($id, $_REQUEST['theme'], $_REQUEST['type'], $name);
}

if (isset($_REQUEST["delete"])) {
	foreach (array_keys($_REQUEST["obj"])as $obj) {
		$tcontrollib->tc_remove_object($obj);
	}
}

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

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $tcontrollib->tc_list_objects($_REQUEST['type'], $offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('channels', $channels["data"]);

//$sections=Array('wiki','galleries','file_galleries','cms','blogs','forums','chat','categories','games','faqs','html_pages','quizzes','surveys','webmail','trackers','featured_links','directory','user_messages','newsreader','mytiki');

// Display the template
$smarty->assign('mid', 'tiki-theme_control_objects.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
