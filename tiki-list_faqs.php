<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-list_faqs.php,v 1.18 2007-10-12 07:55:28 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'faqs';
require_once ('tiki-setup.php');

include_once ('lib/faqs/faqlib.php');

if ($prefs['feature_faqs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_faqs");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_faqs != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["faqId"])) {
	$_REQUEST["faqId"] = 0;
}

$smarty->assign('faqId', $_REQUEST["faqId"]);

if ($_REQUEST["faqId"]) {
	$info = $tikilib->get_faq($_REQUEST["faqId"]);
} else {
	$info = array();

	$info["title"] = '';
	$info["description"] = '';
	$info["canSuggest"] = 'n';
}

$smarty->assign('title', $info["title"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('canSuggest', $info["canSuggest"]);

if (isset($_REQUEST["remove"])) {
	if ($tiki_p_admin_faqs != 'y') {
		$smarty->assign('msg', tra("You do not have permission to use this feature"));
		$smarty->display("error.tpl");
		die;
	}
  $area = 'delfaq';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$faqlib->remove_faq($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["save"])) {
	check_ticket('list-faqs');
	if ($tiki_p_admin_faqs != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to use this feature"));

		$smarty->display("error.tpl");
		die;
	}

	if (isset($_REQUEST["canSuggest"]) && $_REQUEST["canSuggest"] == 'on') {
		$canSuggest = 'y';
	} else {
		$canSuggest = 'n';
	}

	$fid = $faqlib->replace_faq($_REQUEST["faqId"], $_REQUEST["title"], $_REQUEST["description"], $canSuggest);

	$cat_type = 'faq';
	$cat_objid = $fid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-view_faq.php?faqId=" . $cat_objid;
	include_once ("categorize.php");

	$smarty->assign('faqId', 0);
	$smarty->assign('title', '');
	$smarty->assign('description', '');
	$smarty->assign('canSuggest', '');
}

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

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $tikilib->list_faqs($offset, $maxRecords, $sort_mode, $find);

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
$smarty->assign_by_ref('cant', $channels["cant"]);
$cat_type = 'faq';
$cat_objid = $_REQUEST["faqId"];
include_once ("categorize_list.php");

include_once ('tiki-section_options.php');
ask_ticket('list-faqs');

// Display the template
$smarty->assign('mid', 'tiki-list_faqs.tpl');
$smarty->display("tiki.tpl");

?>
