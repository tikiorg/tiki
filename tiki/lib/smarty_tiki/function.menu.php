<?php
function smarty_function_menu($params, &$smarty)
{
    global $tikilib, $user;
    extract($params);
    // Param = zone

    $smarty->caching = true;

    if ($user) {
	$groups = $tikilib->get_user_groups($user);
	sort($groups, SORT_STRING);
	$cache_id = "$id:" . implode(":", $groups);
    } else {
	$cache_id = $id;
    }

    if (!$smarty->is_cached('tiki-user_menu.tpl', "$cache_id")) {
       $menu_info = $tikilib->get_menu($id);
       $channels = $tikilib->list_menu_options($id,0,-1,'position_asc','');

       
       // Let's sort sorted sections options
       $sorted_channels = array();
       $channels = $channels['data'];

       for ($i=0; $i < sizeof($channels); $i++) {
	   $sorted_channels[$i] = $channels[$i];
	   if ($sorted_channels[$i]['type'] == 'r') { // sorted section
       	       $sorted_channels[$i]['type'] = 's'; // common section, let's make it transparent
	       $i++;
	       $section = array();
	       while ($i < sizeof($channels) && $channels[$i]['type'] == 'o') {
		   $section[] = $channels[$i];
		   $i++;
	       }
	       $i--;
	       usort($section, "compare_menu_options");
	       $sorted_channels = array_merge($sorted_channels, $section);
	   }
       }
       $smarty->assign('channels',$sorted_channels);
       
       $smarty->assign('menu_info',$menu_info);
       
    }
    $smarty->display('tiki-user_menu.tpl', "$cache_id");
    $smarty->caching = false;
}

function compare_menu_options($a, $b) { return strcmp(tra($a['name']), tra($b['name'])); }

/* vim: set expandtab: */

?>
