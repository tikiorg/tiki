<?php

require_once('tiki-setup.php');

// PERMISSIONS: NEEDS p_admin
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
}

// Values for the user_module edit/create form
$smarty->assign('um_name','');
$smarty->assign('um_title','');
$smarty->assign('um_data','');

$smarty->assign('assign_name','');
//$smarty->assign('assign_title','');
$smarty->assign('assign_position','');
$smarty->assign('assign_order','');
$smarty->assign('assign_cache',0);
$smarty->assign('assign_rows',10);

if(isset($_REQUEST["clear_cache"])) {
  $h = opendir("modules/cache");
  while (($file = readdir($h)) !== false) {
      if(substr($file,0,3)=='mod') {
        $file="modules/cache/".$file;
        unlink($file);
      }
    }  
  closedir($h);
}

if(isset($_REQUEST["edit_assign"])) {
  $info = $tikilib->get_assigned_module($_REQUEST["edit_assign"]);
  $smarty->assign_by_ref('assign_name',$info["name"]);
  //$smarty->assign_by_ref('assign_title',$info["title"]);
  $smarty->assign_by_ref('assign_position',$info["position"]);
  $smarty->assign_by_ref('assign_cache',$info["cache_time"]);
  $smarty->assign_by_ref('assign_rows',$info["rows"]);
  $cosa="".$info["ord"];
  $smarty->assign_by_ref('assign_order',$cosa);
}

if(isset($_REQUEST["unassign"])) {
  $tikilib->unassign_module($_REQUEST["unassign"]);
}

if(isset($_REQUEST["modup"])) {
  $tikilib->module_up($_REQUEST["modup"]);
}

if(isset($_REQUEST["moddown"])) {
  $tikilib->module_down($_REQUEST["moddown"]);
}


/* Edit or delete a user module */
if(isset($_REQUEST["um_update"])) {
  $smarty->assign_by_ref('um_name',$_REQUEST["um_name"]);
  $smarty->assign_by_ref('um_title',$_REQUEST["um_title"]);
  $smarty->assign_by_ref('um_data',$_REQUEST["um_data"]);
  $tikilib->replace_user_module($_REQUEST["um_name"],$_REQUEST["um_title"],$_REQUEST["um_data"]);
}

if(isset($_REQUEST["assign"])) {
  $smarty->assign_by_ref('assign_name',$_REQUEST["assign_name"]);
  //$smarty->assign_by_ref('assign_title',$_REQUEST["assign_title"]);
  $smarty->assign_by_ref('assign_position',$_REQUEST["assign_position"]);
  $smarty->assign_by_ref('assign_order',$_REQUEST["assign_order"]);
  $smarty->assign_by_ref('assign_cache',$_REQUEST["assign_cache"]);
  $smarty->assign_by_ref('assign_rows',$_REQUEST["assign_rows"]);
  $tikilib->assign_module($_REQUEST["assign_name"],'',$_REQUEST["assign_position"],$_REQUEST["assign_order"],$_REQUEST["assign_cache"],$_REQUEST["assign_rows"]);
}

if(isset($_REQUEST["um_remove"])) {
 $tikilib->remove_user_module($_REQUEST["um_remove"]);
}

if(isset($_REQUEST["um_edit"])) {
  $um_info = $tikilib->get_user_module($_REQUEST["um_edit"]);
  $smarty->assign_by_ref('um_name',$um_info["name"]);
  $smarty->assign_by_ref('um_title',$um_info["title"]);
  $smarty->assign_by_ref('um_data',$um_info["data"]);
}

$user_modules = $tikilib->list_user_modules();
$smarty->assign_by_ref('user_modules',$user_modules["data"]);

$all_modules = $tikilib->get_all_modules();
$smarty->assign_by_ref('all_modules',$all_modules);

$orders = Array();
for($i=1;$i<20;$i++) {
  $orders[]=$i;
}
$smarty->assign_by_ref('orders',$orders);

$left = $tikilib->get_assigned_modules('l');
$right = $tikilib->get_assigned_modules('r');
$smarty->assign_by_ref('left',$left);
$smarty->assign_by_ref('right',$right);

$smarty->assign('mid','tiki-admin_modules.tpl');
$smarty->display('tiki.tpl');


?>