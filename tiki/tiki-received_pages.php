<?php
// Initialization
require_once('tiki-setup.php');

if($feature_comm != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}



if($tiki_p_admin_received_pages != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


if(!isset($_REQUEST["receivedPageId"])) {
  $_REQUEST["receivedPageId"] = 0;
}
$smarty->assign('receivedPageId',$_REQUEST["receivedPageId"]);

if(isset($_REQUEST["accept"])) {
  // CODE TO ACCEPT A PAGE HERE
  $tikilib->accept_page($_REQUEST["accept"]);
}


if($_REQUEST["receivedPageId"]) {
  $info = $tikilib->get_received_page($_REQUEST["receivedPageId"]);
} else {
  $info = Array();
  $info["pageName"]='';
  $info["data"]='';
  $info["comment"]='';
}
$smarty->assign('view','n');
if(isset($_REQUEST["view"])) {
   $info = $tikilib->get_received_page($_REQUEST["view"]);
   $smarty->assign('view','y');
}
if(isset($_REQUEST["preview"])) {
  $info["pageName"]=$_REQUEST["pageName"];
  $info["data"]=$_REQUEST["data"];
  $info["comment"]=$_REQUEST["comment"];  
}

$smarty->assign('pageName',$info["pageName"]);
$smarty->assign('data',$info["data"]);
$smarty->assign('comment',$info["comment"]);

// Assign parsed
$smarty->assign('parsed',$tikilib->parse_data($info["data"]));




if(isset($_REQUEST["remove"])) {
  $tikilib->remove_received_page($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  $tikilib->update_received_page($_REQUEST["receivedPageId"], $_REQUEST["pageName"], $_REQUEST["data"], $_REQUEST["comment"]);
  $smarty->assign('pageName',$_REQUEST["pageName"]);
  $smarty->assign('data',$_REQUEST["data"]);
  $smarty->assign('comment',$_REQUEST["comment"]);
  $smarty->assign('receivedPageId',$_REQUEST["receivedPageId"]);
  $smarty->assign('parsed',$tikilib->parse_data($_REQUEST["data"]));
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'receivedDate_desc'; 
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
$channels = $tikilib->list_received_pages($offset,$maxRecords,$sort_mode,$find);

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
$smarty->assign('mid','tiki-received_pages.tpl');
$smarty->display('tiki.tpl');
?>