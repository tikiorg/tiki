<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

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

    // skip="x,y" will not print Xth and Yth items
    // useful to avoid default first items
    if (!empty($skip) &&
	preg_match('/^\d+(,\d+)*$/', $skip)) {
	$skipped_items = explode(',', $skip);
	$skip = array();
	foreach ($skipped_items as $i) {
	    $skip[$i] = 1;
	}
    } else {
	$skip = array();
    }
    $data = $rsslib->get_rss_module_content($id);
    $items = $rsslib->parse_rss_data($data, $id);
		print('<ul class="rsslist">');
    for($i=0;$i<count($items) && $i<$max;$i++) {
	if (!$skip[$i+1]) {
	    if ($items[$i]["title"] <> '') print('<li class="rssitem"><a target="_blank" href="'.$items[$i]["link"].'" class="rsslink">'.$items[$i]["title"].'</a>');
	    if ($items[$i]["pubdate"] <> '') print(' <span class="rssdate">('.$items[$i]["pubdate"].')</span>');
	    print('</li>');
	}
    }
    print('</ul>');
}

/* vim: set expandtab: */

?>
