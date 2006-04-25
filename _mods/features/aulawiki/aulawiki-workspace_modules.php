<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
if (strpos($_SERVER["SCRIPT_NAME"],"aulawiki-workspace_modules.php")!=FALSE) {
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra("This script cannot be called directly"));
  $smarty->display("error.tpl");
  die;
}

include_once ('lib/aulawiki/workspacemoduleslib.php');
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

// additional module zones added to this array will be exposed to tiki.tpl
// TODO change modules user interface to enable additional zones
$workspace_module_zones = array();
$workspace_module_zones['l'] = 'workspace_left_modules';
$workspace_module_zones['r'] = 'workspace_right_modules';


if ($workspaceId && $wsmoduleslib->workspace_has_assigned_modules($workspaceId,$wsmodtype)) {
    foreach ( $workspace_module_zones as $zone=>$zone_name ) {
        $$zone_name = $wsmoduleslib->get_workspace_assigned_modules_pos($workspaceId,$wsmodtype, $zone);
    }
} 

foreach ( array('workspace_left_modules', 'workspace_right_modules') as $these_modules_name ) {

$workspace_these_modules =& $$these_modules_name;
$temp_max = count($workspace_these_modules);

for ($mod_counter = 0; $mod_counter < $temp_max; $mod_counter++) {
	$smarty->assign('module_params',array()); // ensure params not available outside current module
    $smarty->assign('error_msg',"");
	$mod_reference = &$workspace_these_modules[$mod_counter];
	$smarty->assign('moduleId',$mod_reference["moduleId"]);
	parse_str($mod_reference["params"], $module_params);

	$pass = 'y';
	if (isset($module_params["lang"]) && ((gettype($module_params["lang"]) == "array" && !in_array($language, $module_params["lang"])) ||  (gettype($module_params["lang"]) == "string" && $module_params["lang"] != $language))) {
		$pass="n";
	}
	elseif (isset($mod_reference["groups"]) && $mod_reference["groups"]!="") {
		if ($mod_reference["groups"]) {
			$module_groups = unserialize($mod_reference["groups"]);
		} else {
			$module_groups = array();
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
		$phpfile = 'modules/mod-' . $mod_reference["name"] . '.php';
		$template = 'modules/mod-' . $mod_reference["name"] . '.tpl';
		$nocache = 'templates/modules/mod-' . $mod_reference["name"] . '.tpl.nocache';
		if (!$mod_reference["rows"]) {
			$mod_reference["rows"] = 10;
		}
		$module_rows = $mod_reference["rows"];
		$smarty->assign_by_ref('module_rows',$mod_reference["rows"]);
		$mod_reference["data"] = '';
            $smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params
            if($mod_reference["title"]!=""){
            	$smarty->assign_by_ref('title', tra($mod_reference["title"]));
            }
			if (file_exists($phpfile)) {
				include ($phpfile);
			}
			if (file_exists("templates/".$template)) {
				$data = $smarty->fetch($template);
			} else {
				if ($tikilib->is_user_module($mod_reference["name"])) {
					$info = $tikilib->get_user_module($mod_reference["name"]);
					$smarty->assign_by_ref('user_title', tra($info["title"]));
					if (isset($info['parse']) && $info["parse"] == 'y')
						$info["data"] = $tikilib->parse_data($info["data"]);
					$smarty->assign_by_ref('user_data', $info["data"]);
					$smarty->assign_by_ref('user_module_name', $info["name"]);
					$data = $smarty->fetch('modules/user_module.tpl');
				} else {
					$data = '';
				}
			}
            unset($info); // clean up when done
			$mod_reference["data"] = $data;

	}
} // end for
$smarty->assign_by_ref($these_modules_name, $workspace_these_modules);
} // end foreach
/*$module_nodecorations = array('decorations' => 'n');
$module_isflippable = array('flip' => 'y');
$smarty->assign('module_nodecorations', $module_nodecorations);
$smarty->assign('module_isflippable', $module_isflippable);*/
?>