<?php 
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $prefs;
$catobjperms = Perms::get( array( 'type' => $cat_type, 'object' => $cat_objid ) );

if ($prefs['feature_categories'] == 'y' && $catobjperms->modify_object_categories ) {
	global $categlib; include_once('lib/categories/categlib.php');

	if (isset($_REQUEST['import']) and isset($_REQUEST['categories'])) {
		$_REQUEST["cat_categories"] = explode(',',$_REQUEST['categories']);
		$_REQUEST["cat_categorize"] = 'on';
	}

	if ( !isset($_REQUEST["cat_categorize"]) || $_REQUEST["cat_categorize"] != 'on' || (isset($_REQUEST["cat_clearall"]) && $_REQUEST["cat_clearall"] == 'on') ) {
		$_REQUEST['cat_categories'] = NULL;
	}
	$categlib->update_object_categories(isset($_REQUEST['cat_categories'])?$_REQUEST['cat_categories']:'', $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href, $_REQUEST['cat_managed']);

	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	if (isset($section) && $section == 'wiki' && $prefs['feature_wiki_mandatory_category'] > 0)
		$categories = $categlib->list_categs($prefs['feature_wiki_mandatory_category']);
	else
		$categories = $categlib->list_categs();

	$categories = Perms::filter( array( 'type' => 'category' ), 'object', $categories, array( 'object' => 'categId' ), 'view_category' );

	$num_categories = count($categories);
 	$can = $catobjperms->modify_object_categories;

	for ($iCat = 0; $iCat < $num_categories; $iCat++) {
		$catperms = Perms::get( array( 'type' => 'category', 'object' => $categories[$iCat]['categId'] ) );

		if (in_array($categories[$iCat]["categId"], $cats)) {
			$categories[$iCat]["incat"] = 'y';
			$categories[$iCat]['canchange'] = ($can && $catperms->remove_object) || isset($cat_object_exists) && ! $cat_object_exists;
		} else {
			$categories[$iCat]["incat"] = 'n';
			$categories[$iCat]['canchange'] = $can && $catperms->add_object;
		}
	}
	$smarty->assign_by_ref('categories', $categories["data"]);

}
