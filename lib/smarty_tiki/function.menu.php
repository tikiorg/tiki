<?php
function smarty_function_menu($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing id");
        return;
    }
    $menu_info = $tikilib->get_menu($id);
    $channels = $tikilib->list_menu_options($id,0,-1,'position_asc','');
    $smarty->assign('menu_info',$menu_info);
    $smarty->assign('channels',$channels["data"]);
    
    $smarty->display('tiki-user_menu.tpl');
}

/* vim: set expandtab: */

?>
