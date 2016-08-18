<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

$access->check_permission(array('remove', 'edit'), '', 'wiki page', $page);

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
if (isset($_REQUEST["remove"])) {
	if (isset($_REQUEST["all"]) && $_REQUEST["all"] == 'on') {
		$access->check_authenticity(tr('Are you sure you want to completely remove page "%0"', $_REQUEST['page']));
		$tikilib->remove_all_versions($_REQUEST["page"]);
		$access->redirect();
		die;
	} else {
		$smarty->loadPlugin('smarty_modifier_sefurl');
		if ($version == "last") {
			$access->check_authenticity(tr('Are you sure you want to remove the most recent version of page "%0"', $_REQUEST['page']));
			$wikilib->remove_last_version($_REQUEST["page"]);
			$access->redirect(smarty_modifier_sefurl($_REQUEST['page']));
		} else {
			$access->check_authenticity(tr('Are you sure you want to remove version %0 of page "%1"', $_REQUEST['version'], $_REQUEST['page']));
			$histlib->remove_version($_REQUEST['page'], $_REQUEST['version'], $_REQUEST['historyId']);
			$access->redirect(smarty_modifier_sefurl($_REQUEST['page']));
		}
	}
}
ask_ticket('remove-page');
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-removepage.tpl');
$smarty->display("tiki.tpl");
