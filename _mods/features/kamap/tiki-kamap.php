<?php
$_SERVER["SCRIPT_NAME"]=basename(__FILE__);
require_once('tiki-setup.php');
include_once ('lib/stats/statslib.php');
include_once ('lib/map/maplib.php');
include_once ('kamap/config.php');


if (!function_exists('ms_newMapObj')) {
  $msg = tra("You must first setup MapServer");
  $access->display_error(basename(__FILE__), $msg);
}

$access->check_feature('feature_maps');
$access->check_permission(array('tiki_p_map_view'), tra("View maps"));

// display the name of the map
$page=$aszMapFiles[$szMap]['title']." Map";
$smarty->assign("page",$page);

$pagelink='<a onclick="getWiki(\''.$page.'\');" href="#">'.$page.'</a>';

$smarty->assign("pagelink",$pagelink);

$section = 'maps';
include_once ('tiki-section_options.php');
$smarty->assign('mid','map/tiki-kamap.tpl');
$smarty->display("tiki.tpl");
?>