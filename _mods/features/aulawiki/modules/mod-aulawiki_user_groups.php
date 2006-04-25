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
include_once ('lib/aulawiki/eduuserlib.php');
include_once ('lib/aulawiki/workspacelib.php');

global $dbTiki;
global $userlib;
global $tiki_p_admin;
$eduuserlib = new EduUserLib($dbTiki);
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();
$exit_module = false;

if ($tiki_p_admin != 'y' && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "aulawiki_p_admin_workspace")) {
	$smarty->assign('error_msg', tra("Permission denied, you cannot admin the workspace"));
	$exit_module = true;
}elseif (isset ($workspace)) {
	$groupName = "WSGRP".$workspace["code"];
} else {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}

if (!$exit_module){
	if (isset ($_REQUEST["activeGroup"])) {
		$_SESSION["activeGroup"] = $_REQUEST["activeGroup"];
		$_SESSION["activeParentGroup"] = $_REQUEST["activeParentGroup"];
	}elseif(!isset($_SESSION["activeGroup"])){
		$_SESSION["activeGroup"] = $groupName;
		$_SESSION["activeParentGroup"] = "-1";
	}
	
	if (isset ($_REQUEST["addGroupActiveName"])) {
		if ($userlib->group_exists($_REQUEST["addGroupName"])) {
			$userlib->group_inclusion($_REQUEST["addGroupName"], $_REQUEST["addGroupActiveName"]);
		} else {
			$smarty->assign('error_msg', tra("group not found"));
		}
	}
	
	if (isset ($_REQUEST["createGroupActiveName"])) {
		if ($userlib->group_exists($groupName."-".$_REQUEST["createGroupName"])) {
			$smarty->assign('error_msg', tra("group already exist"));
		} else {
			$userlib->add_group($groupName."-".$_REQUEST["createGroupName"], $_REQUEST["createGroupDesc"], '');
			$userlib->group_inclusion($groupName."-".$_REQUEST["createGroupName"], $_REQUEST["createGroupActiveName"]);
		}
	}
	
	if (isset ($_REQUEST["createUserActiveGrpName"])) {
		if ($userlib->user_exists($_REQUEST["createUserName"])) {
			$userlib->assign_user_to_group($_REQUEST["createUserName"], $_REQUEST["createUserActiveGrpName"]);
		} else {
			$smarty->assign('error_msg', tra("user not found"));
		}
	}
	
	if (isset ($_REQUEST["removeGroupActiveName"])) {
		if (!$userlib->group_exists($_REQUEST["removeGroupActiveName"])){
			$smarty->assign('error_msg', tra("group not found"));
		}elseif($_REQUEST["removeGroupActiveName"]=="WSGRP".$workspace["code"]){
			$smarty->assign('error_msg', tra("the main workspace group cant be removed"));
		}else{
			$eduuserlib->remove_inclusion($_REQUEST["removeGroupActiveName"], $_REQUEST["removeGroupActiveParentName"]);
			if (stristr($_REQUEST["removeGroupActiveName"], $groupName)) {
				$userlib->remove_group($_REQUEST["removeGroupActiveName"]);
			}
		}
	}
	
	if (isset ($_REQUEST["removeUserGroupActiveName"])) {
		$result = $userlib->remove_user_from_group($_REQUEST["removeUserName"], $_REQUEST["removeUserGroupActiveName"]);
	}
	
	$ownurl = $tikilib->httpPrefix().$_SERVER["REQUEST_URI"];
	
	$wsgroups = $eduuserlib->get_descendant_groups($groupName, TRUE);
	$tree_nodes = array ();
	$imgGroup = "<img border=0 src='images/aulawiki/edu_group.gif'>";
	foreach ($wsgroups as $parentGroup => $childgroups) {
		foreach ($childgroups as $childGroup) {
			//$onclick = "onclick=\"alert('hola');document.getElementById('activeGroup').value='$childGroup';document.getElementById('groupSelection').submit();return false\"";
			$onclick = "onclick=\"document.getElementById('activeParentGroup').value='$parentGroup';document.getElementById('activeGroup').value='$childGroup';document['groupSelection'].submit();return false\"";
			//$onclick = "onclick=\"alert('hola')\"";
			//$href = "href=\"javascript:document.getElementById('activeGroup').value='$childGroup';document.getElementById('groupSelection').submit();return false\"";
			//$onclick = "";
			$cssclass = "categtree";
			if ($_SESSION["activeGroup"] == $childGroup) {
				$cssclass = "categtreeActive";
			}
			$tree_nodes[] = array ("id" => $childGroup, "parent" => $parentGroup, "data" => '<a href="#" class="'.$cssclass.'" '.$onclick.'>'.$imgGroup.'&nbsp;'.$childGroup.'</a><br />');
		}
	}
	$onclick = "onclick=\"document.getElementById('activeParentGroup').value='-1';document.getElementById('activeGroup').value='$groupName';document['groupSelection'].submit();return false\"";
	
	$cssclass = "categtree";
	if ($_SESSION["activeGroup"] == $groupName) {
		$cssclass = "categtreeActive";
	}
	$tree_nodes[] = array ("id" => $groupName, "parent" => "99999999", "data" => '<a class="'.$cssclass.'" href="#" '.$onclick.'>'.$imgGroup.'&nbsp;'.$groupName.'</a><br />');
	include_once ('lib/tree/categ_browse_tree.php');
	$tm = new CatBrowseTreeMaker("categ");
	$res = $tm->make_tree("99999999", $tree_nodes);
	$smarty->assign('groupsTree', $res);
	
	//Get users in selected group
	$groupusers = $eduuserlib->get_group_usersdata($_SESSION["activeGroup"]);
	
	$smarty->assign('workspaceGroupName', $groupName);
	$smarty->assign('groupusers', $groupusers);
	$smarty->assign('activeGroup', $_SESSION["activeGroup"]);
	$smarty->assign('activeParentGroup', $_SESSION["activeParentGroup"]);
	$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
}
?>