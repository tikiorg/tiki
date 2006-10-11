<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once('tiki-setup.php');
require_once ('lib/workspaces/resourceslib.php');
require_once ('lib/workspaces/workspacelib.php');

global $dbTiki;
$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace) || $workspace == null || $workspace == "") {
	$smarty->assign('msg', tra("Workspace not found"));
	$smarty->display("error.tpl");
	die;
}
if (!isset ($_REQUEST["name"])) {
	$smarty->assign('msg', tra("Resource name not selected"));
	$smarty->display("error.tpl");
	die;

}

$type = "*";
if (isset ($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
}

$resourcesLib = new WorkspaceResourcesLib($dbTiki);
$resource = $resourcesLib->get_category_object($workspace["categoryId"], $_REQUEST["name"], $type);

if (isset ($resource) && $resource != "") {
	header("location: ".$resource["href"]);
	exit;
}

//If not found try with workspace code+name
$resource = $resourcesLib->get_category_object($workspace["categoryId"], $workspace["code"]."-".$_REQUEST["name"], $type);

if (isset ($resource) && $resource != "") {
	header("location: ".$resource["href"]);
	exit;
}

$smarty->assign('msg', tra("Resource not found"));
$smarty->display("error.tpl");
?>