<?php

// $Header: /cvsroot/tikiwiki/tiki/categorize_list.php,v 1.5 2003-08-07 04:33:56 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/categorize_list.php,v 1.5 2003-08-07 04:33:56 rossta Exp $
include_once ('lib/categories/categlib.php');

if ($feature_categories == 'y') {
	$smarty->assign('cat_categorize', 'n');

	if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
		$smarty->assign('cat_categorize', 'y');
	}

	$categories = $categlib->list_all_categories(0, -1, 'name_asc', '', $cat_type, $cat_objid);

	if (isset($_REQUEST["cat_categories"]) && isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on') {
		for ($i = 0; $i < count($categories["data"]); $i++) {
			if (in_array($categories["data"][$i]["categId"], $_REQUEST["cat_categories"])) {
				$categories["data"][$i]["incat"] = 'y';
			} else {
				$categories["data"][$i]["incat"] = 'n';
			}
		}
	}

	$smarty->assign_by_ref('categories', $categories["data"]);

	// check if this page is categorized
	if ($categlib->is_categorized($cat_type, $cat_objid)) {
		$cat_categorize = 'y';
	} else {
		$cat_categorize = 'n';
	}

	$smarty->assign('cat_categorize', $cat_categorize);
}

?>