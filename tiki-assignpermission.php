<?php
// This script is used to assign groups to a particular user
// ASSIGN USER TO GROUPS
// Initialization
require_once('tiki-setup.php');

if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
}


if(!isset($_REQUEST["group"])) {
  $smarty->assign('msg',tra("Unknown group"));
  $smarty->display('error.tpl');
  die; 
}
$group=$_REQUEST["group"];
if(!$userlib->group_exists($group)) {
  $smarty->assign('msg',tra("Group doesnt exist"));
  $smarty->display('error.tpl');
  die;  
}
$smarty->assign_by_ref('group',$group);


if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='assign') {
    $userlib->assign_permission_to_group($_REQUEST["perm"],$group);  
  }  
  if($_REQUEST["action"]=='remove') {
    $userlib->remove_permission_from_group($_REQUEST["permission"],$group); 
  }
}

$group_info = $userlib->get_group_info($group);
$smarty->assign_by_ref('group_info',$group_info);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'type_desc'; 
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

if(!isset($_REQUEST["type"])) {
  $_REQUEST["type"]='';
} 
$smarty->assign('type',$_REQUEST["type"]);

$perms = $userlib->get_permissions($offset,$maxRecords,$sort_mode,$find,$_REQUEST["type"]);
$cant_pages = ceil($perms["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($perms["cant"] > ($offset+$maxRecords)) {
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
$smarty->assign_by_ref('perms',$perms["data"]);

// Display the template
$smarty->assign('mid','tiki-assignpermission.tpl');
$smarty->display('tiki.tpl');
?>