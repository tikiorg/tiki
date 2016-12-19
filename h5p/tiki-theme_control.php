<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$themelib = TikiLib::lib('theme');
$themecontrollib = TikiLib::lib('themecontrol');
$categlib = TikiLib::lib('categ');
$access->check_feature('feature_theme_control');
$access->check_permission('tiki_p_admin');

$auto_query_args = array('find', 'sort_mode', 'offset', 'theme', 'categId');

//consider preference feature_theme_control_parentcategory setting when displaying list of available categories
if ($prefs['feature_theme_control_parentcategory'] != "n" && $prefs['feature_theme_control_parentcategory'] != -1) {
	$parentCategoryId = $prefs['feature_theme_control_parentcategory'];
	$categoryFilter = array(
		'type' => 'children',
		'identifier' => $parentCategoryId,
	);
}
else {
	$categoryFilter = array(
		'type' => 'all',
	);
}
$categories = $categlib->getCategories($categoryFilter, true, true, false);
$smarty->assign('categories', $categories);

$smarty->assign('categId', isset($_REQUEST['categId']) ? $_REQUEST['categId'] : 0);

$themes = $themelib->list_themes_and_options();
$smarty->assign('themes', $themes);

if (isset($_REQUEST['assign'])) {
	if (isset($_REQUEST['categoryId'])) {
		check_ticket('theme-control');
		$themecontrollib->tc_assign_category($_REQUEST['categoryId'], $_REQUEST['theme']);
	} else {
		$smarty->assign('msg', tra("Please create a category first"));
		$smarty->display("error.tpl");
		die;
	}
}
if (isset($_REQUEST['delete'])) {
	check_ticket('theme-control');
	foreach (array_keys($_REQUEST['categoryIds']) as $cat) {
		$themecontrollib->tc_remove_cat($cat);
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
$channels = $themecontrollib->tc_list_categories($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('theme-control');
// Display the template
$smarty->assign('mid', 'tiki-theme_control.tpl');
$smarty->display("tiki.tpl");

