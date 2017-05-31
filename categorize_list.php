<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$access = TikiLib::lib('access');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

$userlib = TikiLib::lib('user');
$smarty = TikiLib::lib('smarty');

global $prefs;

$catobjperms = Perms::get(array( 'type' => $cat_type, 'object' => $cat_objid ));

$smarty->assign('mandatory_category', '-1');
if ($prefs['feature_categories'] == 'y' && isset($cat_type) && isset($cat_objid)) {
	$categlib = TikiLib::lib('categ');

	if ( ! isset( $cat_object_exists ) ) {
		// article generator uses 'null' for type and id and puts the category id's in $_REQUEST
		$cat_object_exists = ($cat_objid === 'null') ? false : (bool) $cat_objid;
	}

	if ( $cat_object_exists ) {
		$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	} else {
		$cats = $categlib->get_default_categories();
	}
	
	if ($cat_type == 'wiki page' || $cat_type == 'blog' || $cat_type == 'image gallery' || $cat_type == 'mypage') {
		$ext = ($cat_type == 'wiki page')? 'wiki':str_replace(' ', '_', $cat_type);
		$pref = 'feature_'.$ext.'_mandatory_category';
		if ($prefs[$pref] > 0) {
			$categories = $categlib->getCategories(array('identifier'=>$prefs[$pref], 'type'=>'descendants'));
		} else {
			$categories = $categlib->getCategories();
		}
		$smarty->assign('mandatory_category', $prefs[$pref]);
	} else {
		$categories = $categlib->getCategories();
	}

 	$can = $catobjperms->modify_object_categories;

	$categories = Perms::filter(array('type' => 'category'), 'object', $categories, array( 'object' => 'categId' ), array('view_category'));

	foreach ($categories as &$category) {
		$catperms = Perms::get(array( 'type' => 'category', 'object' => $category['categId'] ));

		if (in_array($category["categId"], $cats)) {
			$category["incat"] = 'y';
			$category['canchange'] = ! $cat_object_exists || ( $can && $catperms->remove_object );
		} else {
			$category["incat"] = 'n';
			$category['canchange'] = $can && $catperms->add_object;
		}
		
		// allow to preselect categories when creating a new article
		// like this: /tiki-edit_article.php?cat_categories[]=1&cat_categorize=on
		if (!$cat_object_exists && isset($_REQUEST["cat_categories"]) && isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
			if (in_array($category["categId"], $_REQUEST["cat_categories"])) {
				$category["incat"] = 'y';
			} else {
				$category["incat"] = 'n';
			}
		}
	}

	$smarty->assign('cat_tree', $categlib->generate_cat_tree($categories));
	
	$smarty->assign_by_ref('categories', $categories);
}

