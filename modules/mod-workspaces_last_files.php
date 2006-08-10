<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once ('lib/workspaces/resourceslib.php');
include_once ('lib/workspaces/workspacelib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
		
	$resourcesLib = new WorkspaceResourcesLib($dbTiki);
	$fileGals = $resourcesLib->get_category_objects($workspace["categoryId"],null,"file gallery");
	if (isset($fileGals) && $fileGals!="" && count($fileGals)>0){
		$selectedGal = $fileGals[0];
		if (isset($module_params["name"]) && $module_params["name"]!=""){
			foreach ($fileGals as $key => $fileGal) {
				if ($fileGal["name"]==$module_params["name"]){
					$selectedGal = $fileGal;
				}
			}
		}
		$files = array();
		//Maximum numer of files per page
		$maxFiles = 3;
		if (isset($module_params["maxFiles"]) && $module_params["maxFiles"]!=""){
			$maxFiles = $module_params["maxFiles"];
		}
		
		//Page Offset
		$offset = 0;
		if (isset($module_params["offset"])){
			$offset = $module_params["offset"];
		}
		$ranking = $tikilib->get_files($offset, $maxFiles, 'created_desc', '', $selectedGal["objId"]);
		
		//Configure Offset
		$cant_pages = ceil($ranking["cant"] / $maxFiles);
		$smarty->assign_by_ref('cant_pages', $cant_pages);
		$smarty->assign('actual_page', 1 + ($offset / $maxFiles));
		
		if ($ranking["cant"] > ($offset + $maxFiles)) {
			$smarty->assign('next_offset', $offset + $maxFiles);
		} else {
			$smarty->assign('next_offset', -1);
		}
		
		// If offset is > 0 then prev_offset
		if ($offset > 0) {
			$smarty->assign('prev_offset', $offset - $maxFiles);
		} else {
			$smarty->assign('prev_offset', -1);
		}
	
		$files = $ranking["data"];
		$smarty->assign_by_ref('selectedGal', $selectedGal);
	}
	/*foreach ($fileGals as $key => $fileGal) {
		$ranking = $tikilib->get_files(0, 1, 'created_desc', '', $fileGal["objId"]);
		if ($ranking["data"]!=null && count($ranking["data"])){
			$files[$fileGal["objId"]]=$ranking["data"][0];
		}
	}*/
	$smarty->assign('showBar', isset ($module_params["showBar"]) ? $module_params["showBar"] : 'y');
	$smarty->assign_by_ref('modLastFiles', $files);
	$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
	$smarty->assign_by_ref('fileGals',$fileGals);
}
?>