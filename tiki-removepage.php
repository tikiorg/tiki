<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-removepage.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/histlib.php');
include_once ('lib/wiki/wikilib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

include_once ("tiki-pagesetup.php");

// Now check permissions to access this page
if ($tiki_p_remove != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot remove versions from this page"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($_REQUEST["version"] <> "last") {
	$smarty->assign_by_ref('version', $_REQUEST["version"]);

	$version = $_REQUEST["version"];
} else {
	$smarty->assign('version', 'last');

	$version = "last";
}

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
	if (isset($_REQUEST["all"]) && $_REQUEST["all"] == 'on') {
		$tikilib->remove_all_versions($_REQUEST["page"]);

		header ("location: tiki-index.php");
		die;
	} else {
		if ($version == "last") {
			$wikilib->remove_last_version($_REQUEST["page"]);
		} else {
			$histlib->remove_version($_REQUEST["page"], $_REQUEST["version"]);
		}

		header ("location: tiki-index.php");
		die;
	}
}

$smarty->assign('mid', 'tiki-removepage.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("styles/$style_base/tiki.tpl");

?>