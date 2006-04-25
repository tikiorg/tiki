<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('tiki-setup.php');
require_once ('lib/aulawiki/roleslib.php');

if ($tiki_p_admin != 'y' && (!isset ($aulawiki_p_admin_roles) || $aulawiki_p_admin_roles != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$rolesLib = new RolesLib($dbTiki);

$levelsTmp = $userlib->get_permission_levels();
sort($levelsTmp);
$levels = array ();
foreach ($levelsTmp as $key => $level) {
	$levels[$key] = array ();
	$levels[$key]["name"] = $level;
	$levels[$key]["selected"] = false;
}

if (isset ($_REQUEST["send"])) {
	$levelsReq = "";
	if (isset ($_REQUEST["levels"])) {
		$levelsReq = serialize($_REQUEST["levels"]);
	}
	if (isset ($_REQUEST["uid"]) && ($_REQUEST["uid"] != "")) {
		$rolesLib->update_role($_REQUEST["uid"], $_REQUEST["name"], $_REQUEST["desc"], $levelsReq);
	} else {
		$rolesLib->add_role($_REQUEST["name"], $_REQUEST["desc"], $levelsReq);
	}

} else
	if (isset ($_REQUEST["edit"])) {
		$role = $rolesLib->get_role_by_uid($_REQUEST["edit"]);

		if (isset ($role["levels"]) && $role["levels"] != "") {
			$role["levels"] = unserialize($role["levels"]);
		} else {
			$role["levels"] = array ();
		}

		foreach ($levelsTmp as $key => $level) {
			if (in_array($level, $role["levels"])) {
				$levels[$key]["selected"] = true;
			}
		}

	} else
		if (isset ($_REQUEST["delete"])) {
			$rolesLib->del_role($_REQUEST["delete"]);
			header("location: aulawiki-roles.php");
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

$smarty->assign('levels', $levels);

$smarty->assign_by_ref('roles', $rolesData["data"]);
$smarty->assign_by_ref('role', $role);
$smarty->assign('mid', 'aulawiki-roles.tpl');
$smarty->display('tiki.tpl');
?>