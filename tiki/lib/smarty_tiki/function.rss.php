<?php


function smarty_function_rss($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    global $rsslib;
	include_once('lib/rss/rsslib.php');
    extract($params);
    // Param = zone
    if(empty($id)) {
        $smarty->trigger_error("assign: missing id parameter");
        return;
    }
    if(empty($max)) {
       $max = 99;
    }
    $data = $rsslib->get_rss_module_content($id);
    $items = $rsslib->parse_rss_data($data, $id);
		print('<ul class="rsslist">');
    for($i=0;$i<count($items) && $i<$max;$i++) {
       if ($items[$i]["title"] <> '') print('<li class="rssitem"><a target="_blank" href="'.$items[$i]["link"].'" class="rsslink">'.$items[$i]["title"].'</a>');
       if ($items[$i]["pubdate"] <> '') print(' <span class="rssdate">('.$items[$i]["pubdate"].')</span>');
       print('</li>');
    }
    print('</ul>');
}

/* vim: set expandtab: */

?>
