<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-rename_page.php,v 1.5 2003-10-08 03:53:08 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

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
if ($tiki_p_rename != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot remove versions from this page"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST["rename"])) {
	if (!$wikilib->wiki_rename_page($_REQUEST['oldpage'], $_REQUEST['newpage'])) {
		$smarty->assign('msg', tra("Cannot rename page maybe new page already exists"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$newName = $_REQUEST['newpage'];
	header ("location: tiki-index.php?page=$newName");
}

$smarty->assign('mid', 'tiki-rename_page.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("styles/$style_base/tiki.tpl");

?>
