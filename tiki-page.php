<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-page.php,v 1.4 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/htmlpages/htmlpageslib.php');

if ($feature_html_pages != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_view_html_pages != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["pageName"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$page_data = $htmlpageslib->get_html_page($_REQUEST["pageName"]);
$smarty->assign('type', $page_data["type"]);
$smarty->assign('refresh', $page_data["refresh"]);
$smarty->assign('pageName', $_REQUEST["pageName"]);
$parsed = $htmlpageslib->parse_html_page($_REQUEST["pageName"], $page_data["content"]);
$smarty->assign_by_ref('parsed', $parsed);

$section = 'html_pages';
include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-page.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>