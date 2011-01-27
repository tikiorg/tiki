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
$smarty->assign('page', $page);

// If the page doesn't exist then display an error
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra("Page cannot be found"));
	$smarty->display("error_raw.tpl");
	die;
}

require_once 'lib/wiki/renderlib.php';
$pageRenderer = new WikiRenderer( $info, $user );
$objectperms = $pageRenderer->applyPermissions();

if ($prefs['flaggedrev_approval'] == 'y' && isset($_REQUEST['latest']) && $objectperms->wiki_view_latest) {
	$pageRenderer->forceLatest();
}

$access->check_permission('tiki_p_view');

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

if( isset( $_REQUEST['pagenum'] ) && $_REQUEST['pagenum'] > 0 ) {
	$pageRenderer->setPageNumber( (int) $_REQUEST['pagenum'] );
}

$pageRenderer->useRaw();

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
$pageRenderer->runSetups();
ask_ticket('index-raw');

// Display the Index Template
$smarty->assign('dblclickedit', 'y');

// If the url has the param "download", ask the browser to download it (instead of displaying it)
if ( isset($_REQUEST['download']) && $_REQUEST['download'] !== 'n' ) {
	if (isset($_REQUEST["filename"])) {
		$filename = $_REQUEST['filename'];
	} else {
		$filename = $page;
	}
	$filename = str_replace(array('?',"'",'"',':','/','\\'), '_', $filename);	// clean some bad chars
	header("Content-type: text/plain; charset=utf-8");
	header("Content-Disposition: attachment; filename=\"$filename\"");
}

// add &full to URL to output the whole html head and body
if (isset($_REQUEST['full']) && $_REQUEST['full'] != 'n') {
	$smarty->assign('mid','tiki-show_page_raw.tpl');
	// use tiki_full to include include CSS and JavaScript
	$smarty->display("tiki_full.tpl");
} else if (isset($_REQUEST['textonly']) && $_REQUEST['textonly'] != 'n') {
	$output = $smarty->fetch("tiki-show_page_raw.tpl");
	$output = strip_tags($output);
	$output = $tikilib->htmldecode($output);
	echo $output;
} else {
	// otherwise just the contents of the page without body etc
	$smarty->display("tiki-show_page_raw.tpl");
}
