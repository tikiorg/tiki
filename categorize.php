<?php 
// $Header: /cvsroot/tikiwiki/tiki/categorize.php,v 1.13 2004-06-15 21:22:55 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== FALSE) {
  //smarty is not there - we need setup
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra("This script cannot be called directly"));
  $smarty->display("error.tpl");
  die;
}

global $feature_categories;

if ($feature_categories == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
	$smarty->assign('cat_categorize', 'n');

	if (isset($_REQUEST['import']) and isset($_REQUEST['categories'])) {
		$_REQUEST["cat_categories"] = split(',',$_REQUEST['categories']);
		$_REQUEST["cat_categorize"] = 'on';
	}

	if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
		$smarty->assign('cat_categorize', 'y');
		$categlib->uncategorize_object($cat_type, $cat_objid);

		if (isset($_REQUEST["cat_categories"])) {
			foreach ($_REQUEST["cat_categories"] as $cat_acat) {
				if ($cat_acat) {
					$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);

					if (!$catObjectId) {
						// The object is not cateorized  
						$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
					}

					$categlib->categorize($catObjectId, $cat_acat);
				}
			}
		}
	} else {
		$categlib->uncategorize_object($cat_type, $cat_objid);
	}
	
	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	$categories = $categlib->list_categs();
	for ($i = 0; $i < count($categories); $i++) {
		if (in_array($categories[$i]["categId"], $cats)) {
			$categories[$i]["incat"] = 'y';
		} else {
			$categories[$i]["incat"] = 'n';
		}
	}
	$smarty->assign_by_ref('categories', $categories["data"]);
}

?>
