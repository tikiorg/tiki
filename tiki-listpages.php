<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-listpages.php,v 1.17 2004-07-16 18:46:05 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($feature_listPages != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_listPages");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view pages"));

	$smarty->display("error.tpl");
	die;
}

global $tiki_p_admin;
global $feature_categories;
global $tiki_p_admin_categories;

/* mass-remove: 
   the checkboxes are sent as the array $_REQUEST["checked[]"], values are the wiki-PageNames, 
   e.g. $_REQUEST["checked"][3]="HomePage"
   $_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
   we look if any page's checkbox is on and if remove_pages is selected.
   then we check permission to delete pages.
   if so, we call histlib's method remove_all_versions for all the checked pages.
*/
if (!empty($_REQUEST["submit_mult"]) && !empty($_REQUEST["checked"])) {
	if ($_REQUEST["submit_mult"] == "remove_pages") {
	check_ticket('list-pages');

	// Now check permissions to remove the selected pages
	if ($tiki_p_remove != 'y' && $tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot remove pages"));

		$smarty->display("error.tpl");
		die;
	}

	// permissions ok: go!
	include_once ('lib/wiki/histlib.php');

	foreach ($_REQUEST["checked"] as $deletepage) {
		$histlib->remove_all_versions($deletepage);
	}
	} elseif ($_REQUEST['submit_mult'] == 'categorize') {
		$categorize_mode = TRUE;
		$smarty->assign('categorize_mode', 'y');
		include_once ('lib/categories/categlib.php');
		$categories = $categlib->list_categs();
		$smarty->assign('categories', $categories);
	}
}
// to-do: place the following code in categorize.php?
// mass categorization: add to categories
elseif (!empty($_REQUEST['categorization']) && $_REQUEST['categorization'] == 'add') {
	
	if ($tiki_p_admin_categories != 'y' && $tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("Permission denied: you cannot administer categories"));
		$smarty->display("error.tpl");
		die;
	}
	
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
	$cat_type='wiki page';
	if (!empty($_REQUEST["cat_categories"]) && !empty($_REQUEST["checked"])) {
		foreach ($_REQUEST['checked'] as $page) {
			$pageinfo = $tikilib->get_page_info($page);
			$cat_objid = $pageinfo['pageName'];
			$cat_desc = ($feature_wiki_description == 'y') ? $pageinfo['description'] : '';
			$cat_name = $pageinfo['pageName'];
			$cat_href="tiki-index.php?page=".$cat_objid;
			foreach ($_REQUEST["cat_categories"] as $cat_acat) {
				$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
				if (!$catObjectId) {
					// The object is not cateorized  
					$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
				}
				$categlib->categorize($catObjectId, $cat_acat);
			}
		}
	}
}
// mass categorization: remove from categories
elseif (!empty($_REQUEST['categorization']) && $_REQUEST['categorization'] == 'remove') {
	
	if ($tiki_p_admin_categories != 'y' && $tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("Permission denied: you cannot administer categories"));
		$smarty->display("error.tpl");
		die;
	}
	
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
	$cat_type='wiki page';
	if (!empty($_REQUEST["cat_categories"]) && !empty($_REQUEST["checked"])) {
		foreach ($_REQUEST['checked'] as $page) {
			$pageinfo = $tikilib->get_page_info($page);
			$cat_objid = $pageinfo['pageName'];
			foreach ($_REQUEST["cat_categories"] as $cat_acat) {
				$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
				if ($catObjectId) {
					$categlib->remove_object_from_category($catObjectId, $cat_acat);
				}
			}
		}
	}
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'pageName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

if (!empty($_REQUEST['max_records'])) {
	$maxRecords = $_REQUEST['max_records'];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_pages($offset, $maxRecords, $sort_mode, $find);

if (!empty($categorize_mode)) {
	$arraylen = count($listpages['data']);
	for ($i=0; $i<$arraylen; $i++) {
		if (in_array($listpages['data'][$i]['pageName'], $_REQUEST["checked"])) {
			$listpages['data'][$i]['checked'] = 'y';
		}
	}
}

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
$smarty->assign('maxRecords', $maxRecords);

if ($listpages["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign('tiki_p_admin',$tiki_p_admin);
$smarty->assign('feature_categories', $feature_categories);
$smarty->assign('tiki_p_admin_categories', $tiki_p_admin_categories);

$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
ask_ticket('list-pages');

// Display the template
$smarty->assign('mid', 'tiki-listpages.tpl');
$smarty->display("tiki.tpl");

?>
