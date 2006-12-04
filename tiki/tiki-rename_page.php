<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-rename_page.php,v 1.18 2006-12-04 09:22:12 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');

if ($feature_wiki != 'y') {
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

// Now check permissions to rename this page
$info=null;
if ($tiki_p_rename == 'y') {
	if ($tiki_p_admin_wiki != 'y' && $feature_wiki_usrlock == 'y') {
		$info = $tikilib->get_page_info($page);
		$allowed = ($wikilib->is_editable($page, $user, $info))? 'y': 'n';
	} else {
		$allowed = 'y';
	}
} else {
	$allowed = 'n';
}
if ($allowed == 'n') {
	$smarty->assign('msg', tra("Permission denied you cannot rename this page"));

	$smarty->display("error.tpl");
	die;
}

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page,true)) { // true: casesensitive check here
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["rename"])) {
	check_ticket('rename-page');
	// If the new pagename does match userpage prefix then display an error
	$newName = $_REQUEST['newpage'];
	if (stristr($newName, $feature_wiki_userpage_prefix) == $newName) {//stripos is only php5
		$smarty->assign('msg', tra("Cannot rename page because the new name begins with reserved prefix").' ('.$feature_wiki_userpage_prefix.').');

		$smarty->display("error.tpl");
		die;
	}
	if (!$wikilib->wiki_rename_page($page, $newName)) {
		$smarty->assign('msg', tra("Cannot rename page maybe new page already exists"));

		$smarty->display("error.tpl");
		die;
	}

	header ('location: tiki-index.php?page='.urlencode($newName));
}

ask_ticket('rename-page');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-rename_page.tpl');
$smarty->display("tiki.tpl");

?>
