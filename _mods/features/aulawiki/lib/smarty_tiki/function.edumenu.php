<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_edumenu($params, &$smarty)
{
    global $tikilib, $user;
    extract($params);
    // Param = zone

    $smarty->caching = true;
//REYES problema al crear directorios con nombres demasiado largos
    if ($user) {
    	$uid = md5($tikilib->get_user_cache_id($user).$workspaceId);
        $cache_id = "menu$id|" . $uid;
        
        //$cache_id = "menu$id|" . $tikilib->get_user_cache_id($user);
    } else {
		$cache_id = "menu$id|".$workspaceId;
    }
//TODO: Si el menu está en la cache es necesario modificar el workspaceId de cada enlace del menu
    if (!$smarty->is_cached('tiki-user_edumenu.tpl', "$cache_id")) {
       $menu_info = $tikilib->get_menu($id);
       $channels = $tikilib->list_menu_options($id,0,-1,'position_asc','');
       $channels = $tikilib->sort_menu_options($channels);

		$canales = array();
		if (isset($workspaceId) && $workspaceId!=""){       
	       foreach($channels['data'] as $key=>$chdata){
					if(strstr($chdata["url"],'?')==FALSE){
						$chdata["url"] = $chdata["url"]."?workspaceId=".$workspaceId;
					}else{
						$chdata["url"] = $chdata["url"]."&workspaceId=".$workspaceId;
					}
					$canales[$key]=$chdata;
	       }
    	}else{
    		$canales = $channels['data'];
    	}
       $smarty->assign('channels',$canales);
       
       $smarty->assign('menu_info',$menu_info);
       $smarty->assign('workspaceId',$workspaceId);
       
   }

    $smarty->display('tiki-user_edumenu.tpl', "$cache_id");
    $smarty->caching = false;
}

/*function compare_menu_options($a, $b) { return strcmp(tra($a['name']), tra($b['name'])); }
*/
/* vim: set expandtab: */

?>
