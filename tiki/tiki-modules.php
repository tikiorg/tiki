<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-modules.php,v 1.34 2004-03-23 12:01:52 djnz Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

include_once ('lib/usermodules/usermoduleslib.php');
include_once('tiki-module_controls.php');

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
for ($i = 0; $i < count($these_modules); $i++) {
	$r = &$these_modules[$i];
	$pass = 'y';
	if ($modallgroups != 'y') {
		if ($r["groups"]) {
			$module_groups = unserialize($r["groups"]);
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
		$cachefile = 'modules/cache/' . $tikidomain . 'mod-' . $r["name"] . '.tpl.'.$language.'.cache';
		$phpfile = 'modules/mod-' . $r["name"] . '.php';
		$template = 'modules/mod-' . $r["name"] . '.tpl';
		$nocache = 'templates/modules/mod-' . $r["name"] . '.tpl.nocache';
		if (!$r["rows"]) {
			$r["rows"] = 10;
		}
		$module_rows = $r["rows"];
		parse_str($r["params"], $module_params);
		$smarty->assign_by_ref('module_rows',$r["rows"]);
		if ((!file_exists($cachefile)) || (file_exists($nocache)) || (($now - filemtime($cachefile)) > $r["cache_time"])) {
			$r["data"] = '';
			if (file_exists($phpfile)) {
				include ($phpfile);
			}
			if (file_exists("templates/".$template)) {
                $smarty->assign_by_ref('module_params', $module_params);
				$data = $smarty->fetch($template);
                $smarty->assign('module_params',array()); // ensure params not available outside current module
			} else {
				if ($tikilib->is_user_module($r["name"])) {
					$info = $tikilib->get_user_module($r["name"]);
					$smarty->assign_by_ref('user_title', $info["title"]);
					$smarty->assign_by_ref('user_data', $info["data"]);
					$smarty->assign_by_ref('user_module_name', $info["name"]);
					$data = $smarty->fetch('templates/modules/user_module.tpl');
				}
			}
			$r["data"] = $data;
			if (!file_exists($nocache)) {
				$fp = fopen($cachefile, "w+");
				fwrite($fp, $data, strlen($data));
				fclose ($fp);
			}
		} else {
			$fp = fopen($cachefile, "r");
			$data = fread($fp, filesize($cachefile));
			fclose ($fp);
			$r["data"] = $data;
		}
	}
} // end for
$smarty->assign_by_ref($these_modules_name, $these_modules);
} // end foreach

?>