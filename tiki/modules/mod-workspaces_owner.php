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
require_once ('lib/workspaces/typeslib.php');
require_once ('lib/userprefs/userprefslib.php');

global $dbTiki;

$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$workspacesLib = new WorkspaceLib($dbTiki);
$userprefs = new UserPrefsLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}elseif(!isset($workspace["owner"]) || $workspace["owner"]==""){
	$smarty->assign('error_msg', tra("Owner not set"));
}else{
	$smarty->assign('currentWorkspace', $workspace);
	$wstype = $workspace["type"];
	$smarty->assign('currentWorkspaceType', $wstype);
	$userWorkspaces = $workspacesLib->get_user_workspaces($workspace["owner"]);
	$preferences = $userprefs->get_userprefs($workspace["owner"]);
	$userPreferences = array();
	foreach($preferences as $key=>$pref){
		$userPreferences[$pref["prefName"]] = $pref["value"];
	}

	$smarty->assign_by_ref('userWorkspaces', $userWorkspaces);
	$smarty->assign_by_ref('userPreferences', $userPreferences);
	$smarty->assign_by_ref('owner', $workspace["owner"]);
	$smarty->assign('showName', isset ($module_params["showName"]) ? $module_params["showName"] : 'y');
	$smarty->assign('showWorkspaces', isset ($module_params["showWorkspaces"]) ? $module_params["showWorkspaces"] : 'y');
}
?>