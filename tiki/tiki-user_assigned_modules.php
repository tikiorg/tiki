<?php
// Initialization
require_once('tiki-setup.php');


if($tiki_p_configure_modules != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


if($user_assigned_modules != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!$user) {
    $smarty->assign('msg',tra("You must log in to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(isset($_REQUEST["recreate"])) {
  $tikilib->create_user_assigned_modules($user);
}

if(!$tikilib->user_has_assigned_modules($user))
{
  $tikilib->create_user_assigned_modules($user);
}

if(isset($_REQUEST["unassign"])) {
  $tikilib->unassign_user_module($_REQUEST["unassign"],$user);
}

if(isset($_REQUEST["assign"])) {
  $tikilib->assign_user_module($_REQUEST["module"],$_REQUEST["position"],$_REQUEST["order"],$user);
}

if(isset($_REQUEST["up"])) {
  $tikilib->up_user_module($_REQUEST["up"],$user);
}
if(isset($_REQUEST["down"])) {
  $tikilib->down_user_module($_REQUEST["down"],$user);
}

if(isset($_REQUEST["left"])){
  $tikilib->set_column_user_module($_REQUEST["left"],$user,'l');
}

if(isset($_REQUEST["right"])){
  $tikilib->set_column_user_module($_REQUEST["right"],$user,'r');
}

$orders = Array();
for($i=1;$i<20;$i++) {
  $orders[]=$i;
}
$smarty->assign_by_ref('orders',$orders);

$assignables = $tikilib->get_user_assignable_modules($user);
if(count($assignables)>0) {
  $smarty->assign('canassign','y');
} else {
  $smarty->assign('canassign','n');
}
//print_r($assignables);
$modules = $tikilib->get_user_assigned_modules($user);
//print_r($modules);
$smarty->assign_by_ref('assignables',$assignables);
$smarty->assign_by_ref('modules',$modules);
//print_r($modules);


$smarty->assign('mid','tiki-user_assigned_modules.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>