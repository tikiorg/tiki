<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-backlinks.php,v 1.15 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_backlinks'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_backlinks");

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
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view backlinks for this page"));

	$smarty->display("error.tpl");
	die;
}

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("The page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

// Get the backlinks for the page "page"
$backlinks = $wikilib->get_backlinks($page);
$smarty->assign_by_ref('backlinks', $backlinks);
ask_ticket('backlinks');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-backlinks.tpl');
$smarty->display("tiki.tpl");

?>
