<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once('tiki-setup.php');
require_once('lib/aulawiki/workspacelib.php');
require_once('lib/wiki-plugins/wikiplugin_module.php');

include_once('tiki-jscalendar.php');

$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();

if (!isset($workspace) || $workspace==null || $workspace=="") {
	$smarty->assign('msg', tra("Workspace not found"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["module"])) {
	$smarty->assign('msg', tra("Module not set"));
	$smarty->display("error.tpl");
	die;
}


$smarty->assign_by_ref('modulecode',wikiplugin_module(null, array("np"=>"0","module"=>$_REQUEST["module"])));
$smarty->assign('mid','aulawiki-view_module.tpl');
$smarty->display('tiki.tpl');
?>