<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-modules.php,v 1.26 2004-01-06 20:46:54 gravesweeper Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once (TIKI_LIB_PATH.'/usermodules/usermoduleslib.php');
include_once('tiki-module_controls.php');

clearstatcache();
$now = date("U");

if ($user != 'admin') {
    $user_groups = $userlib->get_user_groups($user);
} else {
    $allgroups = $userlib->get_groups(0, -1, 'groupName_desc', '');

    $user_groups = array();

    foreach ($allgroups["data"] as $grp) {
        $user_groups[] = $grp["groupName"];
    }
}

if ($user_assigned_modules == 'y' && $tiki_p_configure_modules == 'y' && $user && $usermoduleslib->user_has_assigned_modules($user))
    {
    $left_modules = $usermoduleslib->get_assigned_modules_user($user, 'l');

    $right_modules = $usermoduleslib->get_assigned_modules_user($user, 'r');
} else {
    $left_modules = $tikilib->get_assigned_modules('l', 'y');

    $right_modules = $tikilib->get_assigned_modules('r', 'y');
}

//var_dump($left_modules);
for ($i = 0; $i < count($left_modules); $i++) {
    $r = &$left_modules[$i];

    $pass = 'y';

    if ($modallgroups != 'y') {
        // Check for the right groups
        if ($r["groups"]) {
            $module_groups = unserialize($r["groups"]);
        } else {
            $module_groups = array();
        }

        $pass = 'n';

        if($modseparateanon !== 'y') { // normal case
            foreach ($module_groups as $mod_group) {
                if (in_array($mod_group, $user_groups)) {
                    $pass = 'y';
                    break; // no need to continue looping
                }
            }
        } else {
            // for anon: if module is allowed for anon => display
            if(!$user) { // anon
                if(in_array("Anonymous", $module_groups)) {
                    $pass = 'y';
                }
            // for reg user: ignore anon modules, if user-groups matches => display
            } else { // reg user
                foreach($module_groups as $mod_group) {
                    // don't display anon mods to reg users but continue checking
                    if($mod_group === "Anonymous") { continue; }

                    // Module is for other than anon group
                    if(in_array($mod_group,$user_groups)) {
                        // other group matches one user-group => display
                        $pass = 'y';
                        break; // no need to continue checking
                    }
                }
            }
        }
    }

    if ($pass == 'y') {
        $cachefile = 'modules/cache/' . $tikidomain . 'mod-' . $r["name"] . '.tpl.'.$language.'.cache';
// The cache name depending on the language is a quick fix
// the cache is here to avoid calls to consumming queries, but a module is different for each language because of the strings

        $phpfile = 'modules/mod-' . $r["name"] . '.php';
        $template = 'modules/mod-' . $r["name"] . '.tpl';
        $nocache = TIKI_TEMPLATES_PATH.'/modules/mod-' . $r["name"] . '.tpl.nocache';

        //print("Cache: $cachefile PHP: $phpfile Template: $template<br/>");
        if (!$r["rows"])
            $r["rows"] = 10;

        $module_rows = $r["rows"];
        parse_str($r["params"], $module_params);

        //$mnm = $r["name"]."_module_title";
        //$smarty->assign_by_ref($mnm,$r["title"]);
        $smarty->assign_by_ref('module_rows',$r["rows"]);
        if ((!file_exists($cachefile)) || (file_exists($nocache)) || (($now - filemtime($cachefile)) > $r["cache_time"])) {
            //print("Refrescar cache<br/>");
            $r["data"] = '';

            if (file_exists(TIKI_TEMPLATES_PATH."/$phpfile")) {
                //print("Haciendo el include<br/>");
                // If we have a php file then use it!
                include_once (TIKI_TEMPLATES_PATH."/$phpfile");
            }

            //print("Template file: $template<br/>");
            if (file_exists(TIKI_TEMPLATES_PATH."/$template")) {
                //print("FETCH<br/>");
                $data = $smarty->fetch($template);
            } else {
                if ($tikilib->is_user_module($r["name"])) {
                    //print("Es user module");
                    $info = $tikilib->get_user_module($r["name"]);

                    // Ahora usar el template de user
                    $smarty->assign_by_ref('user_title', $info["title"]);
                    $smarty->assign_by_ref('user_data', $info["data"]);
                    $smarty->assign_by_ref('user_module_name', $info["name"]);
                    $data = $smarty->fetch(TIKI_TEMPLATES_PATH.'/modules/user_module.tpl');
                }
            }

            $r["data"] = $data;
            if (!file_exists($nocache)) {
                $fp = fopen($cachefile, "w+");
                fwrite($fp, $data, strlen($data));
                fclose ($fp);
            }
        } else {
            //print("Usando cache<br/>");
            $fp = fopen($cachefile, "r");

            $data = fread($fp, filesize($cachefile));
            fclose ($fp);
            $r["data"] = $data;
        }
    }
}

for ($i = 0; $i < count($right_modules); $i++) {
    $r = &$right_modules[$i];

    $pass = 'y';

    if ($modallgroups != 'y') {
        // Check for the right groups
        if ($r["groups"]) {
            $module_groups = unserialize($r["groups"]);
        } else {
            $module_groups = array();
        }

        $pass = 'n';

        if($modseparateanon !== 'y') { // normal case
            foreach ($module_groups as $mod_group) {
                if (in_array($mod_group, $user_groups)) {
                    $pass = 'y';
                    break; // no need to continue looping
                }
            }
        } else {
            // for anon: if module is allowed for anon => display
            if(!$user) { // anon
                if(in_array("Anonymous", $module_groups)) {
                    $pass = 'y';
                }
            // for reg user: ignore anon modules, if user-groups matches => display
            } else { // reg user
                foreach($module_groups as $mod_group) {
                    // don't display anon mods to reg users but continue checking
                    if($mod_group === "Anonymous") { continue; }

                    // Module is for other than anon group
                    if(in_array($mod_group,$user_groups)) {
                        // other group matches one user-group => display
                        $pass = 'y';
                        break; // no need to continue checking
                    }
                }
            }
        }
    }

    if ($pass == 'y') {
        $cachefile = TIKI_MODULES_PATH.'/cache/' . $tikidomain . 'mod-' . $r["name"] . '.tpl.'.$language.'.cache';

        $phpfile = TIKI_TEMPLATES_PATH.'/modules/mod-' . $r["name"] . '.php';
        $template = TIKI_TEMPLATES_PATH.'/modules/mod-' . $r["name"] . '.tpl';
        $nocache = TIKI_TEMPLATES_PATH.'/modules/mod-' . $r["name"] . '.tpl.nocache';

        if (!$r["rows"])
            $r["rows"] = 10;

        $module_rows = $r["rows"];
        parse_str($r["params"], $module_params);

        //print("Cache: $cachefile PHP: $phpfile Template: $template<br/>");
        $smarty->assign_by_ref('module_rows',$r["rows"]);
        if ((!file_exists($cachefile)) || (file_exists($nocache)) || (($now - filemtime($cachefile)) > $r["cache_time"])) {
            $r["data"] = '';

            if (file_exists($phpfile)) {
                //print("Haciendo el include<br/>");
                // If we have a php file then use it!
                include_once ($phpfile);
            }

            //print("Template file: $template<br/>");
            if (file_exists($template)) {
                //print("FETCH<br/>");
                $data = $smarty->fetch($template);
            } else {
                if ($tikilib->is_user_module($r["name"])) {
                    //print("Es user module");
                    $info = $tikilib->get_user_module($r["name"]);

                    // Ahora usar el template de user
                    $smarty->assign_by_ref('user_title', $info["title"]);
                    $smarty->assign_by_ref('user_data', $info["data"]);
                    $data = $smarty->fetch(TIKI_TEMPLATES_PATH.'/modules/user_module.tpl');
                }
            }

            $r["data"] = $data;
            if (!file_exists($nocache)) {
                $fp = fopen($cachefile, "w+");
                fwrite($fp, $data, strlen($data));
                fclose ($fp);
            }
        } else {
            //print("Usando cache<br/>");
            $fp = fopen($cachefile, "r");

            $data = fread($fp, filesize($cachefile));
            fclose ($fp);
            $r["data"] = $data;
        }
    }
}

//
// ATTENTION: Quick hack: remove modules not assigned to Anonymous group
//            if unregistered user here... (I'll send details to list soon)
//if ($user)
//{
$smarty->assign_by_ref('right_modules', $right_modules);
$smarty->assign_by_ref('left_modules', $left_modules);
/*}
else
{
  $rm = array();
  foreach ($right_modules as $r)
    if (strstr($r["module_groups"], "Anonymous"))
      $rm[] = $r;

  $lm = array();
  foreach ($left_modules as $r)
    if (strstr($r["module_groups"], "Anonymous"))
      $lm[] = $r;

  $smarty->assign_by_ref('right_modules',$rm);
  $smarty->assign_by_ref('left_modules',$lm);
}*/

?>
