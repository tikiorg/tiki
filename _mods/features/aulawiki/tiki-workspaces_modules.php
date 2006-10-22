<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
if (strpos($_SERVER["SCRIPT_NAME"], "aulawiki-workspace_modules.php") != FALSE) {
	require_once ('tiki-setup.php');
	$smarty->assign('msg', tra("This script cannot be called directly"));
	$smarty->display("error.tpl");
	die;
}

include_once ('lib/workspaces/workspacemoduleslib.php');
//include_once('tiki-module_controls.php');
global $modseparateanon;
global $language;
global $workspaceId;
global $wsmodtype;

//clearstatcache();
$now = date("U");

//La variable $user_groups tiene que llegar cargada desde la pagina llamante

/*$allgroups = $userlib->list_all_groups();
	$user_groups = array();
	foreach ($allgroups as $grp) {
		$user_groups[] = $grp;
	}
*/

$workspace = $workspacesLib->get_current_workspace();

$zones = $wsmoduleslib->get_zones($workspaceId, $wsmodtype);

$activeZone = "";

if (isset($_REQUEST["zoneId"]) && isset($zones[$_REQUEST["zoneId"]]) && $zones[$_REQUEST["zoneId"]]!=""){
	$activeZone = $zones[$_REQUEST["zoneId"]];
}elseif(isset($zones) && count($zones)>0){
	$zonevalues = array_values($zones);
	$activeZone = $zonevalues[0];
}
$smarty->assign_by_ref('zones',$zones);
$smarty->assign_by_ref('activeZone',$activeZone);

if (isset($activeZone) && $activeZone!=""){
	$modules = $wsmoduleslib->get_ws_assigned_modules_by_cols($activeZone["zoneId"]);
}else{
	$modules = array();
}

$max_columns = array_pop(array_keys($modules));

$ownurlWS = $tikilib->httpPrefix().$_SERVER["REQUEST_URI"];
$pos = strpos($ownurlWS, "moduleId");
if ($pos) {
	$ownurlWS = substr($ownurlWS, 0, $pos -1);
}

if (!strstr($ownurlWS, 'workspaceId')) {
	if (strstr($ownurlWS, '?')) {
		$ownurlWS = $ownurlWS.'&amp;workspaceId='.$workspace["workspaceId"];
	} else {
		$ownurlWS = $ownurlWS.'?workspaceId='.$workspace["workspaceId"];
	}
}
$separator = '&amp;';
$smarty->assign('$ownurlWS', $ownurlWS);

foreach ($modules as $colid => $workspace_these_modules) {

	$temp_max = count($workspace_these_modules);

	for ($mod_counter = 0; $mod_counter < $temp_max; $mod_counter ++) {
		$smarty->assign('module_params', array ()); // ensure params not available outside current module
		$smarty->assign('error_msg', "");
		$mod_reference = & $workspace_these_modules[$mod_counter];
		$smarty->assign('moduleId', $mod_reference["moduleId"]);
		
		$ownurl = $ownurlWS.$separator."moduleId=".$mod_reference["moduleId"];
		$smarty->assign('ownurl', $ownurl);
		
		$module_params = array();
		parse_str($mod_reference["params"], $module_params);

		if (isset($workspace)){
			foreach ($module_params as $key => $param) {
				$module_params[$key] = str_replace ("%WSCODE%",$workspace["code"],$param);
			}
		}

		if (isset ($_SESSION["WorkspaceParams-".$workspace["workspaceId"]]["module_params_".$mod_reference["moduleId"]])) {
			$module_params = $_SESSION["WorkspaceParams-".$workspace["workspaceId"]]["module_params_".$mod_reference["moduleId"]];
		}

		if ((isset ($_GET["moduleId"]) && $_GET["moduleId"] == $mod_reference["moduleId"]) || (isset ($_POST["moduleId"]) && $_POST["moduleId"] == $mod_reference["moduleId"])) {
			foreach ($_POST as $key => $val) {
				$module_params[$key] = $val;
			}

			foreach ($_GET as $key => $val) {
				$module_params[$key] = $val;
			}
			//print_r($module_params);
		}

		$pass = 'y';
		if (isset ($module_params["lang"]) && ((gettype($module_params["lang"]) == "array" && !in_array($language, $module_params["lang"])) || (gettype($module_params["lang"]) == "string" && $module_params["lang"] != $language))) {
			$pass = "n";
		}
		elseif (isset ($mod_reference["groups"]) && $mod_reference["groups"] != "") {
			if ($mod_reference["groups"]) {
				$module_groups = unserialize($mod_reference["groups"]);
			} else {
				$module_groups = array ();
			}
			$pass = 'n';
			foreach ($module_groups as $mod_group) {
				if (in_array($mod_group, $user_groups)) {
					$pass = 'y';
					break;
				}
			}
		}
		if ($pass == 'y') {
			$phpfile = 'modules/mod-'.$mod_reference["name"].'.php';
			$template = 'modules/mod-'.$mod_reference["name"].'.tpl';
			$nocache = 'templates/modules/mod-'.$mod_reference["name"].'.tpl.nocache';
			if (!$mod_reference["rows"]) {
				$mod_reference["rows"] = 10;
			}
			$module_rows = $mod_reference["rows"];
			$smarty->assign_by_ref('module_rows', $mod_reference["rows"]);
			$mod_reference["data"] = '';
			$smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params
			if ($mod_reference["title"] != "") {
				$smarty->assign('title', tra($mod_reference["title"]));
			}
			$smarty->assign('style_title', $mod_reference["style_title"]);
			$smarty->assign('style_data', $mod_reference["style_data"]);

			if (file_exists($phpfile)) {
				include ($phpfile);
			}
			if (file_exists("templates/".$template)) {
				$data = $smarty->fetch($template);
			} else {
				if ($tikilib->is_user_module($mod_reference["name"])) {
					$info = $tikilib->get_user_module($mod_reference["name"]);
					$smarty->assign_by_ref('user_title', tra($info["title"]));
					if (isset ($info['parse']) && $info["parse"] == 'y')
						$info["data"] = $tikilib->parse_data($info["data"]);
					$smarty->assign_by_ref('user_data', $info["data"]);
					$smarty->assign_by_ref('user_module_name', $info["name"]);
					$data = $smarty->fetch('modules/user_module.tpl');
				} else {
					$data = '';
				}
			}
			unset ($info); // clean up when done
			$mod_reference["data"] = $data;
			if (!isset($_SESSION["WorkspaceParams-".$workspace["workspaceId"]])){
				$_SESSION["WorkspaceParams-".$workspace["workspaceId"]] = array();
			}
			
			//Read module conf
			$phpfile = 'modules/conf/modconf-'.$mod_reference["name"].'.php';
			$conf_params = array();
			if (file_exists($phpfile)) {
				include_once ($phpfile);
		        $funParams = "module_".$mod_reference["name"]."_params";
		        $conf_params = $funParams();
			}
			if (isset($conf_params)){
				$param_values = array();
				foreach ($conf_params as $key => $param) {
					if (isset($module_params[$key])){
						$param_values[$key] = $module_params[$key];
					}
				}
				if (isset($mod_reference["moduleId"]) && $mod_reference["moduleId"]!="" && isset($param_values) && $param_values!=""){
					$_SESSION["WorkspaceParams-".$workspace["workspaceId"]]["module_params_".$mod_reference["moduleId"]] = $param_values;
				}
			}
		}
		$modules[$colid] = $workspace_these_modules;
	} // end for

	//$smarty->assign_by_ref($these_modules_name, $workspace_these_modules);
} // end foreach

//Break columns
$modulegroups = array ();
foreach ($modules as $keycol => $column) {
	$modgroup = 1;
	foreach ($column as $modkey => $module) {
		if (!isset ($modulegroups[$modgroup])) {
			$modulegroups[$modgroup] = array ();
		}
		if (!isset ($modulegroups[$modgroup][$keycol])) {
			$modulegroups[$modgroup][$keycol] = array ();
		}
		//print_r($module);
		if ($module["name"] == "workspaces_break") {
			$modgroup += 1;
		} else {
			$modulegroups[$modgroup][$keycol][] = $module;
		}
	}
}
$smarty->assign_by_ref("modulegroups", $modulegroups);

if (isset($activeZone["name"]))
 $zonename = $activeZone["name"];
else
 $zonename ="";
$smarty->assign('title', $workspace["name"]." : ".$zonename);
/*$module_nodecorations = array('decorations' => 'n');
$module_isflippable = array('flip' => 'y');
$smarty->assign('module_nodecorations', $module_nodecorations);
$smarty->assign('module_isflippable', $module_isflippable);*/
?>