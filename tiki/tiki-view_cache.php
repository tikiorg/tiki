<?php
// Initialization
require_once('tiki-setup.php');

/*
if($feature_listPages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
*/

if(isset($_REQUEST["url"])) {
  $id = $tikilib->get_cache_id($_REQUEST["url"]);
  if(!$id) {
    $smarty->assign('msg',tra("No cache information available"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  $_REQUEST["cacheId"] = $id;
}


if(!isset($_REQUEST["cacheId"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} 


// Get a list of last changes to the Wiki database
$info = $tikilib->get_cache($_REQUEST["cacheId"]);
$smarty->assign_by_ref('info',$info);
$smarty->assign('mid','tiki-view_cache.tpl');
$smarty->display('tiki-view_cache.tpl');
?>
