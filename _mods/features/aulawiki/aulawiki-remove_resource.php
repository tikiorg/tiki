<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once ('tiki-setup.php');
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once ('lib/aulawiki/workspacelib.php');
include_once ('lib/aulawiki/wstypeslib.php');
include_once ('lib/aulawiki/categutillib.php');

global $dbTiki;
global $userlib;
global $feature_phplayers;

$workspacesLib = new WorkspaceLib($dbTiki);
$categUtil = new CategUtilLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('msg', tra("Workspace not selected"));
	$smarty->display("error.tpl");
	die;
}

if (!isset ($_REQUEST["objectId"]) && isset ($_REQUEST["createObject"]) && isset ($_REQUEST["createObjectCategoryId"])) {
	if (!isset ($_REQUEST["createObjectName"])) {
		$smarty->assign('module_error', tra("Name requiered"));
	} else {
		//$wsTypesLib = new WorkspaceTypesLib($dbTiki);

		$wsType = $workspace["type"];

		$wscode = "";
		$wscode = $workspace["code"];
		$id = $categUtil->create_object($wscode."-".$_REQUEST["createObjectName"], $_REQUEST["createObjectDesc"], $_REQUEST["createObjectType"], $_REQUEST["createObjectCategoryId"]);
		$workspacesLib->assign_permissions($wscode, $wsType["roles"], $_REQUEST["createObjectType"], $id);

		$categUtil->redirect($id, $_REQUEST["createObjectName"], $_REQUEST["createObjectType"]);
		$smarty->assign('module_error', $tikilib->httpPrefix()."/tiki-index.php");
	}
}
elseif (isset ($_REQUEST["objectCreated"])) {
	$smarty->assign('module_error', "Object created ".$_REQUEST["objectCreated"]);
}

global $user;
global $categlib;
include_once ('lib/categories/categlib.php');
$ctall = $categlib->get_all_categories_respect_perms($user, 'tiki_p_view_categories');

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
	$car = $categlib->get_category($categId);
	$name = $car["name"];
}

$selectedCategory = $categId;
if (isset ($_REQUEST["selectCategoryId"])) {
	$selectedCategory = $_REQUEST["selectCategoryId"];
}
$categObjects = $categlib->get_category_objects($selectedCategory);
//print_r($categObjects);
foreach ($categObjects as $key => $categObject) {
	$categObjects[$key]["adminURL"] = $categUtil->get_url_admin($categObject["objId"], $categObject["name"], $categObject["type"]);
}

$smarty->assign('categObjects', $categObjects);

$ownurl = $tikilib->httpPrefix().$_SERVER["REQUEST_URI"];
include_once ('lib/tree/categ_browse_tree.php');
$imgWiki = "<img align='bottom' border=0 src='img/icons/page.gif'>";
$imgCateg = "<img border=0 valign='center' src='images/aulawiki/edu_folder_closed.png'>";
$tree_nodes = array ();
$descendants = $categlib->get_category_descendants($categId);
$top = $categId;
$todo = "";
$selectedCategData = "";
foreach ($ctall as $c) {
	if ($c["categId"] == $categId) {
		$top = $c["parentId"];
	}
	if ($c["categId"] == $categId || in_array($c["categId"], $descendants)) {
		/*$objects = $categlib->get_category_objects($c["categId"]);
		
		if (isset($objects)){
			foreach($objects as $obj){
				$imgObject="<img align='bottom' border=0 src='img/icons/edu_".str_replace(" ","",$obj["type"]).".gif'>";
				$tree_nodes[] = array(
				"id" => $c["categId"].'-'.$obj["catObjectId"],
				"parent" => $c["categId"],
				"data" => '<a class="categtree" href="'.$obj["href"].'">' .$imgObject.'&nbsp;'. $obj['name'] . '</a><br />'
				);
			}
		}*/
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
$types = array ("article" => "Article", "blog" => "Blog", "category" => "Category", "directory" => "Directorie", "faq" => "FAQs", "file gallery" => "File Gallerie", "forum" => "Forum", "image gallery" => "Image Gallerie", "newsletter" => "Newsletter", "poll" => "Poll", "quiz" => "Quizze", "structure" => "Structure", "survey" => "Survey", "tracker" => "Tracker", "wiki page" => "Wiki Page", "image" => "Image");
$smarty->assign('types', $types);
//}

$smarty->assign('ownurl', $ownurl);
global $short_date_format;

//}
?>


