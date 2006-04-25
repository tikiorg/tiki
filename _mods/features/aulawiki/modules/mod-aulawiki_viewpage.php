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
require_once ('lib/aulawiki/assignmentslib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}
$smarty->assign('pageBody', "");
$smarty->assign('pageName', "");
if (isset($module_params["name"]) && $module_params["name"]!=""){
	$page = $workspace["code"]."-".$module_params["name"];
	$info = $tikilib->get_page_info($page);
	if (isset ($info) && $info != "") {
		$pdata = $tikilib->parse_data($info["data"], $info["is_html"]);
		$smarty->assign('pageBody', $pdata);
		$smarty->assign('pageName', $page);
	} else {
		$smarty->assign('error_msg', tra("Page not found"." ".$page));
	}
}else{
	$smarty->assign('error_msg', tra("Param name not found"));
	$exit_module = true;
}
?>