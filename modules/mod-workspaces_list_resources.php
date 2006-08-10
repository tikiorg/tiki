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

require_once ('lib/workspaces/resourceslib.php');
require_once ('lib/workspaces/workspacelib.php');

$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

$exit_module = false;

if (!isset ($workspace) || $workspace == null || $workspace == "") {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}else if (!isset ($_REQUEST["type"]) && !isset ($module_params["type"])) {
	$smarty->assign('error_msg', tra("Resource type not selected"));
	$exit_module = true;
}

if (!$exit_module){
	$resourcesLib = new WorkspaceResourcesLib($dbTiki);
	if (isset($module_params["type"]) && $module_params["type"]!=""){
		$type = $module_params["type"];
	}else{
		$type = "*";
	}
	
	if (isset($module_params["showDesc"])){
		$smarty->assign('showDesc',$module_params["showDesc"]);
	}else{
		$smarty->assign('showDesc', 'y');
	}
	if (isset($_REQUEST["showType"])){
		$smarty->assign('showType', $_REQUEST["showType"]);
	}else{
		$smarty->assign('showType','n');
	}
	if (isset($module_params["showCreationDate"])){
		$smarty->assign('showCreationDate',$module_params["showCreationDate"]);
	}else{
		$smarty->assign('showCreationDate', 'y');
	}
	
	if (isset($module_params["showButtons"])){
		$smarty->assign('showButtons',$module_params["showButtons"]);
	}else{
		$smarty->assign('showButtons', 'n');
	}
	$resources = $resourcesLib->get_category_objects($workspace["categoryId"], null, $type);
	
/*	if (isset ($resources) && count($resources) == 1) {
		header("location: ".$resources[0]["href"]);
		exit;
	}*/
	
	$smarty->assign('resources', $resources);

	$smarty->assign_by_ref('workspace', $workspace);
}
?>