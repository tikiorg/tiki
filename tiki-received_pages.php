<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-received_pages.php,v 1.6 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/commcenter/commlib.php');
include_once ('lib/wiki/wikilib.php');

if ($feature_comm != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_admin_received_pages != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["receivedPageId"])) {
	$_REQUEST["receivedPageId"] = 0;
}

$smarty->assign('receivedPageId', $_REQUEST["receivedPageId"]);

if (isset($_REQUEST["accept"])) {
	// CODE TO ACCEPT A PAGE HERE
	$commlib->accept_page($_REQUEST["accept"]);
}

if ($_REQUEST["receivedPageId"]) {
	$info = $commlib->get_received_page($_REQUEST["receivedPageId"]);
} else {
	$info = array();

	$info["pageName"] = '';
	$info["data"] = '';
	$info["comment"] = '';
}

$smarty->assign('view', 'n');

if (isset($_REQUEST["view"])) {
	$info = $commlib->get_received_page($_REQUEST["view"]);

	$smarty->assign('view', 'y');
}

if (isset($_REQUEST["preview"])) {
	$info["pageName"] = $_REQUEST["pageName"];

	$info["data"] = $_REQUEST["data"];
	$info["comment"] = $_REQUEST["comment"];
}

$smarty->assign('pageName', $info["pageName"]);
$smarty->assign('data', $info["data"]);
$smarty->assign('comment', $info["comment"]);

// Assign parsed
$smarty->assign('parsed', $tikilib->parse_data($info["data"]));

if (isset($_REQUEST["remove"])) {
	$commlib->remove_received_page($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	$commlib->update_received_page($_REQUEST["receivedPageId"], $_REQUEST["pageName"], $_REQUEST["data"], $_REQUEST["comment"]);

	$smarty->assign('pageName', $_REQUEST["pageName"]);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('comment', $_REQUEST["comment"]);
	$smarty->assign('receivedPageId', $_REQUEST["receivedPageId"]);
	$smarty->assign('parsed', $tikilib->parse_data($_REQUEST["data"]));
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'receivedDate_desc';
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
$channels = $tikilib->list_received_pages($offset, $maxRecords, $sort_mode, $find);

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

// Display the template
$smarty->assign('mid', 'tiki-received_pages.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>