<?php 

// $Header: /cvsroot/tikiwiki/tiki/categorize.php,v 1.7 2004-01-28 03:50:48 musus Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('lib/categories/categlib.php');

if ($feature_categories == 'y') {
	$smarty->assign('cat_categorize', 'n');

	if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
		$smarty->assign('cat_categorize', 'y');
	}

	if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
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

	$categories = $categlib->list_all_categories(0, -1, 'name_asc', '', $cat_type, $cat_objid);
	$smarty->assign_by_ref('categories', $categories["data"]);
}

?>

