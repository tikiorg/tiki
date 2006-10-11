<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once('tiki-setup.php');
require_once('lib/workspaces/workspacelib.php');
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

if (isset ($_SESSION["WorkspaceParams-".$workspace["workspaceId"]]["module_params_".$_REQUEST["module"]])) {
	$module_params = $_SESSION["WorkspaceParams-".$workspace["workspaceId"]]["module_params_".$_REQUEST["module"]];
}

foreach ($_POST as $key => $val) {
		$module_params[$key] = $val;
}

foreach ($_GET as $key => $val) {
		$module_params[$key] = $val;
}

//$smarty->assign_by_ref('modulecode',wikiplugin_module(null, array("np"=>"0","module"=>$_REQUEST["module"])));
$phpfile = 'modules/mod-'.$_REQUEST["module"].'.php';
$template = 'modules/mod-'.$_REQUEST["module"].'.tpl';

if (file_exists($phpfile)) {
	include ($phpfile);
}

if (file_exists("templates/".$template)) {
	$data = $smarty->fetch($template);
}

if (!isset($_SESSION["WorkspaceParams-".$workspace["workspaceId"]])){
	$_SESSION["WorkspaceParams-".$workspace["workspaceId"]] = array();
}

//Read module conf
$phpfile = 'modules/conf/modconf-'.$_REQUEST["module"].'.php';
$conf_params = array();
if (file_exists($phpfile)) {
	include_once ($phpfile);
    $funParams = "module_".$_REQUEST["module"]."_params";
    $conf_params = $funParams();
}
if (isset($conf_params)){
	$param_values = array();
	foreach ($conf_params as $key => $param) {
		if (isset($module_params[$key])){
			$param_values[$key] = $module_params[$key];
		}
	}
	$_SESSION["WorkspaceParams-".$workspace["workspaceId"]]["module_params_".$_REQUEST["module"]] = $param_values;
}

$smarty->assign_by_ref('modulecode',$data);

$smarty->assign('mid','tiki-workspaces_view_module.tpl');
$smarty->display('tiki.tpl');
?>