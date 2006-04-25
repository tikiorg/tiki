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
require_once ('lib/aulawiki/wstypeslib.php');

global $dbTiki;

$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
	$smarty->assign('currentWorkspace', $workspace);
	$wstype = $workspace["type"];
	if ($wstype["code"] != "PORTFOLIO") {
		$smarty->assign('error_msg', tra("Not a portfolio workspace"));
	} else {
		$smarty->assign('currentWorkspaceType', $wstype);
		$wscode = $workspace["code"];
		//The PORTFOLIO workspace begin with PWS followed by the userid
		$userid = substr($wscode, 3);
		$userWorkspaces = $workspacesLib->get_user_workspaces($userid);
		$smarty->assign_by_ref('userWorkspaces', $userWorkspaces);
	}
}
?>