<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_blogs.php,v 1.16 2003-12-28 20:12:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/blogs/bloglib.php');

if ($feature_blogs != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_blogs");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$smarty->display("error.tpl");
	die;
}

/*
if($feature_listPages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}
*/

/*
// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages"));
  $smarty->display("error.tpl");
  die;  
}
*/
if (isset($_REQUEST["remove"])) {
	check_ticket('list-blogs');

	// Check if it is the owner
	$data = $tikilib->get_blog($_REQUEST["remove"]);

	if ($data["user"] != $user) {
		if ($tiki_p_blog_admin != 'y') {
			$smarty->assign('msg', tra("Permission denied you cannot remove this blog"));

			$smarty->display("error.tpl");
			die;
		}
	}

	$bloglib->remove_blog($_REQUEST["remove"]);
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = $blog_list_order;
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

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_blogs($offset, $maxRecords, $sort_mode, $find);

for ($i = 0; $i < count($listpages["data"]); $i++) {
	if ($userlib->object_has_one_permission($listpages["data"][$i]["blogId"], 'blog')) {
		$listpages["data"][$i]["individual"] = 'y';

		if ($userlib->object_has_permission($user, $listpages["data"][$i]["blogId"], 'blog', 'tiki_p_read_blog')) {
			$listpages["data"][$i]["individual_tiki_p_read_blog"] = 'y';
		} else {
			$listpages["data"][$i]["individual_tiki_p_read_blog"] = 'n';
		}

		if ($userlib->object_has_permission($user, $listpages["data"][$i]["blogId"], 'blog', 'tiki_p_blog_post')) {
			$listpages["data"][$i]["individual_tiki_p_blog_post"] = 'y';
		} else {
			$listpages["data"][$i]["individual_tiki_p_blog_post"] = 'n';
		}

		if ($userlib->object_has_permission($user, $listpages["data"][$i]["blogId"], 'blog', 'tiki_p_create_blogs')) {
			$listpages["data"][$i]["individual_tiki_p_create_blogs"] = 'y';
		} else {
			$listpages["data"][$i]["individual_tiki_p_create_blogs"] = 'n';
		}

		if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $listpages["data"][$i]["blogId"], 'blog', 'tiki_p_blog_admin'))
			{
			$listpages["data"][$i]["individual_tiki_p_create_blogs"] = 'y';

			$listpages["data"][$i]["individual_tiki_p_blog_post"] = 'y';
			$listpages["data"][$i]["individual_tiki_p_read_blog"] = 'y';
		}
	} else {
		$listpages["data"][$i]["individual"] = 'n';
	}
}

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

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

$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
$section = 'blogs';
include_once ('tiki-section_options.php');

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_list_blogs($listpages, $tiki_p_read_blog);
}
ask_ticket('list-blogs');

// Display the template
$smarty->assign('mid', 'tiki-list_blogs.tpl');
$smarty->display("tiki.tpl");

?>
