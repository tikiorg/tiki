<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-theme_control.php,v 1.16 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');

if ($prefs['feature_theme_control'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_theme_control");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$categories = $categlib->get_all_categories();
$smarty->assign('categories', $categories);

$list_styles = $tikilib->list_styles();
$smarty->assign_by_ref('styles',$list_styles);

if (isset($_REQUEST['assigcat'])) {
	if (isset($_REQUEST['categId'])) {
		check_ticket('theme-control');
		$tcontrollib->tc_assign_category($_REQUEST['categId'], $_REQUEST['theme']);
	} else {
		$smarty->assign('msg', tra("Please create a category first"));

		$smarty->display("error.tpl");
		die;
	}
}

if (isset($_REQUEST["delete"])) {
	if (isset($_REQUEST["categ"])) {
		check_ticket('theme-control');
		foreach (array_keys($_REQUEST["categ"])as $cat) {
			$tcontrollib->tc_remove_cat($cat);
		}
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
$channels = $tcontrollib->tc_list_categories($offset, $maxRecords, $sort_mode, $find);

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

ask_ticket('theme-control');

// Display the template
$smarty->assign('mid', 'tiki-theme_control.tpl');
$smarty->display("tiki.tpl");

?>
