<?php
include_once('tiki-setup.php');

if(!$user) {
    $smarty->assign('msg',tra("You must log in to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if($feature_user_watches != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(isset($_REQUEST['hash'])) {
  $query = "delete from tiki_user_watches where hash='$hash'";
  $tikilib->query($query);
}


$smarty->assign('mid','tiki-user_watches.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?> 