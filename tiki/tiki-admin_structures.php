<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_structures.php,v 1.28 2007-06-05 04:48:19 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');
include_once ("lib/ziplib.php");

if ($tiki_p_edit_structures != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['rremove'])) {
  $area = 'delstruct';
  $structure_info = $structlib->s_get_structure_info($_REQUEST['rremove']);
	if (!$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_edit')) {
		$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
		$smarty->display("error.tpl");
		die;		
	}
  if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$structlib->s_remove_page($_REQUEST["rremove"], false);		
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST['rremovex'])) {
  $area = 'delstructandpages';
  $structure_info = $structlib->s_get_structure_info($_REQUEST['rremovex']);
	if (!$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_edit')) {
		$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
		$smarty->display("error.tpl");
		die;		
	}
  if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$structlib->s_remove_page($_REQUEST["rremovex"], true);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST['export'])) {
	check_ticket('admin-structures');
	$structure_info = $structlib->s_get_structure_info($_REQUEST['export']);
	if (!$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_view')) {
		$smarty->assign('msg',tra('Permission denied you cannot view this page'));
		$smarty->display("error.tpl");
		die;
	}
	$structlib->s_export_structure($_REQUEST['export']);
}

if (isset($_REQUEST['export_tree'])) {
	check_ticket('admin-structures');
	$structure_info = $structlib->s_get_structure_info($_REQUEST['export_tree']);
	if (!$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_view')) {
		$smarty->assign('msg',tra('Permission denied you cannot view this page'));
		$smarty->display("error.tpl");
		die;
	}
	header ("content-type: text/plain");

	$structlib->s_export_structure_tree($_REQUEST['export_tree']);
	die;
}

$smarty->assign('askremove', 'n');

if (isset($_REQUEST['remove'])) {
	check_ticket('admin-structures');
	$structure_info = $structlib->s_get_structure_info($_REQUEST['remove']);
	if (!$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_edit')) {
		$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
		$smarty->display("error.tpl");
		die;		
	}
	$smarty->assign('askremove', 'y');

	$smarty->assign('remove', $_REQUEST['remove']);
}

if ($feature_categories == 'y') {
	include_once ("categorize_list.php");
}

$smarty->assign('just_created', 'n');
if (isset($_REQUEST["create"])) {
	check_ticket('admin-structures');
	if ((empty($_REQUEST['name']))) {
		$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));

		$smarty->display("error.tpl");
		die;
	}
    //try to add a new structure
    $structure_id = $structlib->s_create_page(null, null , $_REQUEST["name"], $_REQUEST["alias"]);
	//Cannot create a structure if a structure already exists
    if (!isset($structure_id)) {
		$smarty->assign('msg', $_REQUEST['name'] . " " . tra("page not added (Exists)"));
		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('just_created', $structure_id);
	$smarty->assign('just_created_name', $_REQUEST['name']);	
    $parents[0] = $structure_id;
	$last_pages[0] = null;
	$tree_lines = explode("\n", $_REQUEST["tree"]);
	foreach ($tree_lines as $full_line) {
		$names = explode("->", $full_line);
		$line = $names[0];
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

            $alias = '';
            if (!empty($names[1])) {
				$alias = $names[1];
		    }
			$new_page_ref_id = $structlib->s_create_page($parent_id, $last_page, trim($line), trim($alias));
			if (isset($new_page_ref_id)) {
			    $parents[$tabs + 1] = $new_page_ref_id;
			    $last_pages[$tabs] = $new_page_ref_id;
		    }
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

if (isset($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
	$smarty->assign('maxRecords', $maxRecords);
}

$filter = '';
if (!empty($_REQUEST['lang'])) {
	$filter['lang'] = $_REQUEST['lang'];
	$smarty->assign_by_ref('find_lang', $_REQUEST['lang']);
}
if (!empty($_REQUEST['categId'])) {
	$filter['categId'] = $_REQUEST['categId'];
	$smarty->assign_by_ref('find_categId', $_REQUEST['categId']);
}

if (isset($_REQUEST["exact_match"])) {
	$exact_match = true;
	$smarty->assign('exact_match', 'y');
} else {
	$exact_match = false;
	$smarty->assign('exact_match', 'n');
}  

if ($feature_multilingual == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages(false, 'y');
	$smarty->assign_by_ref('languages', $languages);
	$avls = unserialize($tikilib->get_preference("available_languages"));
	$smarty->assign_by_ref('available_languages', $avls);
}

$channels = $structlib->list_structures($offset, $maxRecords, $sort_mode, $find, $exact_match, $filter);

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
ask_ticket('admin-structures');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_structures.tpl');
$smarty->display("tiki.tpl");

?>
