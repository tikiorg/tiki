<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section

require_once ('tiki-setup.php');
$histlib = TikiLib::lib('hist');
$wikilib = TikiLib::lib('wiki');

$access->check_feature('feature_wiki');

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}

$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission(array('tiki_p_edit'));

if ($_REQUEST["version"] <> "last") {
	$smarty->assign_by_ref('version', $_REQUEST["version"]);
	$version = $_REQUEST["version"];
} else {
	$smarty->assign('version', 'last'); //get_strings tra('last version');
	$version = "last";
}
// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["copy"]) || isset($_REQUEST["confirm"])) {
	check_ticket('copy-page');
	// If the new pagename does match userpage prefix then display an error
	$newName = isset($_REQUEST["confirm"]) ? $_REQUEST['badname'] : $_REQUEST['newpage'];
	if (stristr($newName, $prefs['feature_wiki_userpage_prefix']) == $newName) {
		$smarty->assign('msg', tra("Cannot rename page because the new name begins with reserved prefix") . ' (' . $prefs['feature_wiki_userpage_prefix'] . ').');
		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('newname', $newName);
	$result = false;
	if (!isset($_REQUEST["confirm"]) && $wikilib->contains_badchars($newName)) {
		$smarty->assign('page_badchars_display', $wikilib->get_badchars());
	} else {
		$result = $wikilib->wiki_duplicate_page($page, $newName);

		if ($result) {
			if ($prefs['feature_sefurl'] == 'y') {
				include_once('tiki-sefurl.php');
				header('location: '. urlencode(filter_out_sefurl("tiki-index.php?page=$newName", 'wiki')));
			} else {
				header('location: tiki-index.php?page=' . urlencode($newName));
			}
		} else {
			$smarty->assign('msg', tra("Cannot copy page because maybe new page name already exists"));
			$smarty->display("error.tpl");
			die;
		}
	}
}
ask_ticket('copy-page');
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-copypage.tpl');
$smarty->display("tiki.tpl");
