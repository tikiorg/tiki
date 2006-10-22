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

global $dbTiki;

$wsTypesLib = new WorkspaceTypesLib($dbTiki);

$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

$selectedWorkspaces = array ();
if (isset ($workspace) && $workspace != "") {
	$_SESSION["currentWorkspace"] = $workspace;
	$selectedWorkspaces[] = $workspace;
	$smarty->assign('activeWorkspace', $workspace);
}
global $user;
$parentId = $workspace["parentId"];
while($parentId != 0) {
	$parentWorkspace = $workspacesLib->get_workspace_by_id($parentId);
	if (isset ($parentWorkspace)) {
		$wsType = $wsTypesLib->get_workspace_type_by_id($parentWorkspace["type"]);
		$parentWorkspace["type"] = $wsType;
		$newElement = array ();
		$newElement[] = $parentWorkspace;
		$selectedWorkspaces = array_merge($newElement, $selectedWorkspaces);
	}
	$parentId = $parentWorkspace["parentId"];
}

if (isset ($user) && $user != "") {
	$userWorkspacesTmp = $workspacesLib->get_user_workspaces($user);
	$userWorkspaces = array ();

	do {
		$found = false;
		$userWorkspacesTmp2 = array ();
		foreach ($userWorkspacesTmp as $key => $workspace) {
			if ( $workspacesLib->parentInSelWorspaces($workspace, $selectedWorkspaces)) {
				$wsType = $wsTypesLib->get_workspace_type_by_id($workspace["type"]);
				$workspace["type"] = $wsType;
				$selectedWorkspaces[] = $workspace;
				$found = true;
			}
			if ($workspace["hide"] != "y") {
				$userWorkspacesTmp2[] = $workspace;
			}

		}
		$userWorkspacesTmp = array ();
		$userWorkspacesTmp = $userWorkspacesTmp2;
	} while ($found); //&& count($userWorkspacesTmp)>0);

	$smarty->assign_by_ref('userWorkspaces', $userWorkspacesTmp);
}
$smarty->assign('selectedWorkspaces', $selectedWorkspaces);

?>