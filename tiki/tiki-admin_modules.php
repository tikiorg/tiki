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
$smarty->assign('assign_params','');

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

$module_groups=Array();

if(isset($_REQUEST["edit_assign"])) {
  $_REQUEST["edit_assing"]=urldecode($_REQUEST["edit_assign"]);	
  $info = $tikilib->get_assigned_module($_REQUEST["edit_assign"]);
  $grps='';
  if($info["groups"]) {
    $module_groups = unserialize($info["groups"]);
    foreach($module_groups as $amodule) {
      $grps = $grps.' $amodule ';	
    }
  }
  $smarty->assign('module_groups',$grps);
  $smarty->assign_by_ref('assign_name',$info["name"]);
  //$smarty->assign_by_ref('assign_title',$info["title"]);
  $smarty->assign_by_ref('assign_position',$info["position"]);
  $smarty->assign_by_ref('assign_cache',$info["cache_time"]);
  $smarty->assign_by_ref('assign_rows',$info["rows"]);
  $smarty->assign_by_ref('assign_params',$info["params"]);
  $cosa="".$info["ord"];
  $smarty->assign_by_ref('assign_order',$cosa);
}

if(isset($_REQUEST["unassign"])) {
  $_REQUEST["unassing"]=urldecode($_REQUEST["unassign"]);		
  $tikilib->unassign_module($_REQUEST["unassign"]);
}

if(isset($_REQUEST["modup"])) {
  $_REQUEST["modup"]=urldecode($_REQUEST["modup"]);	
  $tikilib->module_up($_REQUEST["modup"]);
}

if(isset($_REQUEST["moddown"])) {
  $_REQUEST["moddown"]=urldecode($_REQUEST["moddown"]);		
  $tikilib->module_down($_REQUEST["moddown"]);
}


/* Edit or delete a user module */
if(isset($_REQUEST["um_update"])) {
  $_REQUEST["um_update"]=urldecode($_REQUEST["um_update"]);	
  $smarty->assign_by_ref('um_name',$_REQUEST["um_name"]);
  $smarty->assign_by_ref('um_title',$_REQUEST["um_title"]);
  $smarty->assign_by_ref('um_data',$_REQUEST["um_data"]);
  $tikilib->replace_user_module($_REQUEST["um_name"],$_REQUEST["um_title"],$_REQUEST["um_data"]);
}

if(!isset($_REQUEST["groups"])) {
  $_REQUEST["groups"]=Array();
}

$smarty->assign('preview','n');
if(isset($_REQUEST["preview"])) {
  $smarty->assign('preview','y');
  $smarty->assign_by_ref('assign_name',$_REQUEST["assign_name"]);
  if($tikilib->is_user_module($_REQUEST["assign_name"])) {
    $info = $tikilib->get_user_module($_REQUEST["assign_name"]);
    $smarty->assign_by_ref('user_title',$info["title"]);
    $smarty->assign_by_ref('user_data',$info["data"]);
    $data = $smarty->fetch('modules/user_module.tpl');
  } else {
    $phpfile = 'modules/mod-'.$_REQUEST["assign_name"].'.php';
    $template= 'modules/mod-'.$_REQUEST["assign_name"].'.tpl';	
    if(file_exists($phpfile)) {
      $module_rows=$_REQUEST["assign_rows"];
      parse_str($_REQUEST["assign_params"],$module_params);
      include($phpfile);	
    }
    if(file_exists('templates/'.$template)) {
      $data = $smarty->fetch($template);
    } else {
      $data = '';	
    }
  }
  $smarty->assign_by_ref('assign_name',$_REQUEST["assign_name"]);
  $smarty->assign_by_ref('assign_params',$_REQUEST["assign_params"]);
  $smarty->assign_by_ref('assign_position',$_REQUEST["assign_position"]);
  $smarty->assign_by_ref('assign_order',$_REQUEST["assign_order"]);
  $smarty->assign_by_ref('assign_cache',$_REQUEST["assign_cache"]);
  $smarty->assign_by_ref('assign_rows',$_REQUEST["assign_rows"]);
  $module_groups = $_REQUEST["groups"];
  $grps='';
  foreach($module_groups as $amodule) {
    $grps = $grps." $amodule ";	
  }
  $smarty->assign('module_groups',$grps);
  $smarty->assign_by_ref('preview_data',$data);
}

if(isset($_REQUEST["assign"])) {
  $_REQUEST["assing"]=urldecode($_REQUEST["assign"]);	
  $smarty->assign_by_ref('assign_name',$_REQUEST["assign_name"]);
  //$smarty->assign_by_ref('assign_title',$_REQUEST["assign_title"]);
  $smarty->assign_by_ref('assign_position',$_REQUEST["assign_position"]);
  $smarty->assign_by_ref('assign_params',$_REQUEST["assign_params"]);
  $smarty->assign_by_ref('assign_order',$_REQUEST["assign_order"]);
  $smarty->assign_by_ref('assign_cache',$_REQUEST["assign_cache"]);
  $smarty->assign_by_ref('assign_rows',$_REQUEST["assign_rows"]);
  $module_groups = $_REQUEST["groups"];
  $grps='';
  foreach($module_groups as $amodule) {
    $grps = $grps." $amodule ";	
  }
  $smarty->assign('module_groups',$grps);
  $tikilib->assign_module($_REQUEST["assign_name"],'',$_REQUEST["assign_position"],$_REQUEST["assign_order"],$_REQUEST["assign_cache"],$_REQUEST["assign_rows"],serialize($module_groups),$_REQUEST["assign_params"]);
  header("location: tiki-admin_modules.php");
}

if(isset($_REQUEST["um_remove"])) {
 $_REQUEST["um_remove"]=urldecode($_REQUEST["um_remove"]);	
 $tikilib->remove_user_module($_REQUEST["um_remove"]);
}

if(isset($_REQUEST["um_edit"])) {
  $_REQUEST["um_edit"]=urldecode($_REQUEST["um_edit"]);	
  $um_info = $tikilib->get_user_module($_REQUEST["um_edit"]);
  $smarty->assign_by_ref('um_name',$um_info["name"]);
  $smarty->assign_by_ref('um_title',$um_info["title"]);
  $smarty->assign_by_ref('um_data',$um_info["data"]);
}

$user_modules = $tikilib->list_user_modules();
$smarty->assign_by_ref('user_modules',$user_modules["data"]);

$all_modules = $tikilib->get_all_modules();
sort($all_modules);
$smarty->assign_by_ref('all_modules',$all_modules);

$orders = Array();
for($i=1;$i<50;$i++) {
  $orders[]=$i;
}
$smarty->assign_by_ref('orders',$orders);

$groups = $userlib->get_groups(0,-1,'groupName_desc','');
for($i=0;$i<count($groups["data"]);$i++) {
  if(in_array($groups["data"][$i]["groupName"],$module_groups)) {
    $groups["data"][$i]["selected"]='y';	
  } else {
    $groups["data"][$i]["selected"]='n';	
  }	
}
$smarty->assign_by_ref("groups",$groups["data"]);
$galleries = $tikilib->list_galleries(0,-1,'lastModif_desc', $user,'');
$smarty->assign('galleries',$galleries["data"]);
$polls = $tikilib->list_active_polls(0,-1,'publishDate_desc','');
$smarty->assign('polls',$polls["data"]);
$contents = $tikilib->list_content(0,-1,'contentId_desc','');
$smarty->assign('contents',$contents["data"]);
$rsss = $tikilib->list_rss_modules(0,-1,'name_desc','');
$smarty->assign('rsss',$rsss["data"]);
$menus = $tikilib->list_menus(0,-1,'menuId_desc','');
$smarty->assign('menus',$menus["data"]);
$banners = $tikilib->list_zones();
$smarty->assign('banners',$banners["data"]);
$left = $tikilib->get_assigned_modules('l');
$right = $tikilib->get_assigned_modules('r');
$smarty->assign_by_ref('left',$left);
$smarty->assign_by_ref('right',$right);

$smarty->assign('mid','tiki-admin_modules.tpl');
$smarty->display('tiki.tpl');


?>