<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["pollId"])) {
  $_REQUEST["pollId"] = 0;
}
$smarty->assign('pollId',$_REQUEST["pollId"]);

if(isset($_REQUEST["setlast"])) {
  $tikilib->set_last_poll();
}
if(isset($_REQUEST["closeall"])) {
  $tikilib->close_all_polls();
}
if(isset($_REQUEST["activeall"])) {
  $tikilib->active_all_polls();
}


if($_REQUEST["pollId"]) {
  $info = $tikilib->get_poll($_REQUEST["pollId"]);
} else {
  $info = Array();
  $info["title"]='';
  $info["active"]='y';
  $info["publishDate"]=date("U");
}
$smarty->assign('title',$info["title"]);
$smarty->assign('active',$info["active"]);
$smarty->assign('publishDate',$info["publishDate"]);


if(isset($_REQUEST["remove"])) {
  $tikilib->remove_poll($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  
  $publishDate = mktime($_REQUEST["Time_Hour"],$_REQUEST["Time_Minute"],0,
                        $_REQUEST["Date_Month"],$_REQUEST["Date_Day"],$_REQUEST["Date_Year"]);
  $pid = $tikilib->replace_poll($_REQUEST["pollId"], $_REQUEST["title"], $_REQUEST["active"], $publishDate);
  
  $cat_type='poll';
  $cat_objid = $pid;
  $cat_desc = substr($_REQUEST["title"],0,200);
  $cat_name = $_REQUEST["title"];
  $cat_href="tiki-poll_results.php?pollId=".$cat_objid;
  include_once("categorize.php");
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'publishDate_desc'; 
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
$channels = $tikilib->list_polls($offset,$maxRecords,$sort_mode,$find);

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

$cat_type='poll';
$cat_objid = $_REQUEST["pollId"];
include_once("categorize_list.php");


// Display the template
$smarty->assign('mid','tiki-admin_polls.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>