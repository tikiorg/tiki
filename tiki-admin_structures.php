<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_structures.php,v 1.12 2003-11-14 09:02:45 chris_holman Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');
include_once ("lib/ziplib.php");

if ($tiki_p_edit_structures != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST['rremove'])) {
	$structlib->s_remove_page($_REQUEST["rremove"], false);
}

if (isset($_REQUEST['rremovex'])) {
	$structlib->s_remove_page($_REQUEST["rremovex"], true);
}

if (isset($_REQUEST['export'])) {
	$structlib->s_export_structure($_REQUEST['export']);
}

if (isset($_REQUEST['export_tree'])) {
	header ("content-type: text/plain");

	$structlib->s_export_structure_tree($_REQUEST['export_tree']);
	die;
}

$smarty->assign('askremove', 'n');

if (isset($_REQUEST['remove'])) {
	$smarty->assign('askremove', 'y');

	$smarty->assign('remove', $_REQUEST['remove']);
}

if (isset($_REQUEST["create"])) {
	if ((empty($_REQUEST['name']))) {
		$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}
    //try to add a new structure
    $structure_id = $structlib->s_create_page(null, null , $_REQUEST["name"], $_REQUEST["alias"]);
	//Cannot create a structure if a structure already exists
    if (!isset($structure_id)) {
		$smarty->assign('msg', $_REQUEST['name'] . " " . tra("page not added (Exists)"));
		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

    $parents[0] = $structure_id;
	$last_pages[0] = null;
	$tree_lines = explode("\n", $_REQUEST["tree"]);
	foreach ($tree_lines as $line) {
		$line = rtrim($line);
		// count the depth level (leading spaces indicate it)
		$tabs = strlen($line) - strlen(ltrim($line));
		// Is there smth else 'cept spaces?
		if (strlen($line = trim($line))) {

			$parent_id = $parents[$tabs];
			if (isset($last_pages[$tabs])) {
			    $last_page = $last_pages[$tabs];
		    }
			else {
				$last_page = null;
			}

			$new_page_ref_id = $structlib->s_create_page($parent_id, $last_page, trim($line), '');
			if (isset($new_page_ref_id)) {
			    $parents[$tabs + 1] = $new_page_ref_id;
			    $last_pages[$tabs] = $new_page_ref_id;
		    }
		}
	}
}

//
// Thu 03 Jul 2003 10:06:01 PM MSD, by zaufi
// TODO: Even after my fixes for invalid page names
//       this code still too buggy... Try to add 
//       " NewStruct" as tree... with leading space
//       or line with 3 leading spaces followed by 
//       line with one leading space... 
//       I.e. level depth parser too stupid... :()
//
if (isset($_REQUEST["create_from_tree"])) {
	if ((empty($_REQUEST['name']))) {
		$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));
		$smarty->display("styles/$style_base/error.tpl");
		die;
	}
	//Cannot create a new structure if page already exists with that name
    if (!isset($structure_id)) {
		$smarty->assign('msg', $_REQUEST['name'] . " " . tra("page not added (Exists)"));
		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$parents = array();
	$level = 1;
	$current_parent[$level] = $structure_id; 
	$last_page[$level] = $structure_id;
	$tree_lines = explode("\n", $_REQUEST["tree"]);
	foreach ($tree_lines as $line) {
		$line = rtrim($line);
		// count the depth level (leading spaces indicate it)
		$tabs = strlen($line) - strlen(ltrim($line));
		// Is there smth else 'cept spaces?
		if (strlen($line = trim($line))) {

			$current_parent[$tabs + 2] = $line;
			$parent = $parents[$tabs];

			if (isset($previous[$tabs]))
				$prev = $previous[$tabs];
			else
				$prev = '';

			$structlib->s_create_page($parent, $prev, trim($line), '');
			$previous[$tabs] = $line;
		}
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'pageName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

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

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $structlib->list_structures($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('channels', $channels["data"]);

// Display the template
$smarty->assign('mid', 'tiki-admin_structures.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
