<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-slideshow.php,v 1.27 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

//print($GLOBALS["HTTP_REFERER"]);

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', $tikilib->now, 'Tiki initialization');
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$_REQUEST["page"] = $prefs['wikiHomePage'];

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
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));

	$smarty->display("error.tpl");
	die;
}

// BreadCrumbNavigation here
// Get the number of pages from the default or userPreferences
// Remember to reverse the array when posting the array
$anonpref = $prefs['userbreadCrumb'];

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
$smarty->assign('page_info', $info);

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

//Now process the pages
preg_match_all("/-=([^=]+)=-/", $info["data"], $reqs);
$slides = split("-=[^=]+=-", $info["data"]);

if (count($slides) < 2) {
	$slides = explode("...page...", $info["data"]);

	array_unshift($slides, '');
}

if (!isset($_REQUEST["slide"]) or $_REQUEST["slide"] < 0) {
	$_REQUEST["slide"] = 0;
}

if (!isset($slides[$_REQUEST["slide"] + 1])) {
	$_REQUEST["slide"] = count($slides) - 2;
}

$smarty->assign('prev_slide', $_REQUEST["slide"] - 1);
$smarty->assign('next_slide', $_REQUEST["slide"] + 1);

if (isset($reqs[1][$_REQUEST["slide"]])) {
	$slide_title = $reqs[1][$_REQUEST["slide"]];
} else {
	$slide_title = '';
}

$slide_data = $tikilib->parse_data($slides[$_REQUEST["slide"] + 1]);

if (isset($reqs[1][$_REQUEST["slide"] - 1])) {
	$slide_prev_title = $reqs[1][$_REQUEST["slide"] - 1];
} else {
	$slide_prev_title = '';
}

if (isset($reqs[1][$_REQUEST["slide"] + 1])) {
	$slide_next_title = $reqs[1][$_REQUEST["slide"] + 1];
} else {
	$slide_next_title = '';
}

$smarty->assign('slide_prev_title', $slide_prev_title);
$smarty->assign('slide_next_title', $slide_next_title);

$smarty->assign('slide_title', $slide_title);
$smarty->assign('slide_data', $slide_data);

$total_slides = count($slides) - 1;
$current_slide = $_REQUEST["slide"] + 1;
$smarty->assign('total_slides', $total_slides);
$smarty->assign('current_slide', $current_slide);

$smarty->assign_by_ref('lastModif', $info["lastModif"]);

if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}

$smarty->assign_by_ref('lastUser', $info["user"]);

include_once ('tiki-section_options.php');

$smarty->assign('wiki_extras', 'y');

ask_ticket('slideshow');

// Display the Index Template
$smarty->assign('dblclickedit', 'y');
$smarty->assign('mid', 'tiki-show_page.tpl');
$smarty->display("tiki-slideshow.tpl");

?>
