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

include_once ("lib/imagegals/imagegallib.php");
require_once ('lib/aulawiki/categutillib.php');
require_once ('lib/aulawiki/workspacelib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
	$categUtil = new CategUtilLib($dbTiki);
	$imageGals = $categUtil->get_category_objects($workspace["categoryId"], null, "image gallery");
	
	$images = array ();
	foreach ($imageGals as $key => $imgGal) {
		$ranking = $imagegallib->list_images(0, 1, 'created_desc', '', $imgGal["objId"]);
		if ($ranking["data"] != null && count($ranking["data"])) {
			$images[$imgGal["objId"]] = $ranking["data"][0];
		}
	}
	
	$smarty->assign('modLastImages', $images);
	$smarty->assign('nonums', isset ($module_params["nonums"]) ? $module_params["nonums"] : 'n');
	$smarty->assign('imageGals', $imageGals);
}
?>