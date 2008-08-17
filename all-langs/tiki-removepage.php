<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-removepage.php,v 1.19 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/wiki/histlib.php');
include_once ('lib/wiki/wikilib.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

include_once ("tiki-pagesetup.php");

// Now check permissions to access this page
if ($tiki_p_remove != 'y' || !$wikilib->is_editable($page, $user)) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot remove versions from this page"));

	$smarty->display("error.tpl");
	die;
}

if ($_REQUEST["version"] <> "last") {
	$smarty->assign_by_ref('version', $_REQUEST["version"]);

	$version = $_REQUEST["version"];
} else {
	$smarty->assign('version', 'last');//get_strings tra('last version');

	$version = "last";
}

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
	check_ticket('remove-page');
	if (isset($_REQUEST["all"]) && $_REQUEST["all"] == 'on') {
		$tikilib->remove_all_versions($_REQUEST["page"]);

		header ("location: tiki-index.php");
		die;
	} else {
		if ($version == "last") {
			$wikilib->remove_last_version($_REQUEST["page"]);
		} else {
			$histlib->remove_version($_REQUEST['page'], $_REQUEST['version'], '', $_REQUEST['historyId']);
		}

		header ("location: tiki-index.php");
		die;
	}
}

ask_ticket('remove-page');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-removepage.tpl');
$smarty->display("tiki.tpl");

?>
