<?php
require_once('tiki-setup.php');
include_once('lib/newsreader/newslib.php');

if(!$user) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display("styles/$style_base/error.tpl");
   die;
}


if($feature_newsreader != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



$smarty->assign('mid','tiki-newsreader_servers.tpl');
$smarty->display('tiki.tpl');
?>