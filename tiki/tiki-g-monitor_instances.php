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

if(isset($_REQUEST['update'])) {
  foreach($_REQUEST['update_status'] as $key => $val) {
    $processMonitor->update_instance_status($key,$val);
  }
  foreach($_REQUEST['update_actstatus'] as $key => $val) {
    $parts=explode(':',$val);
    $processMonitor->update_instance_activity_status($key,$parts[1],$parts[0]); 
  }
}

if(isset($_REQUEST['delete'])) {
  foreach(array_keys($_REQUEST['inst']) as $ins) {
    $processMonitor->remove_instance($ins);
  }
}

if(isset($_REQUEST['remove_aborted'])) {
  $processMonitor->remove_aborted();
}
if(isset($_REQUEST['remove_all'])) {
  $processMonitor->remove_all($_REQUEST['filter_process']);
}


if(isset($_REQUEST['sendInstance'])) {
  //activityId indicates the activity where the instance was
  //and we have to send it to some activity to be determined
  include_once('lib/Galaxia/src/API/Instance.php');
  $instance = new Instance($dbTiki);
  $instance->getInstance($_REQUEST['sendInstance']);
  // Do not add a workitem since the instance must be already completed!
  $instance->complete($_REQUEST['activityId'],true,false);
  unset($instance);
}


if(isset($_REQUEST['filter_status'])&&$_REQUEST['filter_status']) $wheres[]="gi.status='".$_REQUEST['filter_status']."'";
if(isset($_REQUEST['filter_act_status'])&&$_REQUEST['filter_act_status']) $wheres[]="actstatus='".$_REQUEST['filter_act_status']."'";
if(isset($_REQUEST['filter_process'])&&$_REQUEST['filter_process']) $wheres[]="gi.pId=".$_REQUEST['filter_process']."";
if(isset($_REQUEST['filter_activity'])&&$_REQUEST['filter_activity']) $wheres[]="gia.activityId=".$_REQUEST['filter_activity']."";
if(isset($_REQUEST['filter_user'])&&$_REQUEST['filter_user']) $wheres[]="user='".$_REQUEST['filter_user']."'";
if(isset($_REQUEST['filter_owner'])&&$_REQUEST['filter_owner']) $wheres[]="owner='".$_REQUEST['filter_owner']."'";
$where = implode(' and ',$wheres);

if(!isset($_REQUEST["sort_mode"])) {  $sort_mode = 'name_asc'; } else {  $sort_mode = $_REQUEST["sort_mode"];} 
if(!isset($_REQUEST["offset"])) {  $offset = 0;} else {  $offset = $_REQUEST["offset"]; }$smarty->assign_by_ref('offset',$offset);
if(isset($_REQUEST["find"])) { $find = $_REQUEST["find"];  } else {  $find = ''; } $smarty->assign('find',$find);
$smarty->assign('where',$where); $smarty->assign_by_ref('sort_mode',$sort_mode);

$items = $processMonitor->monitor_list_instances($offset,$maxRecords,$sort_mode,$find,$where);
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

$all_statuses=Array('aborted','active','completed','exception');
$smarty->assign('all_statuses',$all_statuses);

$sameurl_elements = Array('offset','sort_mode','where','find','filter_user','filter_status','filter_act_status','filter_type','processId','filter_process','filter_owner','filter_activity');

$smarty->assign('statuses', $processMonitor->monitor_list_statuses());
$smarty->assign('users', $processMonitor->monitor_list_users());
$smarty->assign('owners', $processMonitor->monitor_list_owners());
$smarty->assign('mid','tiki-g-monitor_instances.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>