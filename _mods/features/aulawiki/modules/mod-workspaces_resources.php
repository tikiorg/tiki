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
require_once ('tiki-setup.php');
include_once ('lib/workspaces/workspacelib.php');
include_once ('lib/workspaces/typeslib.php');
include_once ('lib/workspaces/resourceslib.php');

global $dbTiki;
global $userlib;
global $feature_phplayers;

$workspacesLib = new WorkspaceLib($dbTiki);
$resourcesLib = new WorkspaceResourcesLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

$exit_module=false;
if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
	$exit_module = true;
}
global $userlib;
global $tiki_p_admin;
if (!$exit_module && $tiki_p_admin != 'y' && $tiki_p_admin_workspace != 'y') {
	if (!$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_view_workspace")) {
		$smarty->assign('error_msg', tra("Permission denied you cannot view this page"));
		$exit_module = true;
	}
}
if (!$exit_module){
	if (!isset ($_REQUEST["objectCreated"]) && isset ($_REQUEST["createObject"]) && isset ($_REQUEST["createObjectCategoryId"])) {
		if($tiki_p_admin != 'y' && $tiki_p_admin_workspace != 'y' && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_admin_workspace") && !$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_create_workspace_resour")){
			$smarty->assign('error_msg', tra("Permission denied you cannot create new resources"));
		}elseif (!isset ($_REQUEST["createObjectName"])) {
			$smarty->assign('error_msg', tra("Name requiered"));
		} else {
			//$wsTypesLib = new WorkspaceTypesLib($dbTiki);
	
			$wsType = $workspace["type"];
	
			$wscode = "";
			$wscode = $workspace["code"];
			$id = $resourcesLib->create_object($wscode."-".$_REQUEST["createObjectName"], $_REQUEST["createObjectDesc"], $_REQUEST["createObjectType"], $_REQUEST["createObjectCategoryId"]);
			$workspacesLib->assign_permissions($wscode, $_REQUEST["createObjectType"], $id,$wsType);
	
			$resourcesLib->redirect($id, $wscode."-".$_REQUEST["createObjectName"], $_REQUEST["createObjectType"]);
			//$smarty->assign('error_msg', $tikilib->httpPrefix()."/tiki-index.php");
		}
	}
	elseif (isset ($_REQUEST["objectCreated"])) {
		$smarty->assign('error_msg', "Object created ".$_REQUEST["objectCreated"]);
	}
	
	global $user;
	global $dbTiki;
	global $categlib;
	include_once ('lib/categories/categlib.php');
	$categlib2 = new CategLib($dbTiki);
	$ctall = $categlib2->get_all_categories_respect_perms($user, 'tiki_p_view_categories');
	
	if (isset ($module_params["type"])) {
		$type = $module_params["type"];
		$urlEnd = "&amp;type=".urlencode($type);
	} else {
		$type = '';
		$urlEnd = "";
	}
	if (isset ($module_params["deep"]))
		$deep = $module_params["deep"];
	else
		$deep = 'on';
	$urlEnd .= "&amp;deep=$deep";
	
	$categId = $workspace["categoryId"];
	
	if ($categId == 0)
		$name = tra("Top");
	else {
		$car = $categlib2->get_category($categId);
		$name = $car["name"];
	}
	
	$selectedCategory = $categId;
	if (isset ($_REQUEST["selectCategoryId"])) {
		$selectedCategory = $_REQUEST["selectCategoryId"];
	}
	//$categObjects = $categlib->get_category_objects($selectedCategory);
	$categObjects = $resourcesLib->get_category_objects($selectedCategory);
	
	foreach ($categObjects as $key => $categObject) {
		$categObjects[$key]["adminURL"] = $resourcesLib->get_url_admin($categObject["objId"], $categObject["name"], $categObject["type"]);
		$categObjects[$key]["removeURL"] = $resourcesLib->get_url_remove($categObject["objId"], $categObject["type"]);
	}
	
	$smarty->assign('categObjects', $categObjects);
	
	$ownurl = $tikilib->httpPrefix().$_SERVER["REQUEST_URI"];
	include_once ('lib/tree/categ_browse_tree.php');
	$imgWiki = "<img align='bottom' border=0 src='img/icons/page.gif'>";
	$imgCateg = "<img border=0 valign='center' src='images/workspaces/edu_folder_closed.png'>";
	$tree_nodes = array ();
	$descendants = $categlib2->get_category_descendants($categId);
	$top = $categId;
	$todo = "";
	$selectedCategData = "";
	foreach ($ctall as $c) {
		if ($c["categId"] == $categId) {
			$top = $c["parentId"];
		}
		if ($c["categId"] == $categId || in_array($c["categId"], $descendants)) {
			if ($selectedCategory == $c["categId"]) {
				$smarty->assign('selectedCategory', $c);
				$selectedCategData = $c;
				$class = "categtreeActive";
			} else {
				$class = "categtree";
			}
	
			$tree_nodes[] = array ("id" => $c["categId"], "parent" => $c["parentId"], "data" => '<a class="'.$class.'" href="'.$ownurl.'&selectCategoryId='.$c["categId"].'">'.$imgCateg.'&nbsp;'.$c["name"].'</a><br />');
		}
	
	}
	$tree_nodes[] = array ("id" => '999', "parent" => $top, "data" => '<br />');
	$tm = new CatBrowseTreeMaker("categ");
	$res = $tm->make_tree($top, $tree_nodes);
	$smarty->assign('tree', $res);
	
	
	$smarty->assign('ownurl', $ownurl);
	global $short_date_format;
}
?>