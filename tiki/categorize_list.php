<?php

// $Header: /cvsroot/tikiwiki/tiki/categorize_list.php,v 1.6 2004-02-20 19:22:27 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/categorize_list.php,v 1.6 2004-02-20 19:22:27 mose Exp $
include_once ('lib/categories/categlib.php');

if ($feature_categories == 'y') {
	$smarty->assign('cat_categorize', 'n');

	if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
		$smarty->assign('cat_categorize', 'y');
	}

	$cats = $categlib->get_object_categories($cat_type, $cat_objid);
	$categories = $categlib->list_categs();

	for ($i = 0; $i < count($categories); $i++) {
		if (in_array($categories[$i]["categId"], $cats)) {
			$categories[$i]["incat"] = 'y';
		} else {
			$categories[$i]["incat"] = 'n';
		}
		if (isset($_REQUEST["cat_categories"]) && isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
			if (in_array($categories[$i]["categId"], $_REQUEST["cat_categories"])) {
				$categories[$i]["incat"] = 'y';
			} else {
				$categories[$i]["incat"] = 'n';
			}
		}
	}

	$smarty->assign_by_ref('categories', $categories);

	// check if this page is categorized
	if ($categlib->is_categorized($cat_type, $cat_objid)) {
		$cat_categorize = 'y';
	} else {
		$cat_categorize = 'n';
	}

	$smarty->assign('cat_categorize', $cat_categorize);
}

?>
