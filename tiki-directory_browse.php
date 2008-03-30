<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-directory_browse.php,v 1.16 2007-10-12 07:55:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/directory/dirlib.php');

if ($prefs['feature_directory'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_directory");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_directory != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

// If no parent category then the parent category is 0
if (!isset($_REQUEST["parent"]))
	$_REQUEST["parent"] = 0;

$smarty->assign('parent', $_REQUEST["parent"]);
$all = 0;

if ($_REQUEST["parent"] == 0) {
	$parent_name = 'Top';

	$all = 1;
} else {
	$parent_info = $dirlib->dir_get_category($_REQUEST['parent']);
	$smarty->assign('parent_info', $parent_info);
	$parent_name = $parent_info['name'];
}

$smarty->assign('parent_name', $parent_name);
$smarty->assign('parent', $_REQUEST['parent']);
$smarty->assign('addtocat', $_REQUEST['parent']);

$dirlib->dir_add_category_hit($_REQUEST['parent']);

// Now get the path to the parent category
$path = $dirlib->dir_get_category_path_browse($_REQUEST["parent"]);
$smarty->assign_by_ref('path', $path);
$crumbs[] = new Breadcrumb('Directory',
                            '',
                            'tiki-directory_browse.php?parent=0',
                            'Directory+User',
                            'How to use Directory');
// Now append the path to the parent category
array_splice($crumbs, count($crumbs), 0, $dirlib->dir_build_breadcrumb_trail($_REQUEST["parent"]));
$smarty->assign('trail', $crumbs);     
$headtitle = breadcrumb_buildHeadTitle($crumbs);
$smarty->assign_by_ref('headtitle', $headtitle); 


// Now get the sub categories from this parent category
$categs = $dirlib->dir_list_categories($_REQUEST['parent'], 0, -1, 'name_asc', '');

$temp_max = count($categs['data']);
for ($i = 0; $i < $temp_max; $i++) {
	$categs['data'][$i]['subcats'] = array();

	if ($categs['data'][$i]['childrenType'] == 'c' && $categs['data'][$i]['viewableChildren'] > 0) {
		// Generate the subcategories with most hist as the subcategories to show.
		$subcats
			= $dirlib->dir_list_categories($categs['data'][$i]['categId'], 0, $categs['data'][$i]['viewableChildren'], 'hits_desc',
			'');

		$categs['data'][$i]['subcats'] = $subcats['data'];
	}

	if ($categs['data'][$i]['childrenType'] == 'd' && $categs['data'][$i]['viewableChildren'] > 0) {
		// Generate the subcategories with most hist as the subcategories to show.
		$categs['data'][$i]['subcats'] = array(array("name" => $categs['data'][$i]['description']));
	}

	if ($categs['data'][$i]['childrenType'] == 'r' && $categs['data'][$i]['viewableChildren'] > 0) {
		$categs['data'][$i]['subcats']
			= $dirlib->get_random_subcats($categs['data'][$i]['categId'], $categs['data'][$i]['viewableChildren']);
	}
}

$smarty->assign_by_ref('categs', $categs['data']);
$smarty->assign('cols', $prefs['directory_columns']);

// Now if needed get sites
$categ_info = $dirlib->dir_get_category($_REQUEST['parent']);
$smarty->assign_by_ref('categ_info', $categ_info);

if ($user) {
	if (in_array($categ_info['editorGroup'], $userlib->get_user_groups($user))) {
		$tiki_p_admin_directory_sites = 'y';

		$smarty->assign('tiki_p_admin_directory_sites', 'y');
	}
}

if ($categ_info['allowSites'] == 'y') {
	if (!isset($_REQUEST["sort_mode"])) {
		$sort_mode = 'hits_desc';
	} else {
		$sort_mode = $_REQUEST["sort_mode"];
	}

	if (!isset($_REQUEST["offset"])) {
		$offset = 0;
	} else {
		$offset = $_REQUEST["offset"];
	}

	if (isset($_REQUEST["find"])) {
		$find = $_REQUEST["find"];
	} else {
		$find = '';
	}

	$smarty->assign_by_ref('offset', $offset);
	$smarty->assign_by_ref('sort_mode', $sort_mode);
	$smarty->assign('find', $find);
	$items = $dirlib->dir_list_sites($_REQUEST['parent'], $offset, $prefs['directory_links_per_page'], $sort_mode, '', 'y');
	$cant_pages = ceil($items["cant"] / $prefs['directory_links_per_page']);
	$smarty->assign_by_ref('cant_pages', $cant_pages);
	$smarty->assign('actual_page', 1 + ($offset / $prefs['directory_links_per_page']));

	if ($items["cant"] > ($offset + $prefs['directory_links_per_page'])) {
		$smarty->assign('next_offset', $offset + $prefs['directory_links_per_page']);
	} else {
		$smarty->assign('next_offset', -1);
	}

	if ($offset > 0) {
		$smarty->assign('prev_offset', $offset - $prefs['directory_links_per_page']);
	} else {
		$smarty->assign('prev_offset', -1);
	}

	$smarty->assign_by_ref('items', $items["data"]);
}

$section = 'directory';
include_once('tiki-section_options.php');

// Related categs
$related = $dirlib->dir_list_related_categories($_REQUEST['parent'], 0, -1, 'name_asc', '');
$smarty->assign_by_ref('related', $related['data']);

$stats = $dirlib->dir_stats();
$smarty->assign_by_ref('stats', $stats);
ask_ticket('dir-browse');

$smarty->assign('mid', 'tiki-directory_browse.tpl');
$smarty->display("tiki.tpl");

?>
