<?php
require_once('tiki-setup.php');
include_once('lib/tasks/tasklib.php');

if($feature_tasks != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_tasks != 'y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



$comp_array = Array();
$comp_array_p = Array();
for($i=0;$i<101;$i+=10) {
  $comp_array[]=$i;
  $comp_array_p[]=$i.'%';
}
$smarty->assign('comp_array',$comp_array);
$smarty->assign('comp_array_p',$comp_array_p);

if(!isset($_REQUEST["taskId"])) $_REQUEST["taskId"]=0;


if(isset($_REQUEST["complete"]) && isset($_REQUEST["task"])) {
  foreach(array_keys($_REQUEST["task"]) as $task) {      	
    $tasklib->complete_task($user, $task);
  }
}

if(isset($_REQUEST["open"]) && isset($_REQUEST["task"])) {
  foreach(array_keys($_REQUEST["task"]) as $task) {      	
    $tasklib->open_task($user, $task);
  }
}


if(isset($_REQUEST["delete"]) && isset($_REQUEST["task"])) {
  foreach(array_keys($_REQUEST["task"]) as $task) {      	
    $tasklib->remove_task($user, $task);
  }
}



if(isset($_REQUEST["tasks_useDates"])) {
  $tasks_useDates = $_REQUEST["tasks_useDates"];
} else {
  $tasks_useDates = $tikilib->get_user_preference($user,'tasks_useDates');
}
$tasks_maxRecords = $tikilib->get_user_preference($user,'tasks_maxRecords', $maxRecords);
$maxRecords = $tasks_maxRecords;
$smarty->assign('tasks_useDates',$tasks_useDates);
$smarty->assign('tasks_maxRecords',$tasks_maxRecords);

if($_REQUEST["taskId"]) {
  $info = $tasklib->get_task($user,$_REQUEST["taskId"]);
} else {
  $info=Array();
  $info['title']='';
  $info['description']='';
  $info['priority']=3;
  $info['status']='o';
  $info['date']=date("U");
}

if(isset($_REQUEST['save'])) {
  $date = $tikilib->make_server_time(0,0,0,$_REQUEST["Date_Month"],$_REQUEST["Date_Day"],$_REQUEST["Date_Year"],$tikilib->get_display_timezone($user));
  if($_REQUEST['status']=='c') {
    $_REQUEST['percentage']=100;
    $completed = $date;
  } else {
    $completed = 0;
  }
  if($_REQUEST['percentage']==100) {
    $completed = $date;
    $_REQUEST['status']='c';
  } else {
    $_REQUEST['status']='o';
    $completed = 0;
  }
  $tasklib->replace_task($user,$_REQUEST["taskId"],$_REQUEST["title"],$_REQUEST["description"],$date,$_REQUEST['status'],$_REQUEST['priority'],$completed,$_REQUEST['percentage']);
  $info=Array();
  $info['title']='';
  $info['description']='';
  $info['priority']=3;
  $info['status']='o';
  $info['date']=date("U");
  $_REQUEST["taskId"]=0;
}
$smarty->assign('taskId',$_REQUEST["taskId"]);
$smarty->assign('info',$info);
$smarty->assign('Date_Month',date("m",$info['date']));
$smarty->assign('Date_Day',date("d",$info['date']));
$smarty->assign('Date_Year',date("Y",$info['date']));

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'priority_desc'; 
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
$channels = $tasklib->list_tasks($user,$offset,$maxRecords,$sort_mode,$find,$tasks_useDates,$pdate);

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


$smarty->assign('mid','tiki-user_tasks.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
