<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once ('tiki-setup.php');
include_once ('lib/aulawiki/workspacemoduleslib.php');
require_once('lib/aulawiki/workspacelib.php');
require_once('lib/aulawiki/wstypeslib.php');
require_once('lib/modules/modlib.php');

if ($tiki_p_admin != 'y' && $tiki_p_configure_modules != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_SESSION["currentWorkspace"]) && !isset($_REQUEST["workspaceId"])) {
	$smarty->assign('msg', tra("Workspace not selected"));
	$smarty->display("error.tpl");
	die;
}

$wsuser = "";
$workspacesLib = new WorkspaceLib($dbTiki);
$wsTypesLib = new WorkspaceTypesLib($dbTiki);

$title = "";
$workspaceId="";
$wsmodtype="";
if(isset($_REQUEST["workspaceId"]) && $_REQUEST["wsmodtype"]=="workspace"){
	$workspaceId=$_REQUEST["workspaceId"];
	$workspace = $workspacesLib->get_workspace_by_id($_REQUEST["workspaceId"]);
	if (isset($workspace)){
		$wsuser = "WSUSER-".$workspace["code"];
		$workspaceId = $_REQUEST["workspaceId"];
		$wsmodtype  = "workspace";
		$title = "Workspace: (".$workspace["code"].") ".$workspace["name"];
		$type = $workspace["type"];
	}
}elseif(isset($_REQUEST["workspaceId"]) && $_REQUEST["wsmodtype"]=="workspace type"){
	$wstype = $wsTypesLib->get_workspace_type_by_id($_REQUEST["workspaceId"]);
	if (isset($wstype)){
		$wsuser = "WSTUSER-".$wstype["code"];
		$workspaceId = $_REQUEST["workspaceId"];
		$wsmodtype  = "workspace type";
		$title = "Workspace Type: ".$wstype["name"];
	}	
}elseif(isset($_SESSION["currentWorkspace"])){
	$workspace = $workspacesLib->get_workspace_by_id($_SESSION["currentWorkspace"]["workspaceId"]);
	if (isset($workspace)){
		$wsuser = "WSUSER-".$workspace["code"];
		$workspaceId = $_REQUEST["workspaceId"];
		$wsmodtype  = "workspace";
		$title = "Workspace: (".$workspace["code"].") ".$workspace["name"];
		$type = $workspace["type"];
	}
}

if (!isset($workspaceId) || $workspaceId=="") {
	$smarty->assign('msg', tra("Workspace not selected"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('title', $title);

if (isset($_REQUEST["recreate"])) {
	check_ticket('user-modules');
	assign_wstype_modules($workspaceId,$wsmodtype,$wsmoduleslib);
}
if (isset($_REQUEST["clean"])) {
	check_ticket('user-modules');
	clean_assigned_modules($workspaceId,$wsmodtype,$wsmoduleslib);
}

if (isset($_REQUEST["unassign"])) {
	check_ticket('user-modules');
	$wsmoduleslib->unassign_workspace_module($_REQUEST["moduleId"]);
}

if (isset($_REQUEST["assign"]) && isset($_REQUEST["moduleId"]) && $_REQUEST["moduleId"]!="" ) {
	check_ticket('user-modules');
	//$wsmoduleslib->update_workspace_module($_REQUEST["module"],$_REQUEST["position"],$_REQUEST["order"],$_REQUEST["title"],$_REQUEST["cache_time"],$_REQUEST["rows"],$_REQUEST["params"],$_REQUEST["groups"],$_REQUEST["moduleId"]);
	$wsmoduleslib->update_workspace_module($_REQUEST["module"],$_REQUEST["position"],$_REQUEST["order"],$_REQUEST["title"],"","",$_REQUEST["params"],"",$_REQUEST["moduleId"]);
}else if(isset($_REQUEST["assign"])){
	check_ticket('user-modules');
	//$wsmoduleslib->assign_workspace_module($_REQUEST["module"],$_REQUEST["position"],$_REQUEST["order"],$wsmodtype,$workspaceId,$_REQUEST["title"],$_REQUEST["cache_time"],$_REQUEST["rows"],$_REQUEST["params"],$_REQUEST["groups"]);
	$wsmoduleslib->assign_workspace_module($_REQUEST["module"],$_REQUEST["position"],$_REQUEST["order"],$wsmodtype,$workspaceId,$_REQUEST["title"],"","",$_REQUEST["params"],"");

}

if (isset($_REQUEST["edit"])) {
	$editmod = $wsmoduleslib->get_workspace_assigned_module($_REQUEST["edit"]);
	$smarty->assign('module', $editmod);
}
	
if (isset($_REQUEST["up"])) {
	check_ticket('user-modules');
	$wsmoduleslib->up_workspace_module($_REQUEST["moduleId"]);
}

if (isset($_REQUEST["down"])) {
	check_ticket('user-modules');
	$wsmoduleslib->down_workspace_module($_REQUEST["moduleId"]);
}

if (isset($_REQUEST["left"])) {
	check_ticket('user-modules');
	$wsmoduleslib->set_column_workspace_module($_REQUEST["moduleId"], 'l');
}

if (isset($_REQUEST["right"])) {
	check_ticket('user-modules');
	$wsmoduleslib->set_column_workspace_module($_REQUEST["moduleId"], 'r');
}

$orders = array();

for ($i = 1; $i < 20; $i++) {
	$orders[] = $i;
}

$smarty->assign_by_ref('orders', $orders);

$assignables = $modlib->get_all_modules();
sort ($assignables);

if (count($assignables) > 0) {
	$smarty->assign('canassign', 'y');
} else {
	$smarty->assign('canassign', 'n');
}

//print_r($assignables);
$modules = $wsmoduleslib->get_workspace_assigned_modules($workspaceId,$wsmodtype);
$smarty->assign('modules_l', $wsmoduleslib->get_workspace_assigned_modules_pos($workspaceId,$wsmodtype, 'l'));
$smarty->assign('modules_r', $wsmoduleslib->get_workspace_assigned_modules_pos($workspaceId,$wsmodtype, 'r'));

$smarty->assign_by_ref('assignables', $assignables);
$smarty->assign_by_ref('modules', $modules);
$smarty->assign("workspaceId",$workspaceId);
$smarty->assign("wsmodtype",$wsmodtype);
//print_r($modules);
include_once ('tiki-mytiki_shared.php');

ask_ticket('user-modules');

$smarty->assign('mid', 'aulawiki-ws_assigned_modules.tpl');
$smarty->display("tiki.tpl");


function clean_assigned_modules($workspaceId,$wsmodtype,$wsmoduleslib){
	$modules = $wsmoduleslib->get_workspace_assigned_modules($workspaceId,$wsmodtype);	
	foreach ($modules as $key => $module) {
			$wsmoduleslib->unassign_workspace_module($module["moduleId"]);
	}
}

function assign_wstype_modules($workspaceId,$wsmodtype,$wsmoduleslib){
	clean_assigned_modules($workspaceId,$wsmodtype,$wsmoduleslib);
	$modules = $wsmoduleslib->get_workspace_assigned_modules($workspaceId,$wsmodtype);
	foreach ($modules as $key => $module) {
		$wsmoduleslib->assign_workspace_module($module["name"], $module["position"], $module["ord"],$wsmodtype,$workspaceId,$module["title"],$module["cache_time"],$module["rows"],$module["params"],$module["groups"]);
	}	
}
?>