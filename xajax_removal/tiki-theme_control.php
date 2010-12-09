<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');
$access->check_feature('feature_theme_control');
$access->check_permission('tiki_p_admin');

$auto_query_args = array('find', 'sort_mode', 'offset', 'theme', 'theme-option', 'categId');

$categories = $categlib->get_all_categories();
$smarty->assign('categories', $categories);
$smarty->assign('categId', isset($_REQUEST['categId']) ? $_REQUEST['categId'] : 0);

$tcontrollib->setup_theme_menus();

if (isset($_REQUEST['assigcat'])) {
	if (isset($_REQUEST['categId'])) {
		check_ticket('theme-control');
		$tcontrollib->tc_assign_category($_REQUEST['categId'], $_REQUEST['theme'], isset($_REQUEST['theme-option']) ? $_REQUEST['theme-option'] : '');
	} else {
		$smarty->assign('msg', tra("Please create a category first"));
		$smarty->display("error.tpl");
		die;
	}
}
if (isset($_REQUEST["delete"])) {
	if (isset($_REQUEST["categ"])) {
		check_ticket('theme-control');
		foreach(array_keys($_REQUEST["categ"]) as $cat) {
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
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('theme-control');
// Display the template
$smarty->assign('mid', 'tiki-theme_control.tpl');
$smarty->display("tiki.tpl");

