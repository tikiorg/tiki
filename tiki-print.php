<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-print.php,v 1.27 2006-12-04 10:03:38 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($feature_wiki_print != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki_print");

	$smarty->display("error.tpl");
	die;
}

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($wikiHomePage)) {
	$tikilib->create_page($wikiHomePage, 0, '', date("U"), 'Tiki initialization');
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$page = $wikiHomePage;

	$smarty->assign('page', $wikiHomePage);
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

require_once ('tiki-pagesetup.php');

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if (!$tikilib->user_has_perm_on_object($user, $_REQUEST["page"],'wiki page','tiki_p_view')) {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));

	$smarty->display("error.tpl");
	die;
}

// Now increment page hits since we are visiting this page
if ($count_admin_pvs == 'y' || $user != 'admin') {
	$tikilib->add_hit($page);
}

// Get page data
$info = $tikilib->get_page_info($page);

if (isset($wiki_feature_copyrights) && $wiki_feature_copyrights == 'y' && isset($wikiLicensePage)) {
	// insert license if wiki copyrights enabled
	$license_info = $tikilib->get_page_info($wikiLicensePage);

	$tikilib->add_hit($wikiLicensePage);
	$info["data"] = $info["data"] . "\n<HR>\n" . $license_info["data"];
	$_REQUEST['copyrightpage'] = $page;
}

// Verify lock status
if ($info["flag"] == 'L') {
	$smarty->assign('lock', true);
} else {
	$smarty->assign('lock', false);
}

$pdata = $tikilib->parse_data($info["data"]);
$smarty->assign_by_ref('parsed', $pdata);
$smarty->assign_by_ref('lastModif', $info["lastModif"]);

if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}

$smarty->assign_by_ref('lastUser', $info["user"]);

//Store the page URL to be displayed on print page
$http_domain = $tikilib->get_preference('http_domain', false);
$http_port = $tikilib->get_preference('http_port', 80);
$http_prefix = $tikilib->get_preference('http_prefix', '/');
$http_svrname = $tikilib->get_preference('feature_server_name','');
if ($http_domain) {

	$prefix = 'http://' . $http_domain;

	if ($http_port != 80)
		$prefix .= ':' . $http_port;

	$prefix .= $http_prefix;
	$smarty->assign('urlprefix', $prefix);
} else {
  $prefix = 'http://'.$http_svrname;
  $prefix .= $http_prefix;
  $smarty->assign('urlprefix', $prefix);
}

if (isset($structure) && $structure == 'y' && isset($page_info['page_alias']) && $page_info['page_alias'] != '') {
    $crumbpage = $page_info['page_alias'];
} else {
    $crumbpage = $page;
}

$crumbs[] = new Breadcrumb($crumbpage,
			   $info["description"],
			   'tiki-index.php?page='.urlencode($page),
			   '',
			   '');

ask_ticket('print');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$creator = $wikilib->get_creator($page);
$smarty->assign('creator', $creator);
$smarty->assign('print_page','y');
$smarty->assign('mid', 'tiki-show_page.tpl');
$smarty->display("tiki-print.tpl");

?>
