<?php
require_once('tiki-setup.php');

if(isset($_REQUEST['view_user'])) {
  $userwatch = $_REQUEST['view_user'];
} else {
  if($user) {
    $userwatch = $user;
  } else {
    $smarty->assign('msg',tra("You are not logged in and no user indicated"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}

$smarty->assign('mid','map/tiki-map_download.tpl');
$smarty->assign('userwatch',$userwatch);
$map_path="/var/www/html/map/";

$smarty->display('tiki.tpl');
?>