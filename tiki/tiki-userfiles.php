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

if($tiki_p_userfiles != 'y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$quota = $userfileslib->userfiles_quota($user);
$limit = $userfiles_quota * 1024 * 1000;
if($limit==0) $limit=999999999;
$percentage = ($quota/$limit)*100;

$cellsize = round($percentage/100*200);
$percentage = round($percentage);
$smarty->assign('cellsize',$cellsize);
$smarty->assign('percentage',$percentage);

// Process upload here
for($i=0;$i<5;$i++) {
	if(isset($_FILES["userfile$i"])&&is_uploaded_file($_FILES["userfile$i"]['tmp_name'])) {
	 $fp = fopen($_FILES["userfile$i"]['tmp_name'],"rb");
	 $data = '';
	 $fhash='';
	 $name = $_FILES["userfile$i"]['name'];
	 if($uf_use_db == 'n') {
	   $fhash = md5(uniqid('.'));    
	   $fw = fopen($uf_use_dir.$fhash,"wb");
	   if(!$fw) {
	     $smarty->assign('msg',tra('Cannot write to this file:').$fhash);
	     $smarty->display("styles/$style_base/error.tpl");
	     die;  
	   }
	 }
	 while(!feof($fp)) {
	    if($uf_use_db == 'y') {
	      $data .= fread($fp,8192*16);
	    } else {
	      $data = fread($fp,8192*16);
	      fwrite($fw,$data);
	    }
	  }
	  fclose($fp);
	  if($uf_use_db == 'n') {
	    fclose($fw);
	    $data='';
	  }
	  $size = $_FILES["userfile$i"]['size'];
	  $name = $_FILES["userfile$i"]['name'];
	  $type = $_FILES["userfile$i"]['type'];
	  if($quota + $size > $limit) {
	     $smarty->assign('msg',tra('Cannot upload this file not enough quota'));
	     $smarty->display("styles/$style_base/error.tpl");
	     die;  
	  }  
	  
	  $userfileslib->upload_userfile($user,'',$name,$type,$size, $data, $fhash);
	}
}

// Process removal here
if(isset($_REQUEST["delete"]) && isset($_REQUEST["userfile"])) {
  foreach(array_keys($_REQUEST["userfile"]) as $file) {      	
    $userfileslib->remove_userfile($user, $file);
  }
}

$quota = $userfileslib->userfiles_quota($user);
$limit = $userfiles_quota * 1024 * 1000;
if($limit==0) $limit=999999999;
$percentage = $quota/$limit*100;
$cellsize = round($percentage/100*200);
$percentage = round($percentage);
if($cellsize==0) $cellsize=1;
$smarty->assign('cellsize',$cellsize);
$smarty->assign('percentage',$percentage);


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
$smarty->display("styles/$style_base/tiki.tpl");
?>


