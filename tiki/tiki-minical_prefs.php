<?php
require_once('tiki-setup.php');
include_once('lib/minical/minicallib.php');

if($feature_minical != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


//if($tiki_p_usermenu != 'y') {
//  $smarty->assign('msg',tra("Permission denied to use this feature"));
//  $smarty->display("styles/$style_base/error.tpl");
//  die;  
//}


include_once('tiki-mytiki_shared.php');

$smarty->assign('mid','tiki-minical_prefs.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
