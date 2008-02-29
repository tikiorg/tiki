<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL   
*/

require_once ('tiki-setup.php');
include_once ('lib/workspaces/workspacelib.php');
require_once ('lib/aulawiki/assignmentslib.php');
require_once ('lib/aulawiki/periodslib.php');

global $dbTiki;
global $userlib;
global $tiki_p_admin;

$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('msg', tra("Workspace not selected"));
	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_admin != 'y' && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'assignments', "aulawiki_p_view_assignments")) {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));
	$smarty->display("error.tpl");
	die;
}

$assignmentsLib = new AssignmentsLib($dbTiki);
$periodsLib = new PeriodsLib($dbTiki);

//TODO: asociar al workspace un tipo de periodo y leer los ese tipo
$periods = $periodsLib->get_periods_of_type(1);
$smarty->assign("periods", $periods);

if (isset ($_REQUEST["sendAssignment"]) && isset ($_REQUEST["assignmentId"]) && $_REQUEST["assignmentId"] != "") {
	if ($tiki_p_admin != 'y' && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'assignments', "aulawiki_p_edit_assignments")) {
		$smarty->assign('msg', tra("Permission denied you cannot edit assignments"));
		$smarty->display("error.tpl");
		die;
	}
	$assignmentsLib->update_assignment($_REQUEST["periodId"], $_REQUEST["gradeWeight"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["wikipage"], $_REQUEST["startDate"], $_REQUEST["endDate"], $_REQUEST["type"], $_REQUEST["assignmentId"]);
} else
	if (isset ($_REQUEST["sendAssignment"])) {
		if ($tiki_p_admin != 'y' && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'assignments', "aulawiki_p_create_assignments")) {
			$smarty->assign('msg', tra("Permission denied you cannot create assignments"));
			$smarty->display("error.tpl");
			die;
		}
		global $user;
		$uid = $assignmentsLib->add_assignment($workspace["workspaceId"], $_REQUEST["periodId"], $_REQUEST["gradeWeight"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["wikipage"], $user, $_REQUEST["startDate"], $_REQUEST["endDate"], $_REQUEST["type"]);
	} else
		if (isset ($_REQUEST["removeAssignment"])) {
			if ($tiki_p_admin != 'y' && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'assignments', "aulawiki_p_remove_assignments")) {
				$smarty->assign('msg', tra("Permission denied you cannot remove assignments"));
				$smarty->display("error.tpl");
				die;
			}
			$assignmentsLib->del_assignment($_REQUEST["removeAssignmentId"]);
		}

$smarty->assign("showAssignmentPanel", false);

$smarty->assign('currentWorkspace', $workspace);
$assignments = $assignmentsLib->get_assignments('startDate_desc', $workspace["workspaceId"]);
$smarty->assign_by_ref('assignments', $assignments);
$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);

if (($firstDayofWeek = $tikilib->get_user_preference($user, "")) == "") { /* 0 for Sundays, 1 for Mondays */
	$strRef = "First day of week: Sunday (its ID is 0) - translators you need to localize this string!";
	//get_strings tra("First day of week: Sunday (its ID is 0) - translators you need to localize this string!");
	if (($str = tra($strRef)) != $strRef) {
		$firstDayofWeek = ereg_replace("[^0-9]", "", $str);
		if ($firstDayofWeek < 0 || $firstDayofWeek > 9)
			$firstDayofWeek = 0;
	} else
		$firstDayofWeek = 0;
}

$activeAssignment = null;
if (isset ($_REQUEST["activeAssignment"])) {
	$activeAssignment = $assignmentsLib->get_assignment_by_id($_REQUEST["activeAssignment"]);
	$smarty->assign('activeAssignment', $activeAssignment);
	$smarty->assign("showAssignmentPanel", true);
}

$smarty->assign('firstDayofWeek', $firstDayofWeek);

$strRef = tra("%H:%M %Z");
if (strstr($strRef, "%h") || strstr($strRef, "%g"))
	$timeFormat12_24 = "12";
else
	$timeFormat12_24 = "24";
$smarty->assign('timeFormat12_24', $timeFormat12_24);

if (isset ($activeAssignment) && $activeAssignment != "") {
	$smarty->assign('startDate', $activeAssignment["startDate"]);
	$smarty->assign('endDate', $activeAssignment["endDate"]);
	$smarty->assign_by_ref('created', $activeAssignment["creationDate"]);
} else {
        $date = $tikilib->now;
	$smarty->assign('startDate', $date);
	$smarty->assign('endDate', $date);
}

$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M");

include_once ('tiki-jscalendar.php');
$smarty->assign('mid', 'aulawiki-assignments_admin.tpl');
$smarty->display('tiki.tpl');
?>