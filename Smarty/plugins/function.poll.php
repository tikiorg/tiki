<?php
require_once('lib/tikilib.php'); # httpScheme()

function smarty_function_poll($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    extract($params);
    // Param = zone
	include_once('lib/polls/polllib.php');

    if (empty($id)) { 
      $id = $polllib->get_random_active_poll();
    }
    if($id) {
      $menu_info = $polllib->get_poll($id);
      $channels = $polllib->list_poll_options($id,0,-1,'pollId_asc','');
      $smarty->assign('ownurl',/*httpPrefix().*/$_SERVER["REQUEST_URI"]);
      $smarty->assign('menu_info',$menu_info);
      $smarty->assign('channels',$channels["data"]);
      $smarty->display('tiki-poll.tpl');
    }
}

/* vim: set expandtab: */

?>
