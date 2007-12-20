<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');
include_once('lib/wiki/histlib.php');
include_once('lib/categories/categlib.php');

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_wikiapproval'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wikiapproval");

	$smarty->display("error.tpl");
	die;
}

// Get the page from the request var or fail
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];

	$smarty->assign_by_ref('page', $page);
}

if (substr($page, 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']) {
	$smarty->assign('msg', tra("This function is only for staging pages"));

	$smarty->display("error.tpl");
	die;
}

// first check perms for category set as the approved category (this could be necessary in some setups even though page perms are checked below)
if ($prefs['wikiapproval_approved_category'] == 0 && $tiki_p_edit != 'y' || $prefs['wikiapproval_approved_category'] > 0 && !$categlib->has_edit_permission($user, $prefs['wikiapproval_approved_category'])) {
	$smarty->assign('msg', tra("Permission denied you cannot approve staging pages"));

	$smarty->display("error.tpl");
	die;	
}

// switch page to be edited to approved page and store info of old page here. 
$staging_page = $page;
$page = substr($staging_page, strlen($prefs['wikiapproval_prefix']));

// If either page doesn't exist then display an error
if (!$tikilib->page_exists($page) || !$tikilib->page_exists($staging_page)) { 
	$smarty->assign('msg', tra("Either staging or approved page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

// Check approved page edit permissions
$info = $tikilib->get_page_info($page);
$tikilib->get_perm_object($page, 'wiki page', $info, true);
if ($tiki_p_edit != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
	$smarty->display("error.tpl");
	die;
}

// get staging page info

$staging_info = $tikilib->get_page_info($staging_page);

if ( $staging_info['lastModif'] < $info['lastModif'] ) { 
	$smarty->assign('msg', tra("Approved page was last saved after most recent staging edit"));

	$smarty->display("error.tpl");
	die;
}

// update approved page contents
// multiple commits are needed to make sure contributor list and history are synced

$begin_version = $histlib->get_version_by_time($staging_page, $info['lastModif'], 'after');
if ($begin_version > 0) {
	$lastversion = $histlib->get_page_latest_version($staging_page);
	for ($v = $begin_version; $v <= $lastversion; $v++) {
		$version_info = $histlib->get_version($staging_page, $v);
		$history = array();
		if ($version_info) {
			$tikilib->update_page($page, $version_info["data"], tra('approved by ').$user, $version_info["user"], $version_info["ip"], $version_info["description"], false, $staging_info["lang"], $staging_info["is_html"]);
			$history[] = $version_info;
		} 
	}
}
// finally approve current staging version
$tikilib->update_page($page, $staging_info["data"], tra('approved by ').$user, $staging_info["user"], $staging_info["ip"], $staging_info["description"], false, $staging_info["lang"], $staging_info["is_html"]);
$smarty->assign('history', $history);
$smarty->assign('staging_info', $staging_info);
$smarty->assign('staging_page', $staging_page);

// make sure approved page has approved category set, and sync categories
if ($prefs['feature_categories'] == 'y') {	

	$cat_type='wiki page';
	$cat_objid = $page;
	$cat_desc = ($prefs['feature_wiki_description'] == 'y') ? substr($info["description"],0,200) : '';
	$cat_name = $page;
	$cat_href="tiki-index.php?page=".urlencode($cat_objid);
	$s_cat_desc = ($prefs['feature_wiki_description'] == 'y') ? substr($staging_info["description"],0,200) : '';
	$s_cat_objid = $staging_page;
	$s_cat_name = $staging_page;
	$s_cat_href="tiki-index.php?page=".urlencode($s_cat_objid);

	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	$staging_cats = $categlib->get_object_categories($cat_type, $staging_page);
	
	if ($prefs['wikiapproval_sync_categories'] == 'y') {		
		$cats = $staging_cats;	
	}
	if ($prefs['wikiapproval_staging_category'] > 0 && in_array($prefs['wikiapproval_staging_category'], $cats)) {	
		$cats = array_diff($cats,Array($prefs['wikiapproval_staging_category']));	
	}
	if ($prefs['wikiapproval_approved_category'] > 0 && !in_array($prefs['wikiapproval_approved_category'], $cats)) {
		$cats[] = $prefs['wikiapproval_approved_category'];	
	}
	if ($prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $cats)) {	
		$cats = array_diff($cats,Array($prefs['wikiapproval_outofsync_category']));	
	}
	$categlib->update_object_categories($cats, $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href);
	
	// now to remove out of sync from staging page
	if ($prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $staging_cats)) {
		$staging_cats = array_diff($staging_cats,Array($prefs['wikiapproval_outofsync_category']));
		$categlib->update_object_categories($staging_cats, $s_cat_objid, $cat_type, $s_cat_desc, $s_cat_name, $s_cat_href);	
	}
}

// update approved page tags
if ($prefs['feature_freetags'] == 'y' && ($prefs['wikiapproval_update_freetags'] == 'y' || $prefs['wikiapproval_combine_freetags'] == 'y')) {
	if (!is_object($freetaglib)) include_once('lib/freetag/freetaglib.php');
	
	$tags = $freetaglib->get_tags_on_object($staging_page, 'wiki page');
	$taglist = '';		
	for ($i=0; $i<sizeof($tags['data']); $i++) {
    	$taglist .= $tags['data'][$i]['tag'] . ' ';
	}
	
	$freetaglib->update_tags($user, $page, 'wiki page', $taglist);
}

// OK, done

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-approve_staging_page.tpl');
$smarty->display("tiki.tpl");

?>