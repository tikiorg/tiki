<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once('tiki-setup.php');
require_once('lib/workspaces/typeslib.php');

if ($tiki_p_admin != 'y' && (!isset($tiki_p_admin_workspace) || $tiki_p_admin_workspace != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$wsTypesLib = new WorkspaceTypesLib($dbTiki);
$wstype = null;
if (isset($_REQUEST["wstypeId"])){
	$wstype = $wsTypesLib->get_workspace_type_by_id($_REQUEST["wstypeId"]);
}
if(isset($wstype) && $wstype!=null && $wstype!=""){
	$wstype["resources"] = unserialize($wstype["resources"]);
}else{
	$smarty->assign('msg', tra("Workspace type not found"));
	$smarty->display("error.tpl");
	die;
}

if(isset($_REQUEST["send"])) {
	$newResource = array();
	$newResource["name"] = $_REQUEST["name"];
	$newResource["desc"] = $_REQUEST["desc"];
	$newResource["type"] = $_REQUEST["type"];
	if (isset($_REQUEST["resourceId"]) && isset($wstype["resources"][$_REQUEST["resourceId"]])){
		$wstype["resources"][$_REQUEST["resourceId"]] = $newResource;
	}else{
		$wstype["resources"][] = $newResource;
	}
	$wsTypesLib->update_workspace_type_resources($_REQUEST["wstypeId"],serialize($wstype["resources"]));
}else if(isset($_REQUEST["edit"]) && isset($wstype["resources"][$_REQUEST["edit"]]) ) {
	$smarty->assign_by_ref('resource', $wstype["resources"][$_REQUEST["edit"]]);
	$smarty->assign('resourceId', $_REQUEST["edit"]);
}else if(isset($_REQUEST["delete"]) && isset($wstype["resources"][$_REQUEST["delete"]]) ) {
	$removed = array_splice($wstype["resources"],$_REQUEST["delete"],1);
	$wsTypesLib->update_workspace_type_resources($_REQUEST["wstypeId"],serialize($wstype["resources"]));
	header ("location: tiki-workspaces_types_resources.php?wstypeId=".$_REQUEST["wstypeId"]);
	die;
}else{
	$newResource = array();
	$newResource["name"] = "";
	$newResource["desc"] = "";
	$newResource["type"] = "";
}
$smarty->assign_by_ref('wstype',$wstype);
$smarty->assign('mid','tiki-workspaces_types_resources.tpl');
$smarty->display('tiki.tpl');

?>