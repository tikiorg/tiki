<?php

// $Header: /cvsroot/tikiwiki/tiki/categorize_list.php,v 1.12 2005-01-01 00:16:15 damosoft Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== FALSE) {
  //smarty is not there - we need setup
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra("This script cannot be called directly"));
  $smarty->display("error.tpl");
  die;
}

if ($feature_categories == 'y') {
	include_once ('lib/categories/categlib.php');
	$categories = $categlib->list_categs();
	if (isset($_REQUEST["preview"])) {
		$cats = isset($_REQUEST["cat_categories"]) ? $_REQUEST["cat_categories"] : array();
	} else {
		$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	}

	for ($i = 0; $i < count($categories); $i++) {
		if (in_array($categories[$i]["categId"], $cats)) {
			$categories[$i]["incat"] = 'y';
		} else {
			$categories[$i]["incat"] = 'n';
		}
	}
	$smarty->assign_by_ref('categories', $categories);
	if (isset($_REQUEST["preview"])) {
		$smarty->assign('cat_categorize', (isset($_REQUEST["cat_categories"]) && isset($_REQUEST["cat_categorize"])) ? 'y' : 'n');
	} else {
		$smarty->assign('cat_categorize', count($cats) != 0 ? 'y' : 'n');
	}
}

?>
