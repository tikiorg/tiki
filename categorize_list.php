<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
require_once('tiki-setup.php');  
global $prefs, $userlib;
$catobjperms = Perms::get( array( 'type' => $cat_type, 'object' => $cat_objid ) );

$smarty->assign('mandatory_category', '-1');
if ($prefs['feature_categories'] == 'y' && isset($cat_type) && isset($cat_objid)) {
	global $categlib, $user; include_once ('lib/categories/categlib.php');

	if( ! isset( $cat_object_exists ) ) {
		$cat_object_exists = (bool) $cat_objid;
	}

	if( $cat_object_exists ) {
		$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	} else {
		$cats = $categlib->get_default_categories();
	}
	
	if ($prefs['wikiapproval_sync_categories'] == 'y' && !$cats
	 && $cat_type == 'wiki page' && ( $approved = $tikilib->get_approved_page($cat_objid) )
	 && !$tikilib->page_exists($cat_objid) ) {
	 	// to pre-populate categories of original page if this is the first creation of a staging page
		$approvedPageName = $approved;
		$cats = $categlib->get_object_categories($cat_type, $approvedPageName);
		$cats = array_diff($cats,Array($prefs['wikiapproval_approved_category']));		
	}
	
	if ($cat_type == 'wiki page' || $cat_type == 'blog' || $cat_type == 'image gallery' || $cat_type == 'mypage') {
		$ext = ($cat_type == 'wiki page')? 'wiki':str_replace(' ', '_', $cat_type);
		$pref = 'feature_'.$ext.'_mandatory_category';
		if ($prefs[$pref] > 0) {
			$all_categories = $categlib->list_categs($prefs[$pref]);
		} else {
			$all_categories = $categlib->list_categs();
		}
		$smarty->assign('mandatory_category', $prefs[$pref]);
	} else {
		$all_categories = $categlib->list_categs();
	}

	if( ! empty( $all_categories ) ) {
		$categories = Perms::filter( array( 'type' => 'category' ), 'object', $all_categories, array( 'object' => 'categId' ), 'view_category' );
	} else {
		$categories = array();
	}

	$num_categories = count($categories);
 	$can = $catobjperms->modify_object_categories;

	for ($i = 0; $i < $num_categories; $i++) {
		$catperms = Perms::get( array( 'type' => 'category', 'object' => $categories[$i]['categId'] ) );

		if (!empty($cats) && in_array($categories[$i]["categId"], $cats)) {
			$categories[$i]["incat"] = 'y';
			$categories[$i]['canchange'] = ! $cat_object_exists || ( $can && $catperms->remove_object );
		} else {
			$categories[$i]["incat"] = 'n';
			$categories[$i]['canchange'] = $can && $catperms->add_object;
		}
		if (isset($_REQUEST["cat_categories"]) && isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
			if (in_array($categories[$i]["categId"], $_REQUEST["cat_categories"])) {
				$categories[$i]["incat"] = 'y';
				// allow to preselect categories when creating a new article
				// like this: /tiki-edit_article.php?cat_categories[]=1&cat_categorize=on
			} else {
				$categories[$i]["incat"] = 'n';
			}
		}
	}

	$smarty->assign('cat_tree', $categlib->generate_cat_tree($categories));
	
	if (!empty($cats))
		$smarty->assign('catsdump', implode(',',$cats));
	$smarty->assign_by_ref('categories', $categories);
}
