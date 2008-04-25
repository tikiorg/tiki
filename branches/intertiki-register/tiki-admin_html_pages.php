<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_html_pages.php,v 1.16.2.1 2007-10-29 22:14:01 pkdille Exp $

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
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

include_once("textareasize.php");

if (!isset($_REQUEST["pageName"])) {
	$_REQUEST["pageName"] = '';
}

$smarty->assign('pageName', $_REQUEST["pageName"]);

if ($_REQUEST["pageName"]) {
	$info = $htmlpageslib->get_html_page($_REQUEST["pageName"]);
} else {
	$info = array();

	$info["pageName"] = '';
	$info["content"] = '';
	$info["refresh"] = 0;
	$info["type"] = 's';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	$area = 'delhtmlpage';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$htmlpageslib->remove_html_page($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["content"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');

	//$parsed = $tikilib->parse_data($_REQUEST["content"]);
	$parsed = $htmlpageslib->parse_html_page($_REQUEST["pageName"], $_REQUEST["content"]);
	$smarty->assign('parsed', $parsed);
	$info["content"] = $_REQUEST["content"];
	$info["refresh"] = $_REQUEST["refresh"];
	$info["pageName"] = $_REQUEST["pageName"];
	$info["type"] = $_REQUEST["type"];
	$smarty->assign('info', $info);
}

if (isset($_REQUEST["save"]) && !empty($_REQUEST["pageName"])) {
	check_ticket('admin-html-pages');
	$tid = $htmlpageslib->replace_html_page($_REQUEST["pageName"], $_REQUEST["type"], $_REQUEST["content"], $_REQUEST["refresh"]);

	$smarty->assign("pageName", '');
	$info["pageName"] = '';
	$info["content"] = '';
	$info["regresh"] = 0;
	$info["type"] = 's';
	$smarty->assign('info', $info);
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
$channels = $htmlpageslib->list_html_pages($offset, $maxRecords, $sort_mode, $find);

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

if ($tiki_p_use_content_templates == 'y') {
	$templates = $tikilib->list_templates('html', 0, -1, 'name_asc', '');
}

$smarty->assign_by_ref('templates', $templates["data"]);
ask_ticket('admin-html-pages');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_html_pages.tpl');
$smarty->display("tiki.tpl");

?>
