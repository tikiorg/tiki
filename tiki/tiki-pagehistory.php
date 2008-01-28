<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pagehistory.php,v 1.45.2.5 2008-01-28 19:03:04 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/wiki/histlib.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_wiki');
	$smarty->display('error.tpl');
	die;
}
if (!isset($_REQUEST["source"])) {
	if ($prefs['feature_history'] != 'y') {
		$smarty->assign('msg', tra('This feature is disabled').': feature_history');
		$smarty->display('error.tpl');
		die;
	}
} else {
	if ($prefs['feature_source'] != 'y') {
		$smarty->assign('msg', tra('This feature is disabled').': feature_source');
		$smarty->display('error.tpl');
		die;
	}
}

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

include_once ("tiki-pagesetup.php");

// Now check permissions to access this page
if (!isset($_REQUEST["source"])) {
    if (!$tikilib->user_has_perm_on_object($user, $_REQUEST["page"],'wiki page','tiki_p_view')  || (isset($tiki_p_wiki_view_history) && $tiki_p_wiki_view_history != 'y') ) {
	$smarty->assign('msg', tra("Permission denied you cannot browse this page history"));

	$smarty->display("error.tpl");
	die;
    }
} else {
    if (!$tikilib->user_has_perm_on_object($user, $_REQUEST["page"],'wiki page','tiki_p_view')  || (isset($tiki_p_wiki_view_source) && $tiki_p_wiki_view_source != 'y') ) {
	$smarty->assign('msg', tra("Permission denied you cannot view the source of this page"));

	$smarty->display("error.tpl");
	die;
    }
}

$info = $tikilib->get_page_info($page);
$smarty->assign_by_ref('info', $info);

// If the page doesn't exist then display an error
//check_page_exits($page);

if (isset($_REQUEST["delete"]) && isset($_REQUEST["hist"]) && $info["flag"] != 'L') {
	check_ticket('page-history');
	foreach (array_keys($_REQUEST["hist"])as $version) {
		$histlib->remove_version($_REQUEST["page"], $version);
	}
}

if ($prefs['feature_contribution'] == 'y') {
	global $contributionlib; include_once('lib/contribution/contributionlib.php');
	$contributions = $contributionlib->get_assigned_contributions($page, 'wiki page');
	$smarty->assign_by_ref('contributions', $contributions);
	if ($prefs['feature_contributor_wiki'] == 'y') {
		global $logslib; include_once('lib/logs/logslib.php');
		$contributors = $logslib->get_wiki_contributors($info);
		$smarty->assign_by_ref('contributors', $contributors);
	}
}

if (isset($_REQUEST['oldver'])) { $oldver=(int)$_REQUEST["oldver"]; } else $oldver=0;
if (isset($_REQUEST['newver'])) { $newver=(int)$_REQUEST["newver"]; } else $newver=0;
if (isset($_REQUEST['source'])) $source=$_REQUEST['source'];
if (isset($_REQUEST['version'])) $rversion=$_REQUEST['version'];
if (isset($_REQUEST['preview'])) $preview=$_REQUEST["preview"];

$smarty->assign('source', false);
if (isset($source)) {
	if ($source == '' && isset($rversion)) {
		$source = $rversion;
	}
	if ($source == $info["version"] || $source == 0 ) {
		if ($info['is_html'] == 1 ) {
			$smarty->assign('sourced', $info["data"]);
		} else {
			$smarty->assign('sourced', nl2br($info["data"]));
		}
		$smarty->assign('source', $info['version']);

	}
	else {
		$version = $histlib->get_version($page, $source);
		if ($version) {
			if ($info['is_html'] == 1 ) {
				$smarty->assign('sourced', $info["data"]);
			} else {
				$smarty->assign('sourced', nl2br($version["data"]));
			}
			$smarty->assign('source', $source);
		}
	}
	if ($source == 0) {
		$smarty->assign('noHistory', true);
	}
}

$smarty->assign('preview', false);
if (isset($preview)) {
	if ($preview == '' && isset($rversion)) {
		$preview = $rversion;
	}
	if ($preview == $info["version"] || $preview == 0 ) {
		$previewd = $tikilib->parse_data($info["data"]);
		$smarty->assign_by_ref('previewd', $previewd);
		$smarty->assign('preview', $info['version']);
	}
	else {
		$version = $histlib->get_version($page, $preview);
		if ($version) {
			$previewd = $tikilib->parse_data($version["data"]);
			$smarty->assign_by_ref('previewd', $previewd);
			$smarty->assign('preview', $preview);
		}
	}
	if ($preview == 0) {
		$smarty->assign('noHistory', true);
	}
}

// fetch page history, but omit the actual page content (to save memory)
$history = $histlib->get_page_history($page,false);
$smarty->assign_by_ref('history', $history);

if ($prefs['feature_multilingual'] == 'y' && isset($_REQUEST['show_translation_history'])) {
	include_once("lib/multilingual/multilinguallib.php");
	$smarty->assign( 'show_translation_history', 1 );

	$sources = $multilinguallib->getSourceHistory($info['page_id']);
	$targets = $multilinguallib->getTargetHistory($info['page_id']);
} else {
	$sources = array();
	$targets = array();
}

$smarty->assign_by_ref( 'translation_sources', $sources );
$smarty->assign_by_ref( 'translation_targets', $targets );

if (isset($_REQUEST["diff2"])) { // previous compatibility
	if ($_REQUEST["diff2"] == '' && isset($rversion)) {
		$_REQUEST["diff2"] = $rversion;
	}
	$_REQUEST["compare"] = "y";
	$oldver = (int)$_REQUEST["diff2"];
}
if (!isset($newver)) {
	$newver = 0;
}

if ($prefs['feature_multilingual'] == 'y') {
	include_once("lib/multilingual/multilinguallib.php");
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref( 'languages', $languages );

	if (isset($_REQUEST["update_translation"])) {

		// Update translation button clicked. Forward request to edit page of translation.
		if (isset($_REQUEST['tra_lang'])) {
			$target = $_REQUEST['tra_lang'];
		} else {
			die( 'Invalid call to this page. Specify tra_lang' );
		}

		// Find appropriate translation page
		$langs = $multilinguallib->getTranslations( 'wiki page', $info['page_id'], $info['pageName'], true );
		$pageName = '';
		foreach ($langs as $pageInfo)
			if ($target == (string)$pageInfo['lang']) {
				$pageName = $pageInfo['objName'];
			}

		// Build URI / Redirect
		$diff_style = isset( $_REQUEST['diff_style'] ) ? rawurlencode( $_REQUEST['diff_style'] ) : '';
		$comment = rawurlencode( "Updating from $page at version {$info['version']}" );

		if( $newver == 0 ) {
			$newver = $info['version'];
		}

		if( $pageName ) {
			$uri = "tiki-editpage.php?page=$pageName&source_page=$page&diff_style=$diff_style&oldver=$oldver&newver=$newver&comment=$comment";
		} else {
			$uri = "tiki-edit_translation.php?page=$page";
		}

		header( "Location: $uri" );
		exit;
	}
}

if (isset($_REQUEST["compare"]))
	histlib_helper_setup_diff( $page, $oldver, $newver );
else
	$smarty->assign('diff_style', '');

if($info["flag"] == 'L')
    $smarty->assign('lock',true);  
else
    $smarty->assign('lock',false);
$smarty->assign('page_user',$info['user']);

ask_ticket('page-history');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-pagehistory.tpl');
$smarty->display("tiki.tpl");

?>
