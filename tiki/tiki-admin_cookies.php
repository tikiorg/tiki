<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_edit_cookies != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["cookieId"])) {
  $_REQUEST["cookieId"] = 0;
}
$smarty->assign('cookieId',$_REQUEST["cookieId"]);

if($_REQUEST["cookieId"]) {
  $info = $tikilib->get_cookie($_REQUEST["cookieId"]);
} else {
  $info = Array();
  $info["cookie"]='';
  
}
$smarty->assign('cookie',$info["cookie"]);


if(isset($_REQUEST["remove"])) {
  $tikilib->remove_cookie($_REQUEST["remove"]);
}

if(isset($_REQUEST["removeall"])) {
  $tikilib->remove_all_cookies();
}

if(isset($_REQUEST["upload"])) {
if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
  $fp = fopen($_FILES['userfile1']['tmp_name'],"r");
  while(!feof($fp)) {
    $data=fgets($fp,255);
    if(!empty($data)) {
         $data = str_replace("\n","",$data);
         $tikilib->replace_cookie(0,$data);
    }
  }
  fclose($fp);
  $size = $_FILES['userfile1']['size'];
  $name = $_FILES['userfile1']['name'];
  $type = $_FILES['userfile1']['type'];
} else {
  $smarty->assign('msg',tra("Upload failed"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
}


if(isset($_REQUEST["save"])) {
  
  $tikilib->replace_cookie($_REQUEST["cookieId"], $_REQUEST["cookie"]);
  $smarty->assign("cookieId",'0');
  $smarty->assign('cookie','');
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'cookieId_desc'; 
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
$channels = $tikilib->list_cookies($offset,$maxRecords,$sort_mode,$find);

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
$smarty->assign('mid','tiki-admin_cookies.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>