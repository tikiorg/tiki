<?php
require_once('tiki-setup.php');
include_once('lib/minical/minicallib.php');

if($feature_minical != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


//if($tiki_p_usermenu != 'y') {
//  $smarty->assign('msg',tra("Permission denied to use this feature"));
//  $smarty->display("styles/$style_base/error.tpl");
//  die;  
//}


if(!isset($_REQUEST["eventId"])) $_REQUEST["eventId"]=0;

if(isset($_REQUEST['remove'])) {
//  foreach(array_keys($_REQUEST["menu"]) as $men) {      	
    $minicallib->minical_remove_event($user, $_REQUEST['remove']);
//  }
}

if(isset($_SESSION['thedate'])) {
  $pdate = mktime(0,0,0,date("m",$_SESSION['thedate']),date("d",$_SESSION['thedate']),date("Y",$_SESSION['thedate']));
} else {
  $pdate = date("U");
}
$pdate_h = mktime(date("G"),date("i"),date("s"),date("m",$pdate),date("d",$pdate),date("Y",$pdate));
$smarty->assign('pdate',$pdate);
$smarty->assign('pdate_h',$pdate_h);

if($_REQUEST["eventId"]) {
  $info = $minicallib->minical_get_event($user,$_REQUEST["eventId"]);
} else {
  $info=Array();
  $info['title']='';
  $info['description']='';
  $info['start']=mktime(date("H"),date("i"),date("s"),date("m",$pdate),date("d",$pdate),date("Y",$pdate));
  $info['duration']=1;
}


if(isset($_REQUEST['save'])) {
  $start = mktime($_REQUEST['Time_Hour'],$_REQUEST['Time_Minute'],0,date("m",$pdate),date("d",$pdate),date("Y",$pdate));
  $minicallib->minical_replace_event($user,$_REQUEST["eventId"],$_REQUEST["title"],$_REQUEST["description"],$start,$_REQUEST['duration']);
  $info=Array();
  $info['title']='';
  $info['description']='';
  $info['start']=mktime(date("h"),date("i"),date("s"),date("m",$pdate),date("d",$pdate),date("Y",$pdate));  
  $info['duration']=1;
  $_REQUEST["eventId"]=0;
}
$smarty->assign('eventId',$_REQUEST["eventId"]);
$smarty->assign('info',$info);

$slot_start = $pdate + 60*60*9;
$slot_end = $pdate + 60*60*20;
$smarty->assign('slot_start',$slot_start);
$smarty->assign('slot_end',$slot_end);

$events = $minicallib->minical_events_by_slot($user,$slot_start,$slot_end,1);
//print_r($events);
$smarty->assign_by_ref('slots',$events);


include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-minical.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
