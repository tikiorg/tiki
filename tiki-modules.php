<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-modules.php,v 1.55 2006-12-23 15:15:41 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
global $access; require_once('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $usermoduleslib; include_once ('lib/usermodules/usermoduleslib.php');
include_once('tiki-module_controls.php');
global $modseparateanon, $user, $userlib, $user_assigned_modules, $tiki_p_configure_modules;
global $language, $modallgroups, $smarty, $tikidomain, $tikilib, $section;

clearstatcache();
$now = date("U");

if ($user != 'admin') {
	$user_groups = $userlib->get_user_groups($user);
} else {
	$allgroups = $userlib->list_all_groups();
	$user_groups = array();
	foreach ($allgroups as $grp) {
		$user_groups[] = $grp;
	}
}

// additional module zones added to this array will be exposed to tiki.tpl
// TODO change modules user interface to enable additional zones
$module_zones = array();
$module_zones['l'] = 'left_modules';
$module_zones['r'] = 'right_modules';

if ($user_assigned_modules == 'y' && $tiki_p_configure_modules == 'y' && $user && $usermoduleslib->user_has_assigned_modules($user)) {
    foreach ( $module_zones as $zone=>$zone_name ) {
        $$zone_name = $usermoduleslib->get_assigned_modules_user($user, $zone);
    }
} else {
    foreach ( $module_zones as $zone=>$zone_name ) {
    	$$zone_name = $tikilib->get_assigned_modules($zone, 'y');
    }
}

foreach ( array('left_modules', 'right_modules') as $these_modules_name ) {
// note indent missing to preserve CVS history
$these_modules =& $$these_modules_name;
$temp_max = count($these_modules);
for ($mod_counter = 0; $mod_counter < $temp_max; $mod_counter++) {

	$mod_reference = &$these_modules[$mod_counter];
	parse_str($mod_reference["params"], $module_params);
	if (!isset($module_params['decorations'])) $module_params['decorations'] = 'n';
	if (!isset($module_params['flip'])) $module_params['flip'] = 'n';
	if (!isset($module_params['overflow'])) $module_params['overflow'] = 'n';
	$pass = 'y';
	if (isset($module_params["lang"]) && ((gettype($module_params["lang"]) == "array" && !in_array($language, $module_params["lang"])) ||  (gettype($module_params["lang"]) == "string" && $module_params["lang"] != $language))) {
		$pass="n";
	} elseif (isset($module_params['section']) && (!isset($section) || $section != $module_params['section'])) {
		$pass = 'n';
	}
	elseif ($modallgroups != 'y') {
		if ($mod_reference["groups"]) {
			$module_groups = unserialize($mod_reference["groups"]);
		} else {
			$module_groups = array();
		}
		$pass = 'n';
		if ($modseparateanon !== 'y') {
			foreach ($module_groups as $mod_group) {
				if (in_array($mod_group, $user_groups)) {
					$pass = 'y';
					break; 
				}
			}
		} else {
			if(!$user) { 
				if (in_array("Anonymous", $module_groups)) {
					$pass = 'y';
				}
			} else { 
				foreach ($module_groups as $mod_group) {
					if ($mod_group === "Anonymous") { 
						continue; 
					}
					if (in_array($mod_group,$user_groups)) {
						$pass = 'y';
						break;
					}
				}
			}
		}
	}
	if ($pass == 'y') {
// Commented out here too.  See zaufi's note in lib/wiki-plugins/wikiplugin_module.php	
//		$cachefile = 'modules/cache/';
//		if ($tikidomain) { $cachefile.= "$tikidomain/"; }
//		$cachefile.= 'mod-' . $mod_reference["name"] . '.tpl.'.$language.'.cache';
//		$nocache = 'templates/modules/mod-' . $mod_reference["name"] . '.tpl.nocache';
		$template = 'modules/mod-' . $mod_reference["name"] . '.tpl';
		$phpfile = 'modules/mod-' . $mod_reference["name"] . '.php';

		if (!$mod_reference["rows"]) {
			$mod_reference["rows"] = 10;
		}
		$module_rows = $mod_reference["rows"];
		$smarty->assign_by_ref('module_rows',$mod_reference["rows"]);
//		if ((!file_exists($cachefile)) || (file_exists($nocache)) || (($now - filemtime($cachefile)) >= $mod_reference["cache_time"])) {
			$mod_reference["data"] = '';
            $smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params
			if (file_exists($phpfile)) {
				include ($phpfile);
			}
			if (file_exists("templates/".$template)) {
				$data = $smarty->fetch($template);
			} else {
				if ($tikilib->is_user_module($mod_reference["name"])) {
					$info = $tikilib->get_user_module($mod_reference["name"]);
					$smarty->assign('user_title', tra($info["title"]));
					if (isset($info['parse']) && $info["parse"] == 'y')
						$info["data"] = $tikilib->parse_data($info["data"]);
					$smarty->assign_by_ref('user_data', $info["data"]);
					$smarty->assign_by_ref('user_module_name', $info["name"]);
					$data = $smarty->fetch('modules/user_module.tpl');
				} else {
					$data = '';
				}
			}
            $smarty->assign('module_params',array()); // ensure params not available outside current module
            unset($info); // clean up when done
			$mod_reference["data"] = $data;
//			if (!file_exists($nocache)) {
//				$fp = fopen($cachefile, "w+");
//				fwrite($fp, $data, strlen($data));
//				fclose ($fp);
//			}
//		} else {
//			$fp = fopen($cachefile, "r");
//			$data = @fread($fp, filesize($cachefile));
//			fclose ($fp);
//			$mod_reference["data"] = $data;
//		}
	}
} // end for
$smarty->assign_by_ref($these_modules_name, $these_modules);
} // end foreach
$module_nodecorations = array('decorations' => 'n');
$module_isflippable = array('flip' => 'y');
$smarty->assign('module_nodecorations', $module_nodecorations);
$smarty->assign('module_isflippable', $module_isflippable);
?>
