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
require_once ('lib/workspaces/userlib.php');
global $dbTiki;

$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$workspacesLib = new WorkspaceLib($dbTiki);
$wsuserlib = new WorkspaceUserLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
	global $tikilib;
	global $userlib;
	global $user;
	//$workspacesData = $workspacesLib->get_workspace_list(0, -1, 'name_desc', null, $workspace["workspaceId"]);
	$workspacesData = $workspacesLib->get_child_workspaces(0, -1, 'name_desc', $workspace["workspaceId"]);
	if (isset($workspace["type"]["userwstype"]) && $workspace["type"]["userwstype"]!="" && $workspace["type"]["userwstype"]!="0"){
		$workspace_users = $wsuserlib->get_includegrp_users("WSGRP".$workspace["code"]);
		if (isset($workspace_users) && count($workspace_users)>0){
			$smarty->assign_by_ref('workspace_users', $workspace_users);
		}
	}
	
	$smarty->assign_by_ref('workspaces', $workspacesData["data"]);
	$smarty->assign_by_ref('currentWorkspace', $workspace);
}
?>