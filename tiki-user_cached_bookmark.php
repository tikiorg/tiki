<?php
// Initialization
require_once('tiki-setup.php');

if(!$user) {
    $smarty->assign('msg',tra("You must log in to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


if($feature_user_bookmarks != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["urlid"])) {
  $smarty->assign('msg',tra("No url indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} 

// Get a list of last changes to the Wiki database
$info = $tikilib->get_url($_REQUEST["urlid"]);
$smarty->assign_by_ref('info',$info);
$info["refresh"]=$info["lastUpdated"];
$smarty->assign('mid','tiki-view_cache.tpl');
$smarty->display('tiki-view_cache.tpl');
?>
