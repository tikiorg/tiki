<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('tiki-setup.php');
require_once ('lib/workspaces/roleslib.php');

if ($tiki_p_admin != 'y' && (!isset ($tiki_p_admin_workspace) || $tiki_p_admin_workspace != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$rolesLib = new WorkspaceRolesLib($dbTiki);

$smarty->assign('page_error_msg', tra(""));
$exit = false;

if (isset ($_REQUEST["send"])) {

	if (!isset($_REQUEST["name"]) || $_REQUEST["name"]==""){
		$smarty->assign('page_error_msg', tra("Role name not selected"));
		$exit = true;
	}
	if (!$exit && (!isset ($_REQUEST["uid"]) || ($_REQUEST["uid"] == ""))){
		$roleOld = $rolesLib->get_role_by_name($_REQUEST["name"]);
		if (isset($roleOld) && $roleOld!=""){
			$smarty->assign('page_error_msg', tra("Role name in use, please select a different name"));
			$exit = true;
		}
	}
	if(!$exit){
		if (isset ($_REQUEST["permgroup"]) && $_REQUEST["permgroup"]!="") {
			$permgroup = $_REQUEST["permgroup"];
		}else{
			$permgroup = "RolePerms-".$_REQUEST["name"];
		}
		if (isset ($_REQUEST["uid"]) && ($_REQUEST["uid"] != "")) {
			$rolesLib->update_role($_REQUEST["uid"], $_REQUEST["name"], $_REQUEST["desc"], $permgroup);
		} else {
			$rolesLib->add_role($_REQUEST["name"], $_REQUEST["desc"], $permgroup);
		}
		if (!$userlib->group_exists($permgroup)){
			$userlib->add_group($permgroup, $_REQUEST["name"]." role permissions template", '');
		}
	}

} else
	if (isset ($_REQUEST["edit"])) {
		$role = $rolesLib->get_role_by_uid($_REQUEST["edit"]);
	} else
		if (isset ($_REQUEST["delete"])) {
			$rolesLib->del_role($_REQUEST["delete"]);
			header("location: tiki-workspaces_roles.php");
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

$rolesData = $rolesLib->get_role_list($offset, $numrows, $sort_mode, $find);
$cant_pages = ceil($rolesData["cant"] / $numrows);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $numrows));

if ($rolesData["cant"] > ($offset + $numrows)) {
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

//$smarty->assign('levels', $levels);

$smarty->assign_by_ref('roles', $rolesData["data"]);
if ($exit){
	$tmpRole = array();
	$tmpRole["name"] = $_REQUEST["name"];
	$tmpRole["description"] = $_REQUEST["desc"];
	$tmpRole["permgroup"] = $_REQUEST["permgroup"];
	$tmpRole["uid"] = $_REQUEST["uid"];
	$smarty->assign_by_ref('role', $tmpRole);
}else{
	$smarty->assign_by_ref('role', $role);
}
$smarty->assign('mid', 'tiki-workspaces_roles.tpl');
$smarty->display('tiki.tpl');
?>