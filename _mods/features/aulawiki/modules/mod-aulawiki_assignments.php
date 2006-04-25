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
include_once ('lib/aulawiki/workspacelib.php');
require_once ('lib/aulawiki/assignmentslib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
	$assignmentsLib = new AssignmentsLib($dbTiki);
	$smarty->assign('currentWorkspace', $workspace);
	$assignments = $assignmentsLib->get_assignments('startDate_desc', $workspace["workspaceId"]);
	$smarty->assign_by_ref('assignments', $assignments);
}
?>