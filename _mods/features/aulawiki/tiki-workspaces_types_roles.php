<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('tiki-setup.php');
require_once ('lib/workspaces/typeslib.php');
require_once ('lib/workspaces/roleslib.php');

if ($tiki_p_admin != 'y' && (!isset ($tiki_p_admin_workspace) || $tiki_p_admin_workspace != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}
if (!isset ($_REQUEST["wstypeId"]) ) {
	$smarty->assign('msg', tra("Workspace type not selected"));
	$smarty->display("error.tpl");
	die;
}
$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$rolesLib = new WorkspaceRolesLib($dbTiki);
$wstype = $wsTypesLib->get_workspace_type_by_id($_REQUEST["wstypeId"]);

if (isset($_REQUEST["delete"]) && isset($_REQUEST["roleName"]) && $_REQUEST["roleName"]!="") {
	$wsTypesLib->del_workspace_type_role($_REQUEST["wstypeId"],$_REQUEST["roleName"]);
}else if(isset($_REQUEST["add"]) && isset($_REQUEST["roleName"]) && $_REQUEST["roleName"]!="") {
	if (isset($_REQUEST["permgroup"]) && $_REQUEST["permgroup"]!="" && !$userlib->group_exists($_REQUEST["permgroup"])){
			$userlib->add_group($_REQUEST["permgroup"], $_REQUEST["roleName"]." role permissions template for ".$wstype["name"]." workspace type", '');
	}
	$wsTypesLib->add_workspace_type_role($_REQUEST["wstypeId"],$_REQUEST["roleName"],$_REQUEST["permgroup"]);
}

$rolesData = $rolesLib->get_role_list();
$rolesAll = $rolesData["data"];
$wstype = $wsTypesLib->get_workspace_type_by_id($_REQUEST["wstypeId"]);
		
foreach ($wstype["roles"] as $key => $rol) {
	foreach ($rolesAll as $keyAll => $rolAll) {
		if ($rolAll["name"] == $rol["name"]) {
			$rolesAll[$keyAll]["selected"] = true;
		}
	}
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
$smarty->assign_by_ref('rolesAll', $rolesAll);
$smarty->assign_by_ref('wstypes', $wstypesData["data"]);
$smarty->assign_by_ref('wstype', $wstype);
$smarty->assign('mid', 'tiki-workspaces_types_roles.tpl');
$smarty->display('tiki.tpl');
?>