<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once('tiki-setup.php');
include_once('lib/structures/structlib.php');

include_once('lib/wiki/wikilib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
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

$tikilib->get_perm_object( $page, 'wiki page', $info);
$access->check_permission('tiki_p_view');

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
if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
	$tikilib->add_hit($page);
}

// Get page data
$info = $tikilib->get_page_info($page);

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
$tikilib->parse_first( $info["data"], $preparsed, $noparsed );

$pdata = $wikilib->get_parse($page, $canBeRefreshed);

$pdata = str_replace('tiki-index.php', 'tiki-index_p.php', $pdata);

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

$smarty->assign_by_ref('lastModif', $info["lastModif"]);

if (empty($info["user"])) {
	$info["user"] = 'anonymous';
}

$smarty->assign_by_ref('lastUser', $info["user"]);
$smarty->assign_by_ref('description', $info["description"]);

include_once('tiki-section_options.php');

$smarty->assign('wiki_extras', 'y');
$smarty->assign('structure', 'n');

/* broken since nov 18 2003
if ($structlib->page_is_in_structure($page)) {
	$smarty->assign('structure', 'y');
	if (isset($_REQUEST["structID"]))	{
		$prev_next_pages = $structlib->get_prev_next_pages($page, $_REQUEST["structID"]);
	}
	else {
		$prev_next_pages = $structlib->get_prev_next_pages($page);
	} 	
	$smarty->assign('struct_prev_next', $prev_next_pages);
}
*/

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'wiki page';

	$cat_objid = $_REQUEST["page"];
	include('tiki-tc.php');
}
ask_ticket('index-p');

// Display the Index Template
$smarty->assign('dblclickedit', 'y');
$smarty->display("tiki-index_p.tpl");
