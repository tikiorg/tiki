<?php 
// $Header: /cvsroot/tikiwiki/tiki/categorize.php,v 1.15 2004-10-28 01:03:55 chealer Exp $

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

if ($feature_categories == 'y') {
	include_once ('lib/categories/categlib.php');
	
	//handles categories when importing objects
	if (isset($_REQUEST['import']) and isset($_REQUEST['categories'])) {
		$_REQUEST["cat_categories"] = split(',',$_REQUEST['categories']);
		$_REQUEST["cat_categorize"] = 'on';
	}

	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	// Recategorize trying to minimize queries
	if (!isset($_REQUEST["cat_categorize"]) || !isset($_REQUEST["cat_categories"])) {
		if (count($cats) != 0) {
			$categlib->uncategorize_object($cat_type, $cat_objid);
		}
	} else {
		$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
		if (!$catObjectId) {
			// The object is not categorized  
			$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
		}
		foreach ($_REQUEST["cat_categories"] as $cat) {
			//Avoids categorizing in TOP (not sure why) and if already categorized
			if ($cat && !in_array($cat, $cats)) {
				$categlib->categorize($catObjectId, $cat);
			}
		}
		foreach ($cats as $cat) {
			if (!in_array($cat, $_REQUEST["cat_categories"])) {
				$categlib->remove_object_from_category($catObjectId, $cat, 0);
			}
		}
	}
}

?>
