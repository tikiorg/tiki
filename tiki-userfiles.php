<?php
require_once('tiki-setup.php');
include_once('lib/userfiles/userfileslib.php');

if($feature_userfiles != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// Process upload here
if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
  $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
  $data = '';
  while(!feof($fp)) {
    $data .= fread($fp,8192*16);
  }
  fclose($fp);
  $size = $_FILES['userfile1']['size'];
  $name = $_FILES['userfile1']['name'];
  $type = $_FILES['userfile1']['type'];
  $userfileslib->upload_userfile($user,'',$name,$type,$size, $data);
}



// Process removal here
if(isset($_REQUEST["delete"])) {
  foreach(array_keys($_REQUEST["userfile"]) as $file) {      	
    $userfileslib->remove_userfile($user, $file);
  }
}



if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'created_desc'; 
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
$channels = $userfileslib->list_userfiles($user,$offset,$maxRecords,$sort_mode,$find);

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

$smarty->assign('tasks_useDates',$tasks_useDates);

include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-userfiles.tpl');
$smarty->display('tiki.tpl');
?>


