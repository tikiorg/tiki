<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-theme_control_objects.php,v 1.19 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/filegals/filegallib.php');
include_once ('lib/htmlpages/htmlpageslib.php');

function correct_array(&$arr, $id, $name) {
	$temp_max = count($arr);
	for ($i = 0; $i < $temp_max; $i++) {
		$arr[$i]['objId'] = $arr[$i][$id];

		$arr[$i]['objName'] = $arr[$i][$name];
	}
}

if ($prefs['feature_theme_control'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_theme_control");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$list_styles = $tikilib->list_styles();
$smarty->assign_by_ref('styles', $list_styles);

$find_objects = '';
$types = array(
	'image gallery',
	'file gallery',
	'forum',
	'blog',
	'wiki page',
	'html page',
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
	require_once('lib/commentslib.php');
	if (!isset($commentslib)) {
		$commentslib = new Comments($dbTiki);
	}
	$objects = $commentslib->list_forums(0, -1, 'name_asc', $find_objects);

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
	$objects = $tikilib->list_pageNames(0, -1, 'pageName_asc', $find_objects);

	$smarty->assign_by_ref('objects', $objects["data"]);
	$objects = $objects['data'];
	correct_array($objects, 'pageName', 'pageName');
	break;

case 'html page':
	$objects = $htmlpageslib->list_html_pages(0, -1, 'pageName_asc', $find_objects);

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
	check_ticket('tc-objects');
	list($id, $name) = explode('|', $_REQUEST['objdata']);

	$tcontrollib->tc_assign_object($id, $_REQUEST['theme'], $_REQUEST['type'], $name);
}

if (isset($_REQUEST["delete"])) {
	check_ticket('tc-objects');
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

ask_ticket('tc-objects');

// Display the template
$smarty->assign('mid', 'tiki-theme_control_objects.tpl');
$smarty->display("tiki.tpl");

?>
