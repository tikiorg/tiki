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

$selectedWorkspaces = array ();
if (isset ($workspace) && $workspace != "") {
	$_SESSION["currentWorkspace"] = $workspace;
	$selectedWorkspaces[] = $workspace;
	$smarty->assign('activeWorkspace', $workspace);
}

global $user;

//If the selected workspace is hide, we need to show the first not hide father
if ($workspace["hide"] == "y") {
	$hideParent = true;
	do {
		$parentWorkspace = $workspacesLib->get_workspace_by_id($workspace["parentId"]);
		if (isset ($parentWorkspace)) {
			$wsType = $wsTypesLib->get_workspace_type_by_id($parentWorkspace["type"]);
			$parentWorkspace["type"] = $wsType;
			$newElement = array ();
			$newElement[] = $parentWorkspace;
			$selectedWorkspaces = array_merge($newElement, $selectedWorkspaces);
		}
		if (!isset ($parentWorkspace) || $parentWorkspace["hide"] != "y") {
			$hideParent = false;
		}
	} while ($hideParent);
}

if (isset ($user) && $user != "") {
	$userWorkspacesTmp = $workspacesLib->get_user_workspaces($user);
	$userWorkspaces = array ();

	do {
		$found = false;
		$userWorkspacesTmp2 = array ();
		foreach ($userWorkspacesTmp as $key => $workspace) {
			if ($workspace["hide"] == "y" && $workspacesLib->parentInSelWorspaces($workspace, $selectedWorkspaces)) {
				$wsType = $wsTypesLib->get_workspace_type_by_id($workspace["type"]);
				$workspace["type"] = $wsType;
				$selectedWorkspaces[] = $workspace;
				$found = true;
			}
			elseif ($workspace["hide"] != "y") {
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