<?php
require_once('tiki-setup.php');
include_once('lib/Galaxia/ProcessManager.php');

// The galaxia source editor for activities and
// processes.

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


if(!isset($_REQUEST['pid'])) {
  $smarty->assign('msg',tra("No process indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
$smarty->assign('pid',$_REQUEST['pid']);

$proc_info = $processManager->get_process($_REQUEST['pid']);
$proc_info['graph']="lib/Galaxia/Processes/".$proc_info['normalized_name']."/graph/".$proc_info['normalized_name'].".png";
$smarty->assign_by_ref('proc_info',$proc_info);

$procname = $proc_info['normalized_name'];

$smarty->assign('warn','');

if(!isset($_REQUEST['activityId'])) $_REQUEST['activityId']=0;
$smarty->assign('activityId',$_REQUEST['activityId']);
if($_REQUEST['activityId']) {
  $act_info = $activityManager->get_activity($_REQUEST['pid'], $_REQUEST['activityId']);
  $actname = $act_info['normalized_name'];
  if(isset($_REQUEST['template'])) {
    $smarty->assign('template','y');
    $source = "lib/Galaxia/processes/$procname/code/templates/$actname".'.tpl';
  } else {
    $smarty->assign('template','n');
    $source = "lib/Galaxia/processes/$procname/code/activities/$actname".'.php';
  }
  // Then editing an activity
  $smarty->assign('act_info',$act_info);
} else {
  // Then editing shared code
  $source = "lib/Galaxia/processes/$procname/code/shared.php";
}


//First of all save
if(isset($_REQUEST['source'])) {
  $fp = fopen($_REQUEST['source_name'],"w");  
  fwrite($fp,$_REQUEST['source']);
  fclose($fp);
  if($_REQUEST['activityId']) {
    $activityManager->compile_activity($_REQUEST['pid'],$_REQUEST['activityId']);
  }
  
}




$smarty->assign('source_name',$source);

$fp = fopen($source,"r");
$data = fread($fp,filesize($source));
fclose($fp);
$smarty->assign('data',$data);


$valid = $activityManager->validate_process_activities($_REQUEST['pid']);
$errors = Array();
if(!$valid) {
  $errors = $activityManager->get_error();
  $proc_info['isValid']='n';
} else {
  $proc_info['isValid']='y';
}



$smarty->assign('errors',$errors);


$activities = $activityManager->list_activities($_REQUEST['pid'],0,-1,'name_asc','');
$smarty->assign_by_ref('items',$activities['data']);


$smarty->assign('mid','tiki-g-admin_shared_source.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>  
