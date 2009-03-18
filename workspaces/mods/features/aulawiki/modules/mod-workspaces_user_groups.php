<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once ('lib/workspaces/workspacelib.php');
include_once ('lib/workspaces/userlib.php');

global $dbTiki;
global $userlib;
global $tiki_p_admin;
$wsUserLib = new WorkspaceUserLib($dbTiki);
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();
$exit_module = false;
$can_admin_groups=false;
$can_add_users =false;

if ( $tiki_p_admin == 'y' 
   || $tiki_p_admin_workspace =='y' 
   || $workspacesLib->user_can_admin_workspace_or_upper($user,$workspace)) {
	$can_admin_groups = true;
	}
else	{			
	$can_admin_groups = false;
	}
if ($userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_create_workspace_resour")) {
	$can_add_users =true;
	}

if (!$can_admin_groups && !$can_add_users) {
	$smarty->assign('error_msg', tra("Permission denied, you cannot admin the workspace"));
	$exit_module = true;
}
	
if (isset ($workspace)) {
	$groupName = "WSGRP".$workspace["code"];
} else {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
	}
 
if (!$exit_module){
	if(!isset($module_params["activeGroup"])){
		$module_params["activeGroup"] = $groupName;
		$module_params["activeParentGroup"] = "-1";
	}
	
	if (isset ($module_params["addGroupActiveName"]) && $can_admin_groups) {
			if($module_params["addGroupName"]=="Anonymous" || $module_params["addGroupName"]=="Registered"){
			$smarty->assign('error_msg', tra("Anonymous and Registered group can't be added"));	
		}elseif ($userlib->group_exists($module_params["addGroupName"])) {
			$userlib->group_inclusion($module_params["addGroupName"], $module_params["addGroupActiveName"]);
		} else {
			$smarty->assign('error_msg', tra("group not found"));
		}
	}
	
	if (isset ($module_params["createGroupActiveName"]) && $can_admin_groups) {
		if ($userlib->group_exists($groupName."-".$module_params["createGroupName"])) {
			$smarty->assign('error_msg', tra("group already exist"));
		} else {
			$userlib->add_group($groupName."-".$module_params["createGroupName"], $module_params["createGroupDesc"], '');
			$userlib->group_inclusion($groupName."-".$module_params["createGroupName"], $module_params["createGroupActiveName"]);
		}
	}
	
	if (isset ($module_params["createUserActiveGrpName"])) {
		if ($userlib->user_exists($module_params["createUserName"])) {
			$userlib->assign_user_to_group($module_params["createUserName"], $module_params["createUserActiveGrpName"]);
		} else {
			$smarty->assign('error_msg', tra("user not found"));
		}
	}
	
	if (isset ($module_params["removeGroupActiveName"]) && $can_admin_groups) {
		if (!$userlib->group_exists($module_params["removeGroupActiveName"])){
			$smarty->assign('error_msg', tra("group not found"));
		}elseif($module_params["removeGroupActiveName"]=="WSGRP".$workspace["code"]){
			$smarty->assign('error_msg', tra("the main workspace group cant be removed"));
		}else{
			$wsUserLib->remove_inclusion($module_params["removeGroupActiveName"], $module_params["removeGroupActiveParentName"]);
			if (stristr($module_params["removeGroupActiveName"], $groupName)) {
				$userlib->remove_group($module_params["removeGroupActiveName"]);
			}
			$module_params["activeGroup"] = $groupName;
		}
	}
	
	if (isset ($module_params["removeUserGroupActiveName"])) {
		$result = $userlib->remove_user_from_group($module_params["removeUserName"], $module_params["removeUserGroupActiveName"]);
	}

	$smarty->assign('can_admin_groups', $can_admin_groups);	
	$smarty->assign('can_add_users', $can_add_users);	
	if ($topmost_workspace_Iadmin=$workspacesLib->get_topmost_workspace_Iadmin($user,$workspace)){
        	$allwsgroups = $workspacesLib->get_child_workspaces_groups($topmost_workspace_Iadmin,"WSGRP".$workspace["code"]);
	}
		$smarty->assign_by_ref('groups', $allwsgroups); 

	$wsgroups = $wsUserLib->get_descendant_groups($groupName, TRUE);
	$tree_nodes = array ();
	$imgGroup = "<img border=0 src='images/workspaces/edu_group.gif'>";
	foreach ($wsgroups as $parentGroup => $childgroups) {
		foreach ($childgroups as $childGroup) {
			$onclick = "onclick=\"document.getElementById('activeParentGroup').value='$parentGroup';document.getElementById('activeGroup').value='$childGroup';document['groupSelection'].submit();return false\"";
			
			$cssclass = "categtree";
			if ($module_params["activeGroup"] == $childGroup) {
				$cssclass = "categtreeActive";
			}
			$tree_nodes[] = array ("id" => $childGroup, "parent" => $parentGroup, "data" => '<a href="#" class="'.$cssclass.'" '.$onclick.'>'.$imgGroup.'&nbsp;'.$childGroup.'</a><br />');
		}
	}
	$onclick = "onclick=\"document.getElementById('activeParentGroup').value='-1';document.getElementById('activeGroup').value='$groupName';document['groupSelection'].submit();return false\"";
	
	$cssclass = "categtree";
	if ($module_params["activeGroup"] == $groupName) {
		$cssclass = "categtreeActive";
	}
	$tree_nodes[] = array ("id" => $groupName, "parent" => "99999999", "data" => '<a class="'.$cssclass.'" href="#" '.$onclick.'>'.$imgGroup.'&nbsp;'.$groupName.'</a><br />');
	include_once ('lib/tree/categ_browse_tree.php');
	$tm = new CatBrowseTreeMaker("categ");
	$res = $tm->make_tree("99999999", $tree_nodes);
	$smarty->assign('groupsTree', $res);
	
	//Get users in selected group
	$groupusers = $wsUserLib->get_group_usersdata($module_params["activeGroup"]);
	
	$smarty->assign('workspaceGroupName', $groupName);
	$smarty->assign('groupusers', $groupusers);
	$smarty->assign('activeGroup', $module_params["activeGroup"]);
	$smarty->assign('activeParentGroup', $module_params["activeParentGroup"]);
}
?>