<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_structure.php,v 1.20 2003-12-22 04:53:06 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');

if ($tiki_p_edit_structures != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["page_ref_id"])) {
	$smarty->assign('msg', tra("No structure indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('remove', 'n');

if (isset($_REQUEST["remove"])) {
	$smarty->assign('remove', 'y');
  $remove_info = $structlib->s_get_page_info($_REQUEST["remove"]);
	$smarty->assign('removepage', $_REQUEST["remove"]);
	$smarty->assign('removePageName', $remove_info["pageName"]);
}

$page_info      = $structlib->s_get_page_info($_REQUEST["page_ref_id"]);
if (isset($_REQUEST["rremove"])) {
	$structlib->s_remove_page($_REQUEST["rremove"], false);
  $_REQUEST["page_ref_id"] = $page_info["parent_id"];
}

if (isset($_REQUEST["sremove"])) {
	$structlib->s_remove_page($_REQUEST["sremove"], true);
  $_REQUEST["page_ref_id"] = $page_info["parent_id"];
}

$page_info      = $structlib->s_get_page_info($_REQUEST["page_ref_id"]);
$structure_info = $structlib->s_get_structure_info($_REQUEST["page_ref_id"]);
if (!isset($structure_info) or !isset($page_info) ) {
	$smarty->assign('msg', tra("Invalid structure_id or page_ref_id"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('page_ref_id', $_REQUEST["page_ref_id"]);
$smarty->assign('structure_id', $structure_info["page_ref_id"]);
$smarty->assign('structure_name', $structure_info["pageName"]);

if (isset($_REQUEST["create"])) {
	if (isset($_REQUEST["pageAlias"]))	{
		$structlib->set_page_alias($_REQUEST["page_ref_id"], $_REQUEST["pageAlias"]);
	}
  
  $after = null;
  if (isset($_REQUEST['after_ref_id'])) {
    $after = $_REQUEST['after_ref_id'];
  }
	if (!(empty($_REQUEST['name']))) {
		$structlib->s_create_page($_REQUEST["page_ref_id"], $after, $_REQUEST["name"], '');
		$userlib->copy_object_permissions($page_info["pageName"], $_REQUEST["name"],'wiki page');

	} 
	elseif(!empty($_REQUEST['name2'])) {
		foreach ($_REQUEST['name2'] as $name) {
			$new_page_ref_id = $structlib->s_create_page($_REQUEST["page_ref_id"], $after, $name, '');
      $after = $new_page_ref_id;
		}
	}
}

if (isset($_REQUEST["move_node"])) {
	if ($_REQUEST["move_node"] == '1') {
		$structlib->promote_node($_REQUEST["page_ref_id"]);
	} elseif ($_REQUEST["move_node"] == '2') {
		$structlib->move_before_previous_node($_REQUEST["page_ref_id"]);
	}	elseif ($_REQUEST["move_node"] == '3') {
		$structlib->move_after_next_node($_REQUEST["page_ref_id"]);
	} elseif ($_REQUEST["move_node"] == '4') {
		$structlib->demote_node($_REQUEST["page_ref_id"]);
	}
}

$page_info = $structlib->s_get_page_info($_REQUEST["page_ref_id"]);
$smarty->assign('pageName', $page_info["pageName"]);
$smarty->assign('pageAlias', $page_info["page_alias"]);

$subpages = $structlib->s_get_pages($_REQUEST["page_ref_id"]);
$max = count($subpages);
$smarty->assign_by_ref('subpages', $subpages);
if ($max != 0) {
  $last_child = $subpages[$max - 1];
  $smarty->assign('insert_after', $last_child["page_ref_id"]);
}
if (isset($_REQUEST["find_objects"])) {
	$find_objects = $_REQUEST["find_objects"];
} else {
	$find_objects = '';
}

$smarty->assign('find_objects', $find_objects);

// Get all wiki pages for the dropdown menu
$listpages = $tikilib->list_pages(0, 50, 'pageName_asc', $find_objects);
$smarty->assign_by_ref('listpages', $listpages["data"]);


$subtree = $structlib->get_subtree($structure_info["page_ref_id"]);
$smarty->assign('subtree', $subtree);

// Display the template
$smarty->assign('mid', 'tiki-edit_structure.tpl');
$smarty->display("tiki.tpl");

?>
