<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-modules.php,v 1.69.2.9 2008-03-10 05:43:16 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
global $access; require_once('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $usermoduleslib; include_once ('lib/usermodules/usermoduleslib.php');
include_once('tiki-module_controls.php');
global $prefs, $user, $userlib, $tiki_p_configure_modules, $smarty, $tikidomain, $tikilib, $section, $page;

clearstatcache();

if ($user != 'admin') {
	$user_groups = $tikilib->get_user_groups($user);
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

if ($prefs['user_assigned_modules'] == 'y' && $tiki_p_configure_modules == 'y' && $user && $usermoduleslib->user_has_assigned_modules($user)) {
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
$show_columns[$these_modules_name] = 'n';
for ($mod_counter = 0; $mod_counter < $temp_max; $mod_counter++) {

	$mod_reference = &$these_modules[$mod_counter];
	parse_str($mod_reference["params"], $module_params);
	if (!isset($module_params['decorations'])) $module_params['decorations'] = 'y';
	if (isset($prefs['user_flip_modules']) && $prefs['user_flip_modules'] != 'module')
		$module_params['flip'] = $prefs['user_flip_modules'];
	elseif (!isset($module_params['flip']))
		$module_params['flip'] = 'n';
	if (!isset($module_params['overflow'])) $module_params['overflow'] = 'n';
	if (!isset($module_params['nobox'])) $module_params['nobox'] = 'n';
	if (isset($module_params['section']) && $module_params['section'] == 'wiki' && $section == 'wiki page') $module_params['section'] = 'wiki page';
	$pass = 'y';
	if (isset($module_params["lang"]) && ((gettype($module_params["lang"]) == "array" && !in_array($prefs['language'], $module_params["lang"])) ||  (gettype($module_params["lang"]) == "string" && $module_params["lang"] != $prefs['language']))) {
		$pass="n";
	} elseif (isset($module_params['section']) && (!isset($section) || $section != $module_params['section'])) {
		$pass = 'n';
	} elseif (isset($module_params['page']) && (!isset($section) || $section != 'wiki page' || !isset($page) || $page != $module_params['page'])) {
		$pass = 'n';
	} elseif (isset($module_params['theme'])) {
		global $tc_theme;
		if (substr($module_params['theme'],0,1) != '!') { // usual behavior
			if (isset($tc_theme) && $tc_theme > '' && $module_params['theme'] != $tc_theme) {
				$pass = 'n';
			} elseif ($module_params['theme'] != $prefs['style'] && (!isset($tc_theme) || $tc_theme == '')) {
				$pass = 'n';
			}
		} else { // negation behavior
			$excluded_theme = substr($module_params['theme'],1);
			if (isset($tc_theme) && $tc_theme > '' && $excluded_theme == $tc_theme) {
				$pass = 'n';
			} elseif ($excluded_theme == $prefs['style'] && (!isset($tc_theme) || $tc_theme == '')) {
				$pass = 'n';
			}
		}
	} elseif ($prefs['modallgroups'] != 'y') {
		if ($mod_reference["groups"]) {
			$module_groups = unserialize($mod_reference["groups"]);
		} else {
			$module_groups = array();
		}
		$pass = 'n';
		if ($prefs['modseparateanon'] !== 'y') {
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
	if ($pass == 'y' && isset($module_params['creator']) && $section == 'wiki page' && isset($page)) {
		if (!$page_info = $tikilib->get_page_info($page)) {
			$pass = 'n';
		} elseif (($module_params['creator'] == 'y' && $page_info['creator'] != $user) || ($module_params['creator'] == 'n' && $page_info['creator'] == $user)) {
			$pass = 'n';
		}
	}
	if ($pass == 'y' && isset($module_params['contributor'])  && $section == 'wiki page' && isset($page)) {
		global $wikilib; include_once('lib/wiki/wikilib.php');
		if (!$page_info = $tikilib->get_page_info($page)) {
			$pass = 'n';
		} else {
			$contributors = $wikilib->get_contributors($page);
			$contributors[] = $page_info['creator'];
			$in = in_array($user, $contributors);
			if (($module_params['contributor'] == 'y' && !$in) || ($module_params['contributor'] == 'n' && $in)) {
				$pass = 'n';
			}
		}
	}
	if ($pass == 'y') {
		if (isset($module_params['title'])) { 
			$smarty->assign('tpl_module_title',tra($module_params['title'])); 
		} else {
			$smarty->clear_assign('tpl_module_title');
		}
		$show_columns[$these_modules_name] = 'y';
		$template = 'modules/mod-' . $mod_reference["name"] . '.tpl';
		$phpfile = 'modules/mod-' . $mod_reference["name"] . '.php';

		if (!$mod_reference["rows"]) {
			$mod_reference["rows"] = 10;
		}
		$module_rows = $mod_reference["rows"];
		$smarty->assign_by_ref('module_rows',$mod_reference["rows"]);
			$mod_reference["data"] = '';
            $smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params
			$smarty->assign('module_ord', $mod_reference['ord']);
			$smarty->assign('module_position', $mod_reference['position']);
			$smarty->assign('moduleId', $mod_reference['moduleId']);
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
$smarty->assign_by_ref('show_columns', $show_columns);
} // end foreach
$module_nodecorations = array('decorations' => 'n');
$module_isflippable = array('flip' => 'y');
$smarty->assign('module_nodecorations', $module_nodecorations);
$smarty->assign('module_isflippable', $module_isflippable);
?>
