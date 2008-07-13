<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_html_page_content.php,v 1.15 2007-10-12 07:55:23 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/htmlpages/htmlpageslib.php');

if ($prefs['feature_html_pages'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_html_pages");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_edit_html_pages != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["pageName"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
}

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

/* NO REMOVAL
if(isset($_REQUEST["remove"])) {
  $htmlpageslib->remove_html_page_content($_REQUEST["pageName"],$_REQUEST["remove"]);
}
*/
if (isset($_REQUEST["editmany"])) {
	check_ticket('admin-html-page-content');
	$zones = $htmlpageslib->list_html_page_content($_REQUEST["pageName"], 0, -1, 'zone_asc', '');

	$temp_max = count($zones["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		if (isset($_REQUEST[$zones["data"][$i]["zone"]])) {
			$htmlpageslib->replace_html_page_content($_REQUEST["pageName"], $zones["data"][$i]["zone"],
				$_REQUEST[$zones["data"][$i]["zone"]]);
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
ask_ticket('admin-html-page-content');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_html_page_content.tpl');
$smarty->display("tiki.tpl");

?>
