<?php 
// $Header: /cvsroot/tikiwiki/tiki/categorize.php,v 1.20 2006-11-07 15:30:20 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

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
	if (isset($section) && $section == 'wiki' && $feature_wiki_mandatory_category > 0)
		$categories = $categlib->list_categs($feature_wiki_mandatory_category);
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
