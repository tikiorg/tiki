<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once ('lib/workspaces/workspacelib.php');
require_once ('lib/aulawiki/assignmentslib.php');
require_once ('lib/aulawiki/periodslib.php');
include_once ('lib/workspaces/userlib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();
$exit_module = false;

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}elseif (!isset ($_REQUEST["activeAssignment"])) {
	$smarty->assign('error_msg', tra("Assignment not found"));
	$exit_module = true;
}

if(!$exit_module){
	$assignmentsLib = new AssignmentsLib($dbTiki);
	$periodsLib = new PeriodsLib($dbTiki);
	$eduuserlib = new WorkspaceUserLib($dbTiki);
	
	//TODO: asociar al workspace un tipo de periodo y leer los ese tipo
	$periods = $periodsLib->get_periods_of_type(1);
	$smarty->assign("periods", $periods);
	
	$smarty->assign("showAssignmentPanel", false);
	
	$smarty->assign('currentWorkspace', $workspace);
	$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
	
	$activeAssignment = $assignmentsLib->get_assignment_by_id($_REQUEST["activeAssignment"]);
	if (isset ($activeAssignment)) {
		$users = $eduuserlib->get_includegrp_users("WSGRP".$workspace["code"]."-Student");
		$isTeacher = is_workspace_teacher($workspace, $eduuserlib);
		$smarty->assign('isTeacher', $isTeacher);
		$smarty->assign_by_ref('users', $users);
	
		if ($isTeacher && isset ($_REQUEST["saveGrades"])) {
			$grades = $assignmentsLib->get_assignment_grades($_REQUEST["activeAssignment"]);
			foreach ($users as $key => $guser) {
				if (isset ($_REQUEST["grade-".$guser["login"]])) {
					if (isset ($grades[$guser["login"]])) {
						$assignmentsLib->update_usergrade($_REQUEST["activeAssignment"], $guser["login"], $_REQUEST["comment-".$guser["login"]], $_REQUEST["grade-".$guser["login"]]);
					} else {
						$assignmentsLib->add_usergrade($_REQUEST["activeAssignment"], $guser["login"], $_REQUEST["comment-".$guser["login"]], $_REQUEST["grade-".$guser["login"]]);
					}
				}
			}
		}
	
		$grades = $assignmentsLib->get_assignment_grades($_REQUEST["activeAssignment"]);
		$smarty->assign_by_ref('grades', $grades);
		$smarty->assign('activeAssignment', $activeAssignment);
		$smarty->assign("showAssignmentPanel", true);
		global $user;
		$smarty->assign("currentUser", $user);
	} else {
		$smarty->assign('error_msg', tra("Assignment not found"));
	}
}

function is_workspace_teacher($workspace, $eduuserlib) {
	$teacher_users = $eduuserlib->get_includegrp_users("WSGRP".$workspace["code"]."-Teacher");
	global $user;
	foreach ($teacher_users as $key => $tuser) {
		if ($tuser["login"] == $user) {
			return true;
		}
	}
	return false;
}
?>