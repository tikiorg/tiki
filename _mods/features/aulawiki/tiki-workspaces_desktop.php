<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('tiki-setup.php');
require_once ('lib/workspaces/workspacelib.php');
require_once ('lib/workspaces/typeslib.php');
include_once ('lib/workspaces/workspacemoduleslib.php');

$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace) || $workspace == null || $workspace == "") {
	$smarty->assign('msg', tra("Workspace not found"));
	$smarty->display("error.tpl");
	die;
}

global $userlib;


if ($tiki_p_admin != 'y' && $tiki_p_admin_workspace!='y') {
	if ($userlib->object_has_one_permission($workspace["workspaceId"], 'workspace')) {
		if (!$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_view_workspace") && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_admin_workspace")) {
			$smarty->assign('msg', tra("Permission denied you cannot view this page"));
			$smarty->display("error.tpl");
			die;
		}
	}elseif($tiki_p_view_workspace != 'y'){
			$smarty->assign('msg', tra("Permission denied you cannot view this page"));
			$smarty->display("error.tpl");
			die;
	}
	
	$now = date("U");
	if ( (($workspace["startDate"]>$now || $workspace["endDate"]<$now) && ceil($workspace["startDate"]/60)!=ceil($workspace["endDate"]/60) ) || $workspace["closed"] == "y") {
		$smarty->assign('msg', tra("Closed Workspace"));
		$smarty->display("error.tpl");
		die;
	}
}

$wstype = $workspace["type"];

$workspaceId = $workspace["workspaceId"];
$wsmodtype= "workspace";
if (!$wsmoduleslib->workspace_has_assigned_zones($workspaceId,$wsmodtype)) {
	$workspaceId = $wstype["id"];
	$wsmodtype= "workspace type";
}


global $user;
$user_groups = $userlib->get_user_groups($user);

include_once ("tiki-workspaces_modules.php");

$path = $workspacesLib->get_workspace_path($workspace["workspaceId"]);

$smarty->assign_by_ref('workspace', $workspace);
$smarty->assign_by_ref('path', $path);
$smarty->assign('mid', 'tiki-workspaces_desktop.tpl');
$smarty->display('tiki.tpl');
?>