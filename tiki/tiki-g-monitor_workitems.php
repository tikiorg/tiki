<?php
require_once('tiki-setup.php');
include_once('lib/Galaxia/ProcessMonitor.php');

if($feature_workflow != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_admin_workflow != 'y') {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// Filtering data to be received by request and
// used to build the where part of a query
// filter_active, filter_valid, find, sort_mode,
// filter_process


$where = ''; 
$wheres=Array();
if(isset($_REQUEST['filter_instance'])&&$_REQUEST['filter_instance']) $wheres[]="instanceId=".$_REQUEST['filter_instance']."";
if(isset($_REQUEST['filter_process'])&&$_REQUEST['filter_process']) $wheres[]="gp.pId=".$_REQUEST['filter_process']."";
if(isset($_REQUEST['filter_activity'])&&$_REQUEST['filter_activity']) $wheres[]="ga.activityId=".$_REQUEST['filter_activity']."";
if(isset($_REQUEST['filter_user'])&&$_REQUEST['filter_user']) $wheres[]="user='".$_REQUEST['filter_user']."'";

$where = implode(' and ',$wheres);

if(!isset($_REQUEST["sort_mode"])) {  $sort_mode = 'orderId_asc'; } else {  $sort_mode = $_REQUEST["sort_mode"];} 
if(!isset($_REQUEST["offset"])) {  $offset = 0;} else {  $offset = $_REQUEST["offset"]; }$smarty->assign_by_ref('offset',$offset);
if(isset($_REQUEST["find"])) { $find = $_REQUEST["find"];  } else {  $find = ''; } $smarty->assign('find',$find);
$smarty->assign('where',$where); $smarty->assign_by_ref('sort_mode',$sort_mode);

$items = $processMonitor->monitor_list_workitems($offset,$maxRecords,$sort_mode,$find,$where);
$smarty->assign('cant',$items['cant']);

$cant_pages = ceil($items["cant"] / $maxRecords);$smarty->assign_by_ref('cant_pages',$cant_pages);$smarty->assign('actual_page',1+($offset/$maxRecords));
if($items["cant"] > ($offset+$maxRecords)) {  $smarty->assign('next_offset',$offset + $maxRecords);} else {  $smarty->assign('next_offset',-1); }
if($offset>0) {  $smarty->assign('prev_offset',$offset - $maxRecords);  } else {  $smarty->assign('prev_offset',-1); }
$smarty->assign_by_ref('items',$items["data"]);

$all_procs = $items = $processMonitor->monitor_list_processes(0,-1,'name_desc','','');
$smarty->assign_by_ref('all_procs',$all_procs["data"]);

if(isset($_REQUEST['filter_process'])&&$_REQUEST['filter_process']) {
  $where = ' pId='.$_REQUEST['filter_process'];
} else {
  $where = '';
}
$all_acts  = $processMonitor->monitor_list_activities(0,-1,'name_desc','',$where);
$smarty->assign_by_ref('all_acts',$all_acts["data"]);


$types = $processMonitor->monitor_list_activity_types();
$smarty->assign_by_ref('types',$types);

$smarty->assign('stats', $processMonitor->monitor_stats());

$smarty->assign('users', $processMonitor->monitor_list_wi_users());

$smarty->assign('mid','tiki-g-monitor_workitems.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>