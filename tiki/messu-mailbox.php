<?php
require_once('tiki-setup.php');
include_once('lib/messu/messulib.php');

if($feature_messages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_messages != 'y' ) {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


$maxRecords = $messulib->get_user_preference($_SESSION['user'],'maxRecords',20);

// Mark messages if the mark button was pressed
if(isset($_REQUEST["mark"])) {
  foreach(array_keys($_REQUEST["msg"]) as $msg) {      	
    $parts = explode('_',$_REQUEST['action']);
    $messulib->flag_message($_SESSION['user'], $msg, $parts[0], $parts[1]);
  }
}

// Delete messages if the delete button was pressed
if(isset($_REQUEST["delete"])) {
  foreach(array_keys($_REQUEST["msg"]) as $msg) {      	
    $messulib->delete_message($_SESSION['user'], $msg);  
  }
}

if(isset($_REQUEST['filter'])) {
  if($_REQUEST['flags']!='') {
    $parts = explode('_',$_REQUEST['flags']);
    $_REQUEST['flag']=$parts[0];
    $_REQUEST['flagval']=$parts[1];
  }
}

if(!isset($_REQUEST["priority"])) $_REQUEST["priority"]='';
if(!isset($_REQUEST["flag"])) $_REQUEST["flag"]='';
if(!isset($_REQUEST["flagval"])) $_REQUEST["flagval"]='';
if(!isset($_REQUEST["sort_mode"])) {  $sort_mode = 'date_desc'; } else {  $sort_mode = $_REQUEST["sort_mode"];} 
if(!isset($_REQUEST["offset"])) {  $offset = 0;} else {  $offset = $_REQUEST["offset"]; }
if(isset($_REQUEST["find"])) {  $find = $_REQUEST["find"];  } else {  $find = ''; }
$smarty->assign_by_ref('flag',$_REQUEST['flag']);
$smarty->assign_by_ref('priority',$_REQUEST['priority']);
$smarty->assign_by_ref('flagval',$_REQUEST['flagval']);
$smarty->assign_by_ref('offset',$offset);
$smarty->assign_by_ref('sort_mode',$sort_mode);
$smarty->assign('find',$find);
// What are we paginating: items

$items = $messulib->list_user_messages($user,$offset,$maxRecords,$sort_mode,$find,$_REQUEST["flag"],$_REQUEST["flagval"],$_REQUEST['priority']);


$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($items["cant"] > ($offset+$maxRecords)) {  $smarty->assign('next_offset',$offset + $maxRecords);} else {  $smarty->assign('next_offset',-1); }
if($offset>0) {  $smarty->assign('prev_offset',$offset - $maxRecords);  } else {  $smarty->assign('prev_offset',-1); }
$smarty->assign_by_ref('items',$items["data"]);



$smarty->assign('mid','messu-mailbox.tpl');
$smarty->display('tiki.tpl');
?>