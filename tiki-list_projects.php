<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_projects.php,v 1.2 2005-01-22 22:54:55 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


// List projects
// Damian Parker aka Damosoft

require_once ('tiki-setup.php');

include_once ('lib/projects/projectlib.php');

if ($feature_categories == 'y') {
	include_once ('lib/categories/categlib.php');
}

if ($feature_projects != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_projects");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_project_list != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = $blog_list_order;
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

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
$listprojects = $projectslib->list_projects($offset, $maxRecords, $sort_mode, $find);

$temp_max = count($listprojects["data"]);

for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($listprojects["data"][$i]["projectId"], 'project')) {
		$listprojects["data"][$i]["individual"] = 'y';

		// blogs that user cannot read are not displayed at all
		$listprojects["data"][$i]["individual_tiki_p_project_view"] = 'y';

	} else {
		$listprojects["data"][$i]["individual"] = 'n';
	}
}

// If there're more records then assign next_offset
$cant_pages = ceil($listprojects["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listprojects["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('listprojects', $listprojects["data"]);

$section = 'projects';
include_once ('tiki-section_options.php');

ask_ticket('list-projects');

// Display the template
$smarty->assign('mid', 'tiki-list_projects.tpl');
$smarty->display("tiki.tpl");

?>
