<?php
include_once("tiki-setup.php");


if($tiki_p_admin != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot assign permissions for this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["referer"])) {
  $_REQUEST["referer"]=$_SERVER['HTTP_REFERER'];
}
$smarty->assign('referer',$_REQUEST["referer"]);

if(!isset($_REQUEST["objectName"]) || !isset($_REQUEST["objectType"]) || !isset($_REQUEST["objectId"]) || !isset($_REQUEST["permType"])) {
  $smarty->assign('msg',tra("Not enough information to display this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($_REQUEST["objectId"]<1) {
  $smarty->assign('msg',tra("Fatal error"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$_REQUEST["objectId"]=urldecode($_REQUEST["objectId"]);
$_REQUEST["objectType"]=urldecode($_REQUEST["objectType"]);
$_REQUEST["permType"]=urldecode($_REQUEST["permType"]);

$smarty->assign('objectName',$_REQUEST["objectName"]);
$smarty->assign('objectId',$_REQUEST["objectId"]);
$smarty->assign('objectType',$_REQUEST["objectType"]);
$smarty->assign('permType',$_REQUEST["permType"]);


// Process the form to assign a new permission to this page
if(isset($_REQUEST["assign"])) {
  $userlib->assign_object_permission($_REQUEST["group"],$_REQUEST["objectId"],$_REQUEST["objectType"],$_REQUEST["perm"]);
}

// Process the form to remove a permission from the page
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"] == 'remove') {
    $userlib->remove_object_permission($_REQUEST["group"],$_REQUEST["objectId"],$_REQUEST["objectType"],$_REQUEST["perm"]);  
  }  
}

// Now we have to get the individual page permissions if any

$page_perms = $userlib->get_object_permissions($_REQUEST["objectId"],$_REQUEST["objectType"]);
$smarty->assign_by_ref('page_perms',$page_perms);

// Get a list of groups
$groups = $userlib->get_groups(0,-1,'groupName_desc');
$smarty->assign_by_ref('groups',$groups["data"]);

// Get a list of permissions
$perms = $userlib->get_permissions(0,-1,'permName_desc','',$_REQUEST["permType"]);
$smarty->assign_by_ref('perms',$perms["data"]);

$smarty->assign('mid','tiki-objectpermissions.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>