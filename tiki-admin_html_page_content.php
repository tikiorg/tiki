<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/htmlpages/htmlpageslib.php');
$access->check_feature('feature_html_pages');
$access->check_permission('tiki_p_edit_html_pages');

if (!isset($_REQUEST["pageName"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
}
$auto_query_args = array(
	'find',
	'offset',
	'pageName',
	'sort_mode'
);
$smarty->assign('pageName', $_REQUEST["pageName"]);
if (!isset($_REQUEST["zone"])) {
	$_REQUEST["zone"] = '';
}
$smarty->assign('zone', $_REQUEST["zone"]);
$page_info = $htmlpageslib->get_html_page($_REQUEST["pageName"]);
if ($_REQUEST["zone"]) {
	$info = $htmlpageslib->get_html_page_content($_REQUEST["pageName"], $_REQUEST["zone"]);
} else {
	$info = array();
	$info["content"] = '';
	$info["type"] = '';
}
$smarty->assign('content', $info["content"]);
$smarty->assign('type', $info["type"]);
if (isset($_REQUEST["editmany"])) {
	check_ticket('admin-html-page-content');
	$zones = $htmlpageslib->list_html_page_content($_REQUEST["pageName"], 0, -1, 'zone_asc', '');
	$temp_max = count($zones["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		if (isset($_REQUEST[$zones["data"][$i]["zone"]])) {
			$htmlpageslib->replace_html_page_content($_REQUEST["pageName"], $zones["data"][$i]["zone"], $_REQUEST[$zones["data"][$i]["zone"]]);
		}
	}
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-html-page-content');
	$htmlpageslib->replace_html_page_content($_REQUEST["pageName"], $_REQUEST["zone"], $_REQUEST["content"]);
	$smarty->assign('zone', '');
	$smarty->assign('content', '');
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'zone_asc';
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
$channels = $htmlpageslib->list_html_page_content($_REQUEST["pageName"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-html-page-content');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_html_page_content.tpl');
$smarty->display("tiki.tpl");
