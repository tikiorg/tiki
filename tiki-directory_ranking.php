<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'directory';
require_once ('tiki-setup.php');
include_once ('lib/directory/dirlib.php');
$access->check_feature('feature_directory');
$access->check_permission('tiki_p_view_directory');
if (isset($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
}
// Listing: sites
// Pagination resolution
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$smarty->assign('find', $find);
$items = $dirlib->dir_list_all_valid_sites($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign_by_ref('cant', $items["cant"]);
include_once ('tiki-section_options.php');
ask_ticket('dir-ranking');
$smarty->assign('mid', 'tiki-directory_ranking.tpl');
$smarty->display("tiki.tpl");
