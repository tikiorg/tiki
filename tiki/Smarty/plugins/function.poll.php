<?php

require_once('lib/tikilib.php'); # httpScheme()

function smarty_function_poll($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone

    if (empty($id)) { 
      $id = $tikilib->get_random_active_poll();
    }
    if($id) {
      $menu_info = $tikilib->get_poll($id);
      $channels = $tikilib->list_poll_options($id,0,-1,'pollId_asc','');
      $smarty->assign('ownurl',httpPrefix().$_SERVER["REQUEST_URI"]);
      $smarty->assign('menu_info',$menu_info);
      $smarty->assign('channels',$channels["data"]);
      $smarty->display('tiki-poll.tpl');
    }
}

/* vim: set expandtab: */

?>
