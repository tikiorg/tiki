<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section

require_once ('tiki-setup.php');
include_once ('lib/wiki/histlib.php');
include_once ('lib/wiki/wikilib.php');

$access->check_feature('feature_wiki');

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}

$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission( array('tiki_p_remove', 'tiki_p_edit') );

if ($_REQUEST["version"] <> "last") {
	$smarty->assign_by_ref('version', $_REQUEST["version"]);
	$version = $_REQUEST["version"];
} else {
	$smarty->assign('version', 'last'); //get_strings tra('last version');
	$version = "last";
}
// If the page doesn't exist then display an error
if (!$tikilib->page_exists($page)) {
	$smarty->assign('msg', tra("Page cannot be found"));
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST["remove"])) {
	check_ticket('remove-page');
	if (isset($_REQUEST["all"]) && $_REQUEST["all"] == 'on') {
		$tikilib->remove_all_versions($_REQUEST["page"]);
		header("location: tiki-index.php");
		die;
	} else {
		if ($version == "last") {
			$wikilib->remove_last_version($_REQUEST["page"]);

			if( ( $approved = $tikilib->get_approved_page($page) ) 
				&& $prefs['wikiapproval_outofsync_category'] > 0 ) {
				global $categlib; require_once('lib/categories/categlib.php');

				$approved_page = $histlib->get_page_from_history($approved, 0, true);
				$staging_page = $histlib->get_page_from_history($page, $_REQUEST["version"], true);
				$cat_type='wiki page';		
				$staging_cats = $categlib->get_object_categories($cat_type, $page);
				$s_cat_desc = ($prefs['feature_wiki_description'] == 'y') ? substr($staging_info["description"],0,200) : '';
				$s_cat_objid = $page;
				$s_cat_name = $page;
				$s_cat_href="tiki-index.php?page=".urlencode($s_cat_objid);

				//Instead of firing up diff, just check if the pages share the same exact data, drop the staging
				//copy out of the review category if so
				if ( $approved_page["data"] != $staging_page["data"] ) //compare these only once
					$pages_diff = true;
				if ( in_array($prefs['wikiapproval_outofsync_category'], $staging_cats) )
					$in_staging_cat = true;

				if ( !$pages_diff && $in_staging_cat ) {
					$staging_cats = array_diff($staging_cats,Array($prefs['wikiapproval_outofsync_category']));
					$categlib->update_object_categories($staging_cats, $s_cat_objid, $cat_type, $s_cat_desc, $s_cat_name, $s_cat_href);	
				} elseif ( $pages_diff && !$in_staging_cat ) {
					$staging_cats[] = $prefs['wikiapproval_outofsync_category'];
					$categlib->update_object_categories($staging_cats, $s_cat_objid, $cat_type, $s_cat_desc, $s_cat_name, $s_cat_href);	
				}
			}
		} else {
			$histlib->remove_version($_REQUEST['page'], $_REQUEST['version'], $_REQUEST['historyId']);
		}
		header("location: tiki-index.php");
		die;
	}
}
ask_ticket('remove-page');
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-removepage.tpl');
$smarty->display("tiki.tpl");
