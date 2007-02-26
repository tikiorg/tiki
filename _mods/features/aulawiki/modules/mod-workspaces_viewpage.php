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
include_once('lib/wiki/wikilib.php');
global $categlib;
include_once('lib/categories/categlib.php');
global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

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

$smarty->assign('pageBody', "");
$smarty->assign('pageName', "");
$smarty->assign('likepages', "");

if (!$exit_module && isset($module_params["name"]) && $module_params["name"]!=""){
	
	$page = str_replace ("%WSCODE%",$workspace["code"],$module_params["name"]);
	$info = $tikilib->get_page_info($page);
	if (!isset ($info) || $info == "") {
		$page = $workspace["code"]."-".$module_params["name"];
		$info = $tikilib->get_page_info($page);
	}
	if (isset ($info) && $info != "") {
		//TODO: BAD PERFORMANCE!!! 322 database querys for 2 pages
		//require('tiki-pagesetup.php');
	
		// Check to see if page is categorized
		$objId = urldecode($page);
		/*if ($tiki_p_admin != 'y' && $feature_categories == 'y' && !$object_has_perms) {
			// Check to see if page is categorized
			$perms_array = $categlib->get_object_categories_perms($user, 'wiki page', $objId);
		   	if ($perms_array) {
		   		$is_categorized = TRUE;
		    	foreach ($perms_array as $perm => $value) {
		    		$$perm = $value;
		    	}
		   	} else {
		   		$is_categorized = FALSE;
		   	}

			if ($is_categorized && isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y'){
				$exit_module = true;
			}
				
		}
		
		if($tiki_p_admin != 'y' && $tiki_p_view != 'y') {
			$exit_module = true;
		}*/
		$exit_module = false;
		if ($exit_module){
				$smarty->assign('error_msg', tra("Permission denied you cannot view this page"));
				$exit_module = true;
		
		}else{
			//Navigation History
			if (isset($_SESSION["module_history_".$mod_reference["moduleId"]])){
				$history_size = count($_SESSION["module_history_".$mod_reference["moduleId"]]);
				if ($history_size>0 && $_SESSION["module_history_".$mod_reference["moduleId"]][$history_size-1]!=$module_params["name"]){
					$_SESSION["module_history_".$mod_reference["moduleId"]][] = $module_params["name"];
				}
			}else{
				$_SESSION["module_history_".$mod_reference["moduleId"]] = array();
				$_SESSION["module_history_".$mod_reference["moduleId"]][] = $module_params["name"];
			}
			$pdata = $tikilib->parse_data($info["data"], $info["is_html"]);
	
			$pdata = str_replace ("tiki-index.php?page=", $ownurl."&amp;name=", $pdata); 
			$smarty->assign('pageBody', $pdata);
			$smarty->assign('pageName', $page);
		}
	} else {
		$likepages = $wikilib->get_like_pages($module_params["name"]);
		$smarty->assign('likepages', $likepages);
		$smarty->assign('error_msg', tra("Page not found"." ".$page));
	}
}elseif (!$exit_module){
	$smarty->assign('error_msg', tra("Param name not found"));
	$exit_module = true;
}
$smarty->assign('showBar', isset ($module_params["showBar"]) ? $module_params["showBar"] : 'y');
$smarty->assign('module_history', isset ($_SESSION["module_history_".$mod_reference["moduleId"]]) ? $_SESSION["module_history_".$mod_reference["moduleId"]] : "");

?>