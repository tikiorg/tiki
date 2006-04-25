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

require_once ('lib/aulawiki/categutillib.php');
include_once ('lib/aulawiki/workspacelib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
		
	$categUtil = new CategUtilLib($dbTiki);
	$fileGals = $categUtil->get_category_objects($workspace["categoryId"],null,"file gallery");
	
	$files = array();
	foreach ($fileGals as $key => $fileGal) {
		$ranking = $tikilib->get_files(0, 1, 'created_desc', '', $fileGal["objId"]);
		if ($ranking["data"]!=null && count($ranking["data"])){
			$files[$fileGal["objId"]]=$ranking["data"][0];
		}
	}
	$smarty->assign('modLastFiles', $files);
	$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
	$smarty->assign('fileGals',$fileGals);
}
?>