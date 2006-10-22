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
include_once ('lib/workspaces/printlib.php');
include_once "lib/structures/structlib.php";

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);
$resourcesLib = new WorkspaceResourcesLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();
$exit_module = false;

if($feature_wiki != 'y') {
   	$smarty->assign('error_msg', tra("This feature is disabled").": feature_wiki");
	$exit_module = true;
}

if (!$exit_module && !isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}

$page = $workspace["code"]."-WStructure";

if (!$exit_module && isset ($module_params["name"]) && $module_params["name"] != "") {
	$page = str_replace ("%WSCODE%",$workspace["code"],$module_params["name"]);
	$structlib = new StructLib($dbTiki);
  $structureId = $structlib->get_struct_ref_if_head($page);
}elseif (!$exit_module){
	$smarty->assign('error_msg', tra("Param name not found"));
	$exit_module = true;
}

$admWikiPerm = $userlib->object_has_permission($user,$page,"wiki page","tiki_p_admin_wiki");
$editStructPerm = $userlib->object_has_permission($user,$page,"wiki page","tiki_p_edit_structures");
$adminWorkspacePerm = $userlib->object_has_permission($user,$workspace["workspaceId"],"workspace","tiki_p_admin_workspace");

$canadmin = $tiki_p_admin=="y"||$tiki_p_admin_workspace=="y"||$adminWorkspacePerm||$admWikiPerm||$editStructPerm;
$smarty->assign('canadmin', $canadmin);

//TODO: Check perms
if($canadmin && isset($module_params["createObjectName"]) && $module_params["createObjectName"]!=""){
	$id = $resourcesLib->create_object($workspace["code"]."-".$module_params["createObjectName"], $module_params["createObjectDesc"], "wiki page", $workspace["categoryId"]);
	$workspacesLib->assign_permissions($workspace["code"], "wiki page", $id,$workspace["type"]);
  
	$subpages = $structlib->s_get_pages($module_params["parentPage".$module_params["structureId"]]);
	$max = count($subpages);
	$last_child_ref_id = null;
	if ($max != 0) {
	  $last_child = $subpages[$max - 1];
	  $last_child_ref_id = $last_child["page_ref_id"];
	}
	
	$new_page_ref_id = $structlib->s_create_page($module_params["parentPage".$module_params["structureId"]], $last_child_ref_id , $workspace["code"]."-".$module_params["createObjectName"], '');

}

if($canadmin && isset($module_params["removeObject"])){
	
	$structlib->s_remove_page($module_params["removePageId".$module_params["structureId"]], false);
}
					
if ($canadmin && isset($_REQUEST["move_node"]) && isset($_REQUEST["page_ref_id"])) {
		
		if ($_REQUEST["move_node"] == '1') {
			$structlib->promote_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '2') {
			$structlib->move_before_previous_node($_REQUEST["page_ref_id"]);
		}	elseif ($_REQUEST["move_node"] == '3') {
			$structlib->move_after_next_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '4') {
			$structlib->demote_node($_REQUEST["page_ref_id"]);
		}
	}

if (isset ($structureId) && $structureId != "") {
	$printlib = new PrintLib($dbTiki);
	$subtree = $printlib->s_print_structure($structureId);
	$smarty->assign_by_ref('subtree', $subtree);
	$smarty->assign('structureId', $structureId);
} else {
	$smarty->assign('subtree', null);
}
?>