<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_admin_chat != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["channelId"])) {
  $_REQUEST["channelId"] = 0;
}
$smarty->assign('channelId',$_REQUEST["channelId"]);

if($_REQUEST["channelId"]) {
  $info = $tikilib->get_channel($_REQUEST["channelId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["active"]='y';
  $info["refresh"]=3000;
}
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('active',$info["active"]);
$smarty->assign('refresh',$info["refresh"]);

if(isset($_REQUEST["remove"])) {
  $tikilib->remove_channel($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["active"]) && $_REQUEST["active"]=='on') {
    $active = 'y';
  } else {
    $active = 'n';
  }
  $tikilib->replace_channel($_REQUEST["channelId"], $_REQUEST["name"], $_REQUEST["description"], 0, 'n', $active,$_REQUEST["refresh"]);
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'name_desc'; 
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

$smarty->assign_by_ref('sort_mode',$sort_mode);

$channels = $tikilib->list_channels($offset,$maxRecords,$sort_mode,$find);

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


// Display the template
$smarty->assign('mid','tiki-admin_chat.tpl');
$smarty->display('tiki.tpl');
?>