<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/wiki/histlib.php');
include_once ('lib/wiki/wikilib.php');

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
if (!isset($_REQUEST["version"])) {
	$smarty->assign('msg', tra("No version indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$version = $_REQUEST["version"];
	$smarty->assign_by_ref('version', $_REQUEST["version"]);
}
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}
if (!$histlib->version_exists($page, $version)) {
	$smarty->assign('msg', tra("Non-existent version"));
	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission( array('tiki_p_rollback', 'tiki_p_edit') );

if (isset($_REQUEST["rollback"])) {
	require_once('lib/diff/difflib.php');
	require_once('lib/categories/categlib.php');
	rollback_page_to_version($_REQUEST['page'], $_REQUEST['version']);
	header("location: tiki-index.php?page=" . urlencode($page));
	die;
}
$version = $histlib->get_version($page, $version);
$version["data"] = $tikilib->parse_data($version["data"], array('preview_mode' => true));
$smarty->assign_by_ref('preview', $version);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-rollback.tpl');
$smarty->display("tiki.tpl");
