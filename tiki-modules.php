<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-modules.php,v 1.20 2003-11-23 01:55:54 zaufi Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

include_once ('lib/usermodules/usermoduleslib.php');
include_once('tiki-module_controls.php');

//
//
//
function process_modules(&$modules)
{
    global $modseparateanon;
    global $modallgroups;
    global $smarty;
    global $user;
    global $userlib;
    global $tikidomain;
    global $language;
    global $tikilib;
    //
    if ($user != 'admin') $user_groups = $userlib->get_user_groups($user);
    else
    {
        $allgroups = $userlib->get_groups(0, -1, 'groupName_desc', '');
        $user_groups = array();
        foreach ($allgroups["data"] as $grp) $user_groups[] = $grp["groupName"];
    }

    for ($i = 0; $i < count($modules); $i++)
    {
        $r = &$modules[$i];
        $pass = 'y';

        if ($modallgroups != 'y')
        {
            // Check for the right groups
            if ($r["groups"]) $module_groups = unserialize($r["groups"]);
            else $module_groups = array();
            $pass = 'n';

            if ($modseparateanon !== 'y')
            {
                // normal case
                foreach ($module_groups as $mod_group)
                    if (in_array($mod_group, $user_groups))
                    {
                        $pass = 'y';
                        break; // no need to continue looping
                    }
            }
            else
            {
                // for anon: if module is allowed for anon => display
                if (!$user)
                {
                    // anon
                    if (in_array("Anonymous", $module_groups)) $pass = 'y';
                    // for reg user: ignore anon modules, if user-groups matches => display
                }
                else
                {
                    // reg user
                    foreach ($module_groups as $mod_group)
                    {
                        // don't display anon mods to reg users but continue checking
                        if ($mod_group === "Anonymous") continue;

                        // Module is for other than anon group
                        if (in_array($mod_group,$user_groups))
                        {
                            // other group matches one user-group => display
                            $pass = 'y';
                            break; // no need to continue checking
                        }
                    }
                }
            }
        }

        if ($pass == 'y')
        {
            // The cache name depending on the language is a quick fix
            // the cache is here to avoid calls to consumming queries,
            // but a module is different for each language because of the strings
            $cachefile = 'modules/cache/'.$tikidomain.'mod-'.$r["name"].'.tpl.'.$language.'.cache';
            $phpfile = 'modules/mod-' . $r["name"] . '.php';
            $template = 'modules/mod-' . $r["name"] . '.tpl';
            $nocache = 'templates/modules/mod-' . $r["name"] . '.tpl.nocache';

            if (!$r["rows"]) $r["rows"] = 10;

            $module_rows = $r["rows"];
            parse_str($r["params"], $module_params);

            if ((!file_exists($cachefile))
             || (file_exists($nocache))
             || ((time() - filemtime($cachefile)) > $r["cache_time"]))
            {
                $r["data"] = '';

                if (file_exists($phpfile))
                {
                    // If we have a php file then use it!
                    include_once ($phpfile);
                }

                // If there's a template we use it
                $template_file = 'templates/' . $template;

                if (file_exists($template_file))  $data = $smarty->fetch($template);
                else
                {
                    if ($tikilib->is_user_module($r["name"]))
                    {
                        $info = $tikilib->get_user_module($r["name"]);
                        // Ahora usar el template de user
                        $smarty->assign_by_ref('user_title', $info["title"]);
                        $smarty->assign_by_ref('user_data', $info["data"]);
                        $data = $smarty->fetch('modules/user_module.tpl');
                    }
                }

                $r["data"] = $data;
                if (!file_exists($nocache))
                {
                    $fp = fopen($cachefile, "w+");
                    fwrite($fp, $data, strlen($data));
                    fclose ($fp);
                }
            }
            else
            {
                $fp = fopen($cachefile, "r");
                $data = fread($fp, filesize($cachefile));
                fclose ($fp);
                $r["data"] = $data;
            }
        }
    }
}

clearstatcache();

if ($user_assigned_modules == 'y'
 && $tiki_p_configure_modules == 'y'
 && $user && $usermoduleslib->user_has_assigned_modules($user))
{
    $left_modules = $usermoduleslib->get_assigned_modules_user($user, 'l');
    $right_modules = $usermoduleslib->get_assigned_modules_user($user, 'r');
}
else
{
    $left_modules = $tikilib->get_assigned_modules('l', 'y');
    $right_modules = $tikilib->get_assigned_modules('r', 'y');
}

process_modules($left_modules);
process_modules($right_modules);

$smarty->assign_by_ref('right_modules', $right_modules);
$smarty->assign_by_ref('left_modules', $left_modules);

?>