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

if(isset($_REQUEST['save'])) {
  $tikilib->set_user_preference($user,'minical_interval',$_REQUEST['minical_interval']);
  $tikilib->set_user_preference($user,'minical_start_hour',$_REQUEST['minical_start_hour']);
  $tikilib->set_user_preference($user,'minical_end_hour',$_REQUEST['minical_end_hour']);
//  $tikilib->set_user_preference($user,'minical_public',$_REQUEST['minical_public']);
}

$minical_interval = $tikilib->get_user_preference($user,'minical_interval',60*60);
$minical_start_hour = $tikilib->get_user_preference($user,'minical_start_hour',9);
$minical_end_hour = $tikilib->get_user_preference($user,'minical_end_hour',20);
$minical_public = $tikilib->get_user_preference($user,'minical_public','n');
if(isset($_REQUEST['minical_interval'])) {
  $minical_interval = $_REQUEST['minical_interval'];
} 
if(isset($_REQUEST['minical_start_hour'])) {
  $minical_start_hour = $_REQUEST['minical_start_hour'];
} 
if(isset($_REQUEST['minical_end_hour'])) {
  $minical_end_hour = $_REQUEST['minical_end_hour'];
} 
if(isset($_REQUEST['minical_public'])) {
  $minical_interval = $_REQUEST['minical_public'];
} 

$smarty->assign('minical_interval',$minical_interval);
$smarty->assign('minical_public',$minical_public);
$smarty->assign('minical_start_hour',$minical_start_hour);
$smarty->assign('minical_end_hour',$minical_end_hour);


$hours=range(0,23);
$smarty->assign('hours',$hours);

include_once('tiki-mytiki_shared.php');

$smarty->assign('mid','tiki-minical_prefs.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
