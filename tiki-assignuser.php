<?php
// This script is used to assign groups to a particular user
// ASSIGN USER TO GROUPS
// Initialization
require_once('tiki-setup.php');

if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}


if(!isset($_REQUEST["assign_user"])) {
  $smarty->assign('msg',tra("Unknown user"));
  $smarty->display("styles/$style_base/error.tpl");
  die; 
}
$assign_user=$_REQUEST["assign_user"];
$smarty->assign_by_ref('assign_user',$assign_user);

if(!$userlib->user_exists($assign_user)) {
  $smarty->assign('msg',tra("User doesnt exist"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='assign') {
    $userlib->assign_user_to_group($_REQUEST["assign_user"],$_REQUEST["group"]);  
  }  
  if($_REQUEST["action"]=='removegroup') {
    $userlib->remove_user_from_group($_REQUEST["assign_user"],$_REQUEST["group"]); 
  }
}

$user_info = $userlib->get_user_info($assign_user);
$smarty->assign_by_ref('user_info',$user_info);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'groupName_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 
$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
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

$users = $userlib->get_groups($offset,$maxRecords,$sort_mode,$find);
$cant_pages = ceil($users["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($users["cant"] > ($offset+$maxRecords)) {
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

// Get users (list of users)
$smarty->assign_by_ref('users',$users["data"]);

// Display the template
$smarty->assign('mid','tiki-assignuser.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>