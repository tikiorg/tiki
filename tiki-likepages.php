<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-likepages.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($feature_likePages != 'y') {
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
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view pages like this page"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$likepages = $wikilib->get_like_pages($page);
$smarty->assign_by_ref('likepages', $likepages);

// Display the template
$smarty->assign('mid', 'tiki-likepages.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("styles/$style_base/tiki.tpl");

?>