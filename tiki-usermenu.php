<?php
require_once('tiki-setup.php');
include_once('lib/usermenu/usermenulib.php');

if($feature_usermenu != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_usermenu != 'y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["menuId"])) $_REQUEST["menuId"]=0;

if(isset($_REQUEST["delete"]) && isset($_REQUEST["menu"])) {
  foreach(array_keys($_REQUEST["menu"]) as $men) {      	
    $usermenulib->remove_usermenu($user, $men);
  }
  if(isset($_SESSION['usermenu'])) 
  	unset($_SESSION['usermenu']); 
}

if(isset($_REQUEST['addbk'])) {
  $usermenulib->add_bk($user);
  if(isset($_SESSION['usermenu'])) 
  	unset($_SESSION['usermenu']); 

}

if($_REQUEST["menuId"]) {
  $info = $usermenulib->get_usermenu($user,$_REQUEST["menuId"]);
} else {
  $info=Array();
  $info['name']='';
  $info['url']='';
  $info['position']=$usermenulib->get_max_position($user)+1;
}

if(isset($_REQUEST['save'])) {
  $usermenulib->replace_usermenu($user,$_REQUEST["menuId"],$_REQUEST["name"],$_REQUEST["url"],$_REQUEST['position'],$_REQUEST['mode']);
  $info=Array();
  $info['name']='';
  $info['url']='';
  $info['position']=1;
  $_REQUEST["menuId"]=0;
  unset($_SESSION['usermenu']);
}
$smarty->assign('menuId',$_REQUEST["menuId"]);
$smarty->assign('info',$info);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'position_asc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);
if(isset($_SESSION['thedate'])) {
 $pdate = $_SESSION['thedate'];
} else {
 $pdate = date("U");
}
$channels = $usermenulib->list_usermenus($user,$offset,$maxRecords,$sort_mode,$find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}
$smarty->assign_by_ref('channels',$channels["data"]);


include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-usermenu.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
