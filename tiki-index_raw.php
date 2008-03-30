<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-index_raw.php,v 1.29 2007-10-12 07:55:28 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');
include_once ('lib/wiki/wikilib.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error_raw.tpl");
	die;
}

//print($GLOBALS["HTTP_REFERER"]);

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', date("U"), 'Tiki initialization');
}

if (!isset($_SESSION["thedate"])) {
	$thedate = date("U");
} else {
	$thedate = $_SESSION["thedate"];
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$_REQUEST["page"] = $wikilib->get_default_wiki_page();
}
$page = $_REQUEST['page'];
$smarty->assign('page', $page);


require_once ('tiki-pagesetup.php');

// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));

	$smarty->display("error_raw.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));

	$smarty->display("error_raw.tpl");
	die;
}

// BreadCrumbNavigation here
// Remember to reverse the array when posting the array

if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}

if (!in_array($page, $_SESSION["breadCrumb"])) {
	if (count($_SESSION["breadCrumb"]) > $prefs['userbreadCrumb']) {
		array_shift ($_SESSION["breadCrumb"]);
	}

	array_push($_SESSION["breadCrumb"], $page);
} else {
	// If the page is in the array move to the last position
	$pos = array_search($page, $_SESSION["breadCrumb"]);

	unset ($_SESSION["breadCrumb"][$pos]);
	array_push($_SESSION["breadCrumb"], $page);
}

//print_r($_SESSION["breadCrumb"]);

// Now increment page hits since we are visiting this page
if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
	$tikilib->add_hit($page);
}

// Get page data
$info = $tikilib->get_page_info($page);

// Verify lock status
if ($info["flag"] == 'L') {
	$smarty->assign('lock', true);
} else {
	$smarty->assign('lock', false);
}

// If not locked and last version is user version then can undo
$smarty->assign('canundo', 'n');

if ($info["flag"] != 'L' && (($tiki_p_edit == 'y' && $info["user"] == $user) || ($tiki_p_remove == 'y'))) {
	$smarty->assign('canundo', 'y');
}

if ($tiki_p_admin_wiki == 'y') {
	$smarty->assign('canundo', 'y');
}

// Get ~pp~, ~np~ and <pre> out of the way. --rlpowell, 24 May 2004
$preparsed = array();
$noparsed = array();
$tikilib->parse_first( $info["data"], $preparsed, $noparsed );

$pdata = $tikilib->parse_data_raw($info["data"]);

if (!isset($_REQUEST['pagenum']))
	$_REQUEST['pagenum'] = 1;

$pages = $wikilib->get_number_of_pages($pdata);
$pdata = $wikilib->get_page($pdata, $_REQUEST['pagenum']);
$smarty->assign('pages', $pages);

if ($pages > $_REQUEST['pagenum']) {
	$smarty->assign('next_page', $_REQUEST['pagenum'] + 1);
} else {
	$smarty->assign('next_page', $_REQUEST['pagenum']);
}

if ($_REQUEST['pagenum'] > 1) {
	$smarty->assign('prev_page', $_REQUEST['pagenum'] - 1);
} else {
	$smarty->assign('prev_page', 1);
}

$smarty->assign('first_page', 1);
$smarty->assign('last_page', $pages);
$smarty->assign('pagenum', $_REQUEST['pagenum']);

// Put ~pp~, ~np~ and <pre> back. --rlpowell, 24 May 2004
$tikilib->replace_preparse( $info["data"], $preparsed, $noparsed );
$tikilib->replace_preparse( $pdata, $preparsed, $noparsed );

$smarty->assign_by_ref('parsed', $pdata);
//$smarty->assign_by_ref('lastModif',date("l d of F, Y  [H:i:s]",$info["lastModif"]));
$smarty->assign_by_ref('lastModif', $info["lastModif"]);

if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}

$smarty->assign_by_ref('lastUser', $info["user"]);

/*
// force enable wiki comments (for development)
$prefs['feature_wiki_comments'] = 'y';
*/

// Comments engine!
if ($prefs['feature_wiki_comments'] == 'y') {
	$comments_per_page = $prefs['wiki_comments_per_page'];

	$thread_sort_mode = $prefs['wiki_comments_default_ordering'];
	$comments_vars = array('page');
	$comments_prefix_var = 'wiki page:';
	$comments_object_var = 'page';
	include_once ("comments.php");
}

$section = 'wiki';
include_once ('tiki-section_options.php');
ask_ticket('index-raw');

// Display the Index Template
$smarty->assign('dblclickedit', 'y');
$smarty->display("tiki-show_page_raw.tpl");

?>
