<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/directory/dirlib.php');
$access->check_feature('feature_directory');
$access->check_permission('tiki_p_validate_links');
if (isset($_REQUEST["validate"]) && isset($_REQUEST['sites'])) {
	check_ticket('dir-validate');
	foreach (array_keys($_REQUEST["sites"]) as $siteId) {
		$dirlib->dir_validate_site($siteId);
	}
}
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$dirlib->dir_remove_site($_REQUEST["remove"]);
}
if (isset($_REQUEST["del"]) && isset($_REQUEST['sites'])) {
	check_ticket('dir-validate');
	foreach (array_keys($_REQUEST["sites"]) as $siteId) {
		$dirlib->dir_remove_site($siteId);
	}
}
// Listing: invalid sites
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
$items = $dirlib->dir_list_invalid_sites($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $items["cant"]);
$smarty->assign_by_ref('items', $items["data"]);
// This page should be displayed with Directory section options
$section = 'directory';
include_once ('tiki-section_options.php');
ask_ticket('dir-validate');
// Display the template
$smarty->assign('mid', 'tiki-directory_validate_sites.tpl');
$smarty->display("tiki.tpl");
