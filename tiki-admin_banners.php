<?php
// Initialization
require_once('tiki-setup.php');

/*
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}
*/


// Display the template
$smarty->assign('mid','tiki-edit_banner.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>