<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('tiki-setup.php');
require_once ('lib/workspaces/typeslib.php');

if ($tiki_p_admin != 'y' && (!isset ($tiki_p_admin_workspace) || $tiki_p_admin_workspace != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$wsTypesLib = new WorkspaceTypesLib($dbTiki);

if (isset ($_REQUEST["send"])) {
	if (isset ($_REQUEST["hide"])) {
		$hide = "y";
	} else {
		$hide = "n";
	}
	if (isset ($_REQUEST["active"])) {
		$active = $_REQUEST["active"];
	} else {
		$active = "n";
	}

	if (isset ($_REQUEST["id"]) && ($_REQUEST["id"] != "")) {
		$wsTypesLib->update_workspace_type($_REQUEST["id"], $_REQUEST["code"], $_REQUEST["name"], $_REQUEST["desc"], $_REQUEST["menuid"], $active, $_REQUEST["userws"], $hide);
	} else {
		$wsTypesLib->add_workspace_type($_REQUEST["code"], $_REQUEST["name"], $_REQUEST["desc"], $_REQUEST["menuid"], $active, "", $_REQUEST["userws"], $hide);
	}

} else
	if (isset ($_REQUEST["edit"])) {
		$wstype = $wsTypesLib->get_workspace_type_by_id($_REQUEST["edit"]);
	} else
		if (isset ($_REQUEST["delete"])) {
			$wsTypesLib->del_workspace_type($_REQUEST["delete"]);
			//borraRecursosAsg($idAsg,$dbTiki,$tikilib,$userlib);
			header("location: tiki-workspaces_types.php");
			die;
		}

if (!isset ($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = 'name_desc';
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset ($_REQUEST["numrows"])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST["numrows"];
}

$smarty->assign_by_ref('numrows', $numrows);

if (!isset ($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset ($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$wstypesData = $wsTypesLib->get_workspace_type_list($offset, $numrows, $sort_mode, $find);
$cant_pages = ceil($wstypesData["cant"] / $numrows);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $numrows));

if ($wstypesData["cant"] > ($offset + $numrows)) {
	$smarty->assign('next_offset', $offset + $numrows);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $numrows);
} else {
	$smarty->assign('prev_offset', -1);
}
$activeTypes = $wsTypesLib->list_active_types();
$smarty->assign_by_ref('activeTypes', $activeTypes);
$smarty->assign_by_ref('wstypes', $wstypesData["data"]);
$smarty->assign_by_ref('wstype', $wstype);
$smarty->assign('mid', 'tiki-workspaces_types.tpl');
$smarty->display('tiki.tpl');
?>