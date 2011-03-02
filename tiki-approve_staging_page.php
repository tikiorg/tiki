<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/wiki/wikilib.php');
include_once('lib/wiki/histlib.php');
include_once('lib/categories/categlib.php');
include_once ('lib/notifications/notificationlib.php');
include_once ('lib/notifications/notificationemaillib.php');
if ($prefs['feature_multilingual'] == 'y') {
	include_once("lib/multilingual/multilinguallib.php");
}

$access->check_feature(array('feature_wiki', 'feature_wikiapproval'));

// Get the page from the request var or fail
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
} else {
	$page = urldecode($_REQUEST["page"]);

	$smarty->assign_by_ref('page', $page);
}

if ($tikilib->get_staging_page($page)) {
	$smarty->assign('msg', tra("This function is only for staging pages"));

	$smarty->display("error.tpl");
	die;
}

// first check perms for category set as the approved category (this could be necessary in some setups even though page perms are checked below)
if ($prefs['wikiapproval_approved_category'] == 0 && $tiki_p_edit != 'y' || $prefs['wikiapproval_approved_category'] > 0 && !$categlib->has_edit_permission($user, $prefs['wikiapproval_approved_category'])) {
	$smarty->assign('msg', tra("You do not have permission to approve staging pages"));

	$smarty->display("error.tpl");
	die;	
}

// switch page to be edited to approved page and store info of old page here. 
$staging_page = $page;
$page = $tikilib->get_approved_page($page);

// If either page doesn't exist then display an error
if (!$tikilib->page_exists($staging_page)) { 
	$smarty->assign('msg', tra("Staging page cannot be found"));

	$smarty->display("error.tpl");
	die;
}

// get staging page info

$staging_info = $tikilib->get_page_info($staging_page);

// If approved page does not exist, then create it first
if ($page && !$tikilib->page_exists($page)) {
	$tikilib->create_page($page, 0, '', $staging_info['lastModif'] - 1, 'Staging creation');
}

// Check approved page edit permissions
$info = $tikilib->get_page_info($page);
$tikilib->get_perm_object($page, 'wiki page', $info, true);
$access->check_permission('tiki_p_edit');

if ( $staging_info['lastModif'] < $info['lastModif'] ) { 
	$smarty->assign('msg', tra("Approved page was last saved after most recent staging edit"));

	$smarty->display("error.tpl");
	die;
}

// update approved page contents
// multiple commits are needed to make sure contributor list and history are synced

$begin_version = $histlib->get_version_by_time($staging_page, $info['lastModif'], 'after');
$commitversion = $histlib->get_page_latest_version($page) + 1;
$lastversion = $histlib->get_page_latest_version($staging_page);
$finalversion = $lastversion + 1;
if ($begin_version > 0) {
	for ($v = $begin_version; $v <= $lastversion; $v++) {
		$version_info = $histlib->get_version($staging_page, $v);
		$history = array();
		if ($version_info) {
			$tikilib->update_page($page, $version_info["data"], $version_info["comment"] . " [" . tra('approved by ').$user . "]", $version_info["user"], $version_info["ip"], $version_info["description"], false, $staging_info["lang"], $staging_info["is_html"]);
			$commitversion++;
			$history[] = $version_info;
			if ($prefs['feature_multilingual'] == 'y') {
				// update translation bits
				$flags = $multilinguallib->get_page_bit_flags( $staging_info['page_id'], $v );				
				$multilinguallib->createTranslationBit( 'wiki page', $info['page_id'], $commitversion, $flags );
			}			
		} 		
	}
}
// finally approve current staging version
$tikilib->update_page($page, $staging_info["data"], $staging_info["comment"] . " [" . tra('approved by ').$user . "]", $staging_info["user"], $staging_info["ip"], $staging_info["description"], false, $staging_info["lang"], $staging_info["is_html"]);

$commitversion++;
if ($prefs['feature_multilingual'] == 'y') {
	// update translation bits
	include_once("lib/multilingual/multilinguallib.php");
	$flags = $multilinguallib->get_page_bit_flags( $staging_info['page_id'], $finalversion );
	$multilinguallib->createTranslationBit( 'wiki page', $info['page_id'], $commitversion, $flags );
}
$smarty->assign('history', $history);
$smarty->assign('staging_info', $staging_info);
$smarty->assign('staging_page', $staging_page);

// update translation set lang for approved pages
if ($prefs['feature_multilingual'] == 'y') {
	include_once("lib/multilingual/multilinguallib.php");
	if (isset($info["lang"]) && $info['lang'] != $staging_info['lang'])
		$multilinguallib->updatePageLang('wiki page', $info['page_id'], $staging_info['lang'], true);
}

// make sure approved page has approved category set, and sync categories
if ($prefs['feature_categories'] == 'y') {	

	$cat_type='wiki page';
	$cat_objid = $page;
	$cat_desc = ($prefs['feature_wiki_description'] == 'y') ? substr($info["description"], 0, 200) : '';
	$cat_name = $page;
	$cat_href="tiki-index.php?page=".urlencode($cat_objid);
	$s_cat_desc = ($prefs['feature_wiki_description'] == 'y') ? substr($staging_info["description"], 0, 200) : '';
	$s_cat_objid = $staging_page;
	$s_cat_name = $staging_page;
	$s_cat_href="tiki-index.php?page=".urlencode($s_cat_objid);

	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	$staging_cats = $categlib->get_object_categories($cat_type, $staging_page);
	
	if ($prefs['wikiapproval_sync_categories'] == 'y') {		
		$cats = $staging_cats;	
	}
	if ($prefs['wikiapproval_staging_category'] > 0 && in_array($prefs['wikiapproval_staging_category'], $cats)) {	
		$cats = array_diff($cats, Array($prefs['wikiapproval_staging_category']));	
	}
	if ($prefs['wikiapproval_approved_category'] > 0 && !in_array($prefs['wikiapproval_approved_category'], $cats)) {
		$cats[] = $prefs['wikiapproval_approved_category'];	
	}
	if ($prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $cats)) {	
		$cats = array_diff($cats, Array($prefs['wikiapproval_outofsync_category']));	
	}

	$categlib->update_object_categories($cats, $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href);
	
	// now to remove out of sync from staging page
	if ($prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $staging_cats)) {
		$staging_cats = array_diff($staging_cats, Array($prefs['wikiapproval_outofsync_category']));
		$categlib->update_object_categories($staging_cats, $s_cat_objid, $cat_type, $s_cat_desc, $s_cat_name, $s_cat_href);	
	}
}

// update approved page tags
if ($prefs['feature_freetags'] == 'y' && ($prefs['wikiapproval_update_freetags'] == 'y' || $prefs['wikiapproval_combine_freetags'] == 'y')) {
	if (!is_object($freetaglib)) include_once('lib/freetag/freetaglib.php');
	
	$tags = $freetaglib->get_tags_on_object($staging_page, 'wiki page');
	$taglist = '';		
	foreach($tags['data'] as $tag) {
    	$taglist .= $tag['tag'] . ' ';
	}
	
	$freetaglib->update_tags($user, $page, 'wiki page', $taglist);
}

//delete staging page
if ($prefs['wikiapproval_delete_staging'] == 'y') {
	$tikilib->remove_all_versions($staging_page);
}

// OK, done

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-approve_staging_page.tpl');
$smarty->display("tiki.tpl");
