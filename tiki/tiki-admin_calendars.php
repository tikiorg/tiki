<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/calendar/calendarlib.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["calendarId"])) {
  $_REQUEST["calendarId"] = 0;
}


if(isset($_REQUEST["drop"])) {
  $calendarlib->drop_calendar($_REQUEST["drop"]);
	$_REQUEST["calendarId"] = 0;
}

if(isset($_REQUEST["save"])) {
	$_REQUEST["calendarId"] = $calendarlib->set_calendar($calendarId,$user,$_REQUEST["name"],$_REQUEST["description"],$_REQUEST["public"],$_REQUEST["visible"]);
}


if($_REQUEST["calendarId"]) {
  $info = $calendarlib->get_calendar($_REQUEST["calendarId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["public"]='n';
  $info["visible"]='y';
  $info["user"]="$user";
}
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('public',$info["public"]);
$smarty->assign('visible',$info["visible"]);
$smarty->assign('user',$info["user"]);
$smarty->assign('calendarId',$_REQUEST["calendarId"]);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'name_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 
$smarty->assign_by_ref('sort_mode',$sort_mode);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);


$calendars = $calendarlib->list_calendars(0,-1,$sort_mode,$find,0);
$smarty->assign_by_ref('calendars',$calendars);

$groups = $userlib->get_groups();

$cat_type='calendar';
$cat_objid = $_REQUEST["calendarId"];
include_once("categorize_list.php");


// Display the template
$smarty->assign('mid','tiki-admin_calendars.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
