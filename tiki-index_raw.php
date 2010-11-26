<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');
include_once ('lib/wiki/wikilib.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error_raw.tpl");
	die;
}

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
if (isset($_REQUEST["filename"])) {
  $filename = $_REQUEST['filename'];
}
$smarty->assign('page', $page);

// If the page doesn't exist then display an error
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra("Page cannot be found"));
	$smarty->display("error_raw.tpl");
	die;
}

// Now check permissions to access this page
$tikilib->get_perm_object( $page, 'wiki page', $info);
if ($tiki_p_view != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied. You cannot view this page."));

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

// Comments engine!
if ($prefs['feature_wiki_comments'] == 'y') {
	$comments_per_page = $prefs['wiki_comments_per_page'];

	$thread_sort_mode = $prefs['wiki_comments_default_ordering'];
	$comments_vars = array('page');
	$comments_prefix_var = 'wiki page:';
	$comments_object_var = 'page';
	include_once ("comments.php");
}

include_once ('tiki-section_options.php');
ask_ticket('index-raw');

// Display the Index Template
$smarty->assign('dblclickedit', 'y');

// If the url has the param "download", ask the browser to download it (instead of displaying it)
if ( isset($_REQUEST['download']) && $_REQUEST['download'] !== 'n' ) {
  header("Content-type: text/plain");
  if ( isset($_REQUEST['filename']) ) { // allow the user to specify the file name & extension based on a value in the param filename=foo (for from pretty trackers, ...)
	header("Content-Disposition: attachment; filename=\"$filename\"");
  } else {
	header("Content-Disposition: attachment; filename=\"$page\"");
  }
}

// add &full to URL to output the whole html head and body
if (isset($_REQUEST['full']) && $_REQUEST['full'] != 'n') {
	$smarty->assign('mid','tiki-show_page_raw.tpl');
	// use tiki_full to include include CSS and JavaScript
	$smarty->display("tiki_full.tpl");
} else {
	// otherwise just the contents of the page without body etc
	$smarty->display("tiki-show_page_raw.tpl");
}
