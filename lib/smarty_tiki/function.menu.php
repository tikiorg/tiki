<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_menu($params, &$smarty)
{
    global $tikilib, $user;
    extract($params);
    // Param = zone

    $smarty->caching = true;

    if ($user) {
        $cache_id = "menu$id|" . $tikilib->get_user_cache_id($user);
    } else {
	$cache_id = "menu$id";
    }

    if (!$smarty->is_cached('tiki-user_menu.tpl', "$cache_id")) {
       $menu_info = $tikilib->get_menu($id);
       $channels = $tikilib->list_menu_options($id,0,-1,'position_asc','');
       $channels = $tikilib->sort_menu_options($channels);
       
       $smarty->assign('channels',$channels['data']);
       
       $smarty->assign('menu_info',$menu_info);
       
    }
    $smarty->display('tiki-user_menu.tpl', "$cache_id");
    $smarty->caching = false;
}

function compare_menu_options($a, $b) { return strcmp(tra($a['name']), tra($b['name'])); }

/* vim: set expandtab: */

?>
