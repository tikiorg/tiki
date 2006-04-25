<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('tiki-setup.php');
require_once ('lib/aulawiki/wstypeslib.php');
require_once ('lib/aulawiki/roleslib.php');

if ($tiki_p_admin != 'y' && (!isset ($aulawiki_p_admin_wstypes) || $aulawiki_p_admin_wstypes != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$rolesLib = new RolesLib($dbTiki);

$rolesData = $rolesLib->get_role_list();
$rolesAll = $rolesData["data"];
if (isset ($_REQUEST["send"])) {
	if (isset ($_REQUEST["anonymous"])) {
		$anonymous = "y";
	} else {
		$anonymous = "n";
	}
	if (isset ($_REQUEST["registered"])) {
		$registered = "y";
	} else {
		$registered = "n";
	}
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
	if (isset ($_REQUEST["roles"])) {
		$roles = $_REQUEST["roles"];
	} else {
		$roles = array ();
	}
	if (isset ($_REQUEST["workspaceResources"])) {
		$workspaceResources = serialize($_REQUEST["workspaceResources"]);
	} else {
		$workspaceResources = "";
	}

	if (isset ($_REQUEST["id"]) && ($_REQUEST["id"] != "")) {
		$wsTypesLib->update_workspace_type($_REQUEST["id"], $_REQUEST["code"], $_REQUEST["name"], $_REQUEST["desc"], $_REQUEST["menuid"], $active, $roles, $_REQUEST["userws"], $hide,$anonymous,$registered);
	} else {
		$wsTypesLib->add_workspace_type($_REQUEST["code"], $_REQUEST["name"], $_REQUEST["desc"], $_REQUEST["menuid"], $active, $roles, $workspaceResources, $_REQUEST["userws"], $hide,$anonymous,$registered);
	}

} else
	if (isset ($_REQUEST["edit"])) {
		$wstype = $wsTypesLib->get_workspace_type_by_id($_REQUEST["edit"]);
		$wstype["resources"] = unserialize($wstype["resources"]);
		foreach ($wstype["roles"] as $key => $rol) {
			foreach ($rolesAll as $keyAll => $rolAll) {
				if ($rolAll["name"] == $rol["name"]) {
					$rolesAll[$keyAll]["selected"] = true;
				}
			}
		}
	} else
		if (isset ($_REQUEST["delete"])) {
			$wsTypesLib->del_workspace_type($_REQUEST["delete"]);
			//borraRecursosAsg($idAsg,$dbTiki,$tikilib,$userlib);
			header("location: aulawiki-workspace_types.php");
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
$smarty->assign_by_ref('rolesAll', $rolesAll);
$smarty->assign_by_ref('wstypes', $wstypesData["data"]);
$smarty->assign_by_ref('wstype', $wstype);
$smarty->assign('mid', 'aulawiki-workspace_types.tpl');
$smarty->display('tiki.tpl');
?>