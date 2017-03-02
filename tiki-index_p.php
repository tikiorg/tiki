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
require_once('tiki-setup.php');
$structlib = TikiLib::lib('struct');

$wikilib = TikiLib::lib('wiki');

$parserlib = TikiLib::lib('parser');

if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$access->check_feature('feature_wiki');

// Create the HomePage if it doesn't exist
if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', $tikilib->now, 'Tiki initialization');
}

if (!isset($_SESSION["thedate"])) {
	$thedate = $tikilib->now;
} else {
	$thedate = $_SESSION["thedate"];
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$_REQUEST["page"] = $wikilib->get_default_wiki_page();
}
$page = $_REQUEST['page'];
$smarty->assign('page', $page);

if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
	$tikilib->create_page($prefs['wikiHomePage'], 0, '', $tikilib->now, 'Tiki initialization');
}

if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}

require_once 'lib/wiki/renderlib.php';
$pageRenderer = new WikiRenderer($info, $user);
$objectperms = $pageRenderer->applyPermissions();

if ($prefs['flaggedrev_approval'] == 'y' && isset($_REQUEST['latest']) && $objectperms->wiki_view_latest) {
	$pageRenderer->forceLatest();
}

$access->check_permission('tiki_p_view', '', 'wiki page', $page);

// BreadCrumbNavigation here
// Remember to reverse the array when posting the array

if (!isset($_SESSION["breadCrumb"])) {
	$_SESSION["breadCrumb"] = array();
}

if (!in_array($page, $_SESSION["breadCrumb"])) {
	if (count($_SESSION["breadCrumb"]) > $prefs['userbreadCrumb']) {
		array_shift($_SESSION["breadCrumb"]);
	}

	array_push($_SESSION["breadCrumb"], $page);
} else {
	// If the page is in the array move to the last position
	$pos = array_search($page, $_SESSION["breadCrumb"]);

	unset($_SESSION["breadCrumb"][$pos]);
	array_push($_SESSION["breadCrumb"], $page);
}

// Now increment page hits since we are visiting this page
$tikilib->add_hit($page);

$smarty->assign('page_user', $info['user']);

if (($tiki_p_admin_wiki == 'y')
	|| ($user and ($user == $info['user']) and ($tiki_p_lock == 'y') and ($prefs['feature_wiki_usrlock'] == 'y'))) {
	if (isset($_REQUEST["action"])) {
		check_ticket('index-p');
		if ($_REQUEST["action"] == 'unlock') {
			$wikilib->unlock_page($page);
		}
	}
}

// Save to notepad if user wants to
if ($user && $prefs['feature_wiki_notepad'] == 'y' && $tiki_p_notepad == 'y' && $prefs['feature_notepad'] == 'y' && isset($_REQUEST['savenotepad'])) {
		check_ticket('index-p');
	include_once('lib/notepad/notepadlib.php');

	$notepadlib->replace_note($user, 0, $_REQUEST['page'], $info['data']);
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

if (isset($_REQUEST['refresh'])) {
	$tikilib->invalidate_cache($page);
}

// Here's where the data is parsed
// if using cache
//
// get cache information
// if cache is valid then pdata is cache
// else
// pdata is parse_data 
//   if using cache then update the cache
// assign_by_ref
$smarty->assign('cached_page', 'n');

// Get ~pp~, ~np~ and <pre> out of the way. --rlpowell, 24 May 2004
$preparsed = array();
$noparsed = array();
$parserlib->parse_first($info["data"], $preparsed, $noparsed);

$pdata = $wikilib->get_parse($page, $canBeRefreshed);

$pdata = str_replace('tiki-index.php', 'tiki-index_p.php', $pdata);

if (!isset($_REQUEST['pagenum']))
	$_REQUEST['pagenum'] = 1;

if ( isset( $_REQUEST['pagenum'] ) && $_REQUEST['pagenum'] > 0 ) {
	$pageRenderer->setPageNumber((int) $_REQUEST['pagenum']);
}


include_once('tiki-section_options.php');

$pageRenderer->runSetups();

ask_ticket('index-p');

// Display the Index Template
$smarty->display("tiki-index_p.tpl");
