<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once ('tiki-setup.php');
require_once ('lib/aulawiki/assignmentslib.php');
require_once ('lib/workspaces/workspacelib.php');
require_once ('lib/aulawiki/periodslib.php');
include_once ('lib/workspaces/userlib.php');

$workspacesLib = new WorkspaceLib($dbTiki);
$assignmentsLib = new AssignmentsLib($dbTiki);
$eduuserlib = new WorkspaceUserLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace) || $workspace == null || $workspace == "") {
	$smarty->assign('msg', tra("Workspace not found"));
	$smarty->display("error.tpl");
	die;
}

global $userlib;
if ($tiki_p_admin != 'y') {
	if (!$userlib->object_has_permission($user, $workspace["workspaceId"], 'assignments', "aulawiki_p_admin_assignments")) {
		$smarty->assign('msg', tra("Permission denied you cannot view this page"));
		$smarty->display("error.tpl");
		die;
	}
}

$periodsLib = new PeriodsLib($dbTiki);
//TODO: asociar al workspace un tipo de periodo y leer los ese tipo
$periods = $periodsLib->get_periods_of_type(1);
$smarty->assign("periods", $periods);

$activePeriodId = "";
if (isset ($_REQUEST["periodId"])) {
	$activePeriodId = $_REQUEST["periodId"];
}
elseif (isset ($_REQUEST["activePeriodId"])) {
	$activePeriodId = $_REQUEST["activePeriodId"];
}
elseif (isset ($periods) && count($periods) > 0) { //get present date period
	$now = date("U");
	foreach ($periods as $key => $period) {
		if ($period["startDate"] < $now && $period["endDate"] > $now) {
			$activePeriodId = $period["periodId"];
		}
	}
	if ($activePeriodId == "") {
		$activePeriodId = $periods[0]["periodId"];
	}
}
$smarty->assign("activePeriodId", $activePeriodId);

$assignments = $assignmentsLib->get_assignments('startDate_desc', $workspace["workspaceId"], $activePeriodId);
//$gradebook = $assignmentsLib->get_workspace_gradebook($workspace["workspaceId"],$activePeriodId);

$gradebook = $assignmentsLib->get_gradebook($workspace["workspaceId"], $activePeriodId);

$users = $eduuserlib->get_includegrp_users("WSGRP".$workspace["code"]."-Student");

if (isset ($_REQUEST["editGradebook"])) {
	$editAssgId = $_REQUEST["editAssgId"];
	foreach ($_REQUEST as $key => $value) {
		if ((substr($key, 0, 10) == "usergrade-")) {
			$userreq = substr($key, 10, strlen($key));
			$update = false;
			$comment = "";
			if (isset ($gradebook[$userreq])) {
				foreach ($gradebook[$userreq] as $key => $currentAssignment) {
					if ($currentAssignment["assignmentId"] == $editAssgId) {
						$update = true;
						$comment = $currentAssignment["comment"];
						$gradebook[$userreq][$key]["grade"] = $value;
						break;
					}
				}
			}
			if ($update) {
				if ($value == "-") {
					$assignmentsLib->del_usergrade($editAssgId, $userreq);
				} else {
					$assignmentsLib->update_usergrade($editAssgId, $userreq, $comment, $value);
				}
			} else {
				$assignmentsLib->add_usergrade($editAssgId, $userreq, "", $value);
			}

			//echo $key." ".$value;
		}
	}
	$gradebook = $assignmentsLib->get_gradebook($workspace["workspaceId"], $activePeriodId);
	//$groupusers = $eduuserlib->get_group_usersdata($_SESSION["activeGroup"]);
	//print_r($_REQUEST);
} else
	if (isset ($_REQUEST["editAssgId"])) {
		$smarty->assign('editAssgId', $_REQUEST["editAssgId"]);
	}
$smarty->assign_by_ref('users', $users);
$smarty->assign_by_ref('gradebook', $gradebook);
$smarty->assign_by_ref('assignments', $assignments);

$smarty->assign('mid', 'aulawiki-view_gradebook.tpl');
$smarty->display('tiki.tpl');
?>