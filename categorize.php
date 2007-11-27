<?php 
// $Header: /cvsroot/tikiwiki/tiki/categorize.php,v 1.25.2.1 2007-11-27 18:06:49 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
require_once('tiki-setup.php');  

global $prefs;

if ($prefs['feature_categories'] == 'y') {
	global $categlib; include_once('lib/categories/categlib.php');

	$smarty->assign('cat_categorize', 'n');

	if (isset($_REQUEST['import']) and isset($_REQUEST['categories'])) {
		$_REQUEST["cat_categories"] = split(',',$_REQUEST['categories']);
		$_REQUEST["cat_categorize"] = 'on';
	}

	if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
		$smarty->assign('cat_categorize', 'y');
	} else {
		$_REQUEST['cat_categories'] = NULL;
	}
	if ($prefs["feature_wikiapproval"] == 'y' && $cat_type == 'wiki page' && substr($cat_objid, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix']) {		
		if ($prefs['wikiapproval_approved_category'] > 0 && in_array($prefs['wikiapproval_approved_category'], $_REQUEST['cat_categories'])) {
			$_REQUEST['cat_categories'] = array_diff($_REQUEST['cat_categories'],Array($prefs['wikiapproval_approved_category']));
		}
		if ($prefs['wikiapproval_staging_category'] > 0 && !in_array($prefs['wikiapproval_staging_category'], $_REQUEST['cat_categories'])) {	
			$_REQUEST['cat_categories'][] = $prefs['wikiapproval_staging_category'];	
		}
		if ($prefs['wikiapproval_outofsync_category'] > 0 && !in_array($prefs['wikiapproval_outofsync_category'], $_REQUEST['cat_categories'])) {	
			$_REQUEST['cat_categories'][] = $prefs['wikiapproval_outofsync_category'];	
		}
	}
	$categlib->update_object_categories($_REQUEST['cat_categories'], $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href);

	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	if (isset($section) && $section == 'wiki' && $prefs['feature_wiki_mandatory_category'] > 0)
		$categories = $categlib->list_categs($prefs['feature_wiki_mandatory_category']);
	else
		$categories = $categlib->list_categs();
	$num_categories = count($categories);
	for ($iCat = 0; $iCat < $num_categories; $iCat++) {
		if (in_array($categories[$iCat]["categId"], $cats)) {
			$categories[$iCat]["incat"] = 'y';
		} else {
			$categories[$iCat]["incat"] = 'n';
		}
	}
	$smarty->assign_by_ref('categories', $categories["data"]);

}

?>
