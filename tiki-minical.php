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


//if($tiki_p_minical != 'y') {
//  $smarty->assign('msg',tra("Permission denied to use this feature"));
//  $smarty->display("styles/$style_base/error.tpl");
//  die;  
//}


if(!isset($_REQUEST["eventId"])) $_REQUEST["eventId"]=0;

if(isset($_REQUEST['remove'])) {
  $minicallib->minical_remove_event($user, $_REQUEST['remove']);
}

if(isset($_REQUEST['delete'])) {
  foreach(array_keys($_REQUEST["event"]) as $ev) {      	
    $minicallib->minical_remove_event($user, $ev);
  }
}

if(isset($_SESSION['thedate'])) {
  $pdate = mktime(0,0,0,date("m",$_SESSION['thedate']),date("d",$_SESSION['thedate']),date("Y",$_SESSION['thedate']));
} else {
  $pdate = date("U");
}

$yesterday = $pdate - 60*60*24;
$tomorrow = $pdate + 60*60*24;
$smarty->assign('yesterday',$yesterday);
$smarty->assign('tomorrow',$tomorrow);
$smarty->assign('day',date("d",$pdate));
$smarty->assign('mon',date("m",$pdate));
$smarty->assign('year',date("Y",$pdate));
$pdate_h = mktime(date("G"),date("i"),date("s"),date("m",$pdate),date("d",$pdate),date("Y",$pdate));
$smarty->assign('pdate',$pdate);
$smarty->assign('pdate_h',$pdate_h);

$ev_pdate = $pdate;
$ev_pdate_h = $pdate_h;
if($_REQUEST["eventId"]) {
  $info = $minicallib->minical_get_event($user,$_REQUEST["eventId"]);
  $ev_pdate = $info['start'];
  $ev_pdate_h = $info['start'];
} else {
  $info=Array();
  $info['title']='';
  $info['description']='';
  $info['start']=mktime(date("H"),date("i"),date("s"),date("m",$pdate),date("d",$pdate),date("Y",$pdate));
  $info['duration']=60*60;
}
$smarty->assign('ev_pdate',$ev_pdate);
$smarty->assign('ev_pdate_h',$ev_pdate_h);


if(isset($_REQUEST['save'])) {
  $start = mktime($_REQUEST['Time_Hour'],$_REQUEST['Time_Minute'],0,$_REQUEST['Date_Month'],$_REQUEST['Date_Day'],$_REQUEST['Date_Year']);
  $minicallib->minical_replace_event($user,$_REQUEST["eventId"],$_REQUEST["title"],$_REQUEST["description"],$start,($_REQUEST['duration_hours']*60*60)+($_REQUEST['duration_minutes']*60));
  $info=Array();
  $info['title']='';
  $info['description']='';
  $info['start']=mktime(date("h"),date("i"),date("s"),date("m",$pdate),date("d",$pdate),date("Y",$pdate));  
  $info['duration']=60*60;
  $_REQUEST["eventId"]=0;
}
$smarty->assign('eventId',$_REQUEST["eventId"]);
$smarty->assign('info',$info);

//Check here the interval for the calendar
if(!isset($_REQUEST['view'])) {
  $_REQUEST['view']='daily';
}
$smarty->assign('view',$_REQUEST['view']);

$minical_interval = $tikilib->get_user_preference($user,'minical_interval',60*60);
$minical_start_hour = $tikilib->get_user_preference($user,'minical_start_hour',9);
$minical_end_hour = $tikilib->get_user_preference($user,'minical_end_hour',20);
$minical_public = $tikilib->get_user_preference($user,'minical_public','n');

// Interval is in hours
if($_REQUEST['view']=='daily') {
	$slot_start = $pdate + 60*60*$minical_start_hour;
	$slot_end = $pdate + 60*60*$minical_end_hour;
	$interval = $minical_interval;
}
if($_REQUEST['view']=='weekly') {
	$interval=24*60*60;
	// Determine weekday
	$wd = date('w',$pdate);
	if($wd==0) $w=7;
	$wd=$wd-1;
	// Now get the number of days to substract
	$week_start = $pdate - ($wd*60*60*24);
	$week_end = $week_start + 60*60*24*7-1;
	$smarty->assign('week_start',$week_start);
	$smarty->assign('week_end',$week_end);
	$next_week_start = $week_end+1;
	$smarty->assign('next_week_start',$next_week_start);
	$prev_week_start = $week_start - (60*60*24*7);
	$smarty->assign('prev_week_start',$prev_week_start);
	$slot_start = $pdate - ($wd*60*60*24);
	$slot_end = $slot_start + 60*60*24*7-1;
}

if($_REQUEST['view']=='daily' || $_REQUEST['view']=='weekly') {
  $smarty->assign('slot_start',$slot_start);
  $smarty->assign('slot_end',$slot_end);
  $events = $minicallib->minical_events_by_slot($user,$slot_start,$slot_end,$interval);
  $smarty->assign_by_ref('slots',$events);
}

// List view
if($_REQUEST['view']=='list') {
	if(!isset($_REQUEST["sort_mode"])) {
	  $sort_mode = 'start_desc'; 
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
	$channels = $minicallib->minical_list_events($user,$offset,$maxRecords,$sort_mode,$find);
	
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
}

$hours=range(0,23);
$smarty->assign('hours',$hours);
$minutes=range(0,59);
$smarty->assign('minutes',$minutes);
$duration_hours = $info['duration']/(60*60);
$duration_minutes = $info['duration']%(60*60);
$smarty->assign('duration_hours',$duration_hours);
$smarty->assign('duration_minutes',$duration_minutes);


include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-minical.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
