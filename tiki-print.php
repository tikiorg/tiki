<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-print.php,v 1.36.2.3 2008-02-29 23:15:46 sylvieg Exp $

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

if ($prefs['feature_wiki_print'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki_print");

	$smarty->display("error.tpl");
	die;
}

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', $tikilib->now, 'Tiki initialization');
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$page = $prefs['wikiHomePage'];

	$smarty->assign('page', $prefs['wikiHomePage']);
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
if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
	$tikilib->add_hit($page);
}

// Get page data
$info = $tikilib->get_page_info($page);

if (isset($prefs['wiki_feature_copyrights']) && $prefs['wiki_feature_copyrights'] == 'y' && isset($prefs['wikiLicensePage'])) {
	// insert license if wiki copyrights enabled
	$license_info = $tikilib->get_page_info($prefs['wikiLicensePage']);

	$tikilib->add_hit($prefs['wikiLicensePage']);
	$info["data"] = $info["data"] . "\n<HR>\n" . $license_info["data"];
	$_REQUEST['copyrightpage'] = $page;
}

// Verify lock status
if ($info["flag"] == 'L') {
	$smarty->assign('lock', true);
} else {
	$smarty->assign('lock', false);
}

if (isset($_REQUEST['page_ref_id'])) {
    // If a structure page has been requested
    $page_ref_id = $_REQUEST['page_ref_id'];
}

$pdata = $tikilib->parse_data($info["data"],$info["is_html"]);
$smarty->assign_by_ref('parsed', $pdata);
$smarty->assign_by_ref('lastModif', $info["lastModif"]);

if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}

$smarty->assign_by_ref('lastVersion',$info["version"]);
$smarty->assign_by_ref('lastUser', $info["user"]);

if (isset($structure) && $structure == 'y' && isset($page_info['page_alias']) && $page_info['page_alias'] != '') $crumbpage = $page_info['page_alias'];
else $crumbpage = $page;

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
$smarty->assign('print_page', 'y');
$smarty->assign('urlprefix', $base_url); // Obsolete, use base_url instead. This is for compatibility purposes only.
$smarty->assign('mid', 'tiki-show_page.tpl');

$smarty->assign('display', isset($_REQUEST['display'])?$_REQUEST['display']: '');
// Allow PDF export by installing a Mod that define an appropriate function
if ( isset($_REQUEST['display']) && $_REQUEST['display'] == 'pdf' ) {
	// Method using 'mozilla2ps' mod
	if ( file_exists('lib/mozilla2ps/mod_urltopdf.php') ) {
		include_once('lib/mozilla2ps/mod_urltopdf.php');
		mod_urltopdf();
	}
} else {
	$smarty->display('tiki-print.tpl');
}

?>
