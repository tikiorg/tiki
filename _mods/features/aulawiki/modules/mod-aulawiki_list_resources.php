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

require_once ('lib/aulawiki/categutillib.php');
require_once ('lib/aulawiki/workspacelib.php');

$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

$exit_module = false;

if (!isset ($workspace) || $workspace == null || $workspace == "") {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}else if (!isset ($_REQUEST["type"])) {
	$smarty->assign('error_msg', tra("Resource type not selected"));
	$exit_module = true;
}

if (!$exit_module){
	$categUtil = new CategUtilLib($dbTiki);
	$resources = $categUtil->get_category_objects($workspace["categoryId"], null, $_REQUEST["type"]);
	
	if (isset ($resources) && count($resources) == 1) {
		header("location: ".$resources[0]["href"]);
		exit;
	}
	
	$smarty->assign('resources', $resources);
	$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
	
	$smarty->assign_by_ref('workspace', $workspace);
	$smarty->assign('tpl_module_title', (isset ($module_params["title"]) ? $module_params["title"] : "Workspace resorces type ".$_REQUEST["type"]));
}else{	
	$smarty->assign('tpl_module_title', "Workspace resorces");
}
?>