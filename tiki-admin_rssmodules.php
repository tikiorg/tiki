<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_rssmodules.php,v 1.26 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/rss/rsslib.php');

if (!isset($rsslib)) {
	$rsslib = new RssLib($dbTiki);
}

if( $tiki_p_admin_rssmodules != 'y' ) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["rssId"])) {
	$smarty->assign('rssId', $_REQUEST["rssId"]);
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["view"])) {
	$smarty->assign('preview', 'y');

	$data = $rsslib->get_rss_module_content($_REQUEST["view"]);
	$items = $rsslib->parse_rss_data($data, $_REQUEST["view"]);

	if ($items[0]["isTitle"]=="y") {
		$smarty->assign_by_ref('feedtitle', $items[0]);
		$items = array_slice ($items,1);
	}
	$smarty->assign_by_ref('items', $items);
}

if (isset($_REQUEST["rssId"])) {
	$info = $rsslib->get_rss_module($_REQUEST["rssId"]);
} else {
	$info = array();

  // default for new rss feed:
	$info["name"] = '';
	$info["description"] = '';
	$info["url"] = '';
	$info["refresh"] = 1800;
	$info["showTitle"] = 'n';
	$info["showPubDate"] = 'n';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('url', $info["url"]);
$smarty->assign('refresh', $info["refresh"]);
$smarty->assign('showTitle', $info["showTitle"]);
$smarty->assign('showPubDate', $info["showPubDate"]);

if (isset($_REQUEST["refresh"])) {
	$rsslib->get_rss_module_content($_REQUEST["refresh"], true);
}

if (isset($_REQUEST["remove"])) {
  $area = 'delrss';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$rsslib->remove_rss_module($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-rssmodules');

	if (isset($_REQUEST['showTitle']) == 'on') {
		$smarty->assign('showTitle', 'y');
		$info["showTitle"] = 'y';
	}
	else
	{
		$smarty->assign('showTitle', 'n');
		$info["showTitle"] = 'n';
	}
	if (isset($_REQUEST['showPubDate']) == 'on') {
		$smarty->assign('showPubDate', 'y');
		$info["showPubDate"] = 'y';
	}
	else
	{
		$smarty->assign('showPubDate', 'n');
		$info["showPubDate"] = 'n';
	}

	$rsslib->replace_rss_module($_REQUEST["rssId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["url"], $_REQUEST["refresh"], $info["showTitle"], $info["showPubDate"]);

	$smarty->assign('rssId', 0);
	$smarty->assign('name', '');
	$smarty->assign('description', '');
	$smarty->assign('url', '');
	$smarty->assign('refresh', 900);
	$smarty->assign('showTitle', 'n');
	$smarty->assign('showPubDate', 'n');
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
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
$channels = $rsslib->list_rss_modules($offset, $maxRecords, $sort_mode, $find);

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

$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
  $channels['data'][$i]['size'] = strlen($channels['data'][$i]['content']);
}

$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-rssmodules');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_rssmodules.tpl');
$smarty->display("tiki.tpl");

?>
