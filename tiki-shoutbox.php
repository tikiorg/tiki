<?php
// Initialization
require_once('tiki-setup.php');


if($feature_shoutbox != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if($tiki_p_view_shoutbox != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


if(!isset($_REQUEST["msgId"])) {
  $_REQUEST["msgId"] = 0;
}
$smarty->assign('msgId',$_REQUEST["msgId"]);

if($_REQUEST["msgId"]) {
  if($tiki_p_admin_shoutbox != 'y') {
    $smarty->assign('msg',tra("You dont have permission to edit messages"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
  $info = $tikilib->get_shoutbox($_REQUEST["msgId"]);
} else {
  $info = Array();
  $info["message"]='';
  $info["user"]=$user;
}
$smarty->assign('message',$info["message"]);
$smarty->assign('user',$info["user"]);

if($tiki_p_admin_shoutbox == 'y') {
  if(isset($_REQUEST["remove"])) {
    $tikilib->remove_shoutbox($_REQUEST["remove"]);
  }
}



if($tiki_p_post_shoutbox == 'y') {
  if(isset($_REQUEST["save"])) {
    $tikilib->replace_shoutbox($_REQUEST["msgId"], $user,$_REQUEST["message"]);
    $smarty->assign("msgId",'0');
    $smarty->assign('message','');
  } 
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'timestamp_desc'; 
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
$channels = $tikilib->list_shoutbox($offset,$maxRecords,$sort_mode,$find);

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
$smarty->assign('mid','tiki-shoutbox.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>