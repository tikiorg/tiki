<?php
function smarty_function_rss($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = zone
    if(empty($id)) {
        $smarty->trigger_error("assign: missing id parameter");
        return;
    }
    if(empty($max)) {
       $max = 99;
    }
    $data = $tikilib->get_rss_module_content($id);
    $items = $tikilib->parse_rss_data($data);
    //print('<ul class="rss">');
    for($i=0;$i<count($items) && $i<$max;$i++) {
      print('<li><a target="_blank" href="'.$items[$i]["link"].'" class="linkmenu">'.$items[$i]["title"].'</a></li>');
    }
    //print('</ul>');
}

/* vim: set expandtab: */

?>
