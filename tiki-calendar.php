<?php
require_once('tiki-setup.php');
include_once('lib/userslib.php');
include_once('lib/class_calendar.php');
include_once('lib/calendar/calendarlib.php');

$cal = new Calendar('en');
$usercals = $calendarlib->list_user_calIds();

if($feature_calendar != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(isset($_REQUEST["calIds"]) and is_array($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = $_REQUEST["calIds"];
} elseif (!isset($_SESSION['CalendarViewGroups'])) {
	$_SESSION['CalendarViewGroups'] = $usercals;
}

if(isset($_REQUEST["tikicals"]) and is_array($_REQUEST["tikicals"])) {
	$_SESSION['CalendarViewTikiCals'] = $_REQUEST["tikicals"];
} elseif (!isset($_SESSION['CalendarViewTikiCals'])) {
	$_SESSION['CalendarViewTikiCals'] = array();
}

if (!$_SESSION['CalendarViewGroups'] and !$_SESSION['CalendarViewTikiCals']) {
	$smarty->assign('msg',tra("There is no calendar that you can view"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST["viewmode"]) and $_REQUEST["viewmode"]) {
	$_SESSION['CalendarViewMode'] = $_REQUEST["viewmode"];
}
if (!isset($_SESSION['CalendarViewMode']) or !$_SESSION['CalendarViewMode']) $_SESSION['CalendarViewMode'] = 'week';

$smarty->assign('viewmode',$_SESSION['CalendarViewMode']);

$displayedcals = array();
$tikical = array();
foreach ($usercals as $ucal) {
	$listcal = $calendarlib->get_calendar($ucal);
	$listcals[] = $listcal;
	if (is_array($_SESSION['CalendarViewGroups']) && (in_array($listcal['calendarId'],$_SESSION['CalendarViewGroups']))) {
		$displayedcals[] = $listcal;
	}
}
$smarty->assign('listcals',$listcals);
$smarty->assign('displayedcals',$displayedcals);
foreach ($listcals as $whichcal) {
	$thatid = $whichcal["calendarId"];
	if (is_array($_SESSION['CalendarViewGroups']) && (in_array($thatid,$_SESSION['CalendarViewGroups']))) {
		$thiscal[] = 1;
	} else {
		$thiscal[] = 0;
	}
}
$smarty->assign('thiscal',$thiscal);

foreach ($_SESSION['CalendarViewTikiCals'] as $calt) {
	$tikical["$calt"] = 1;
}
$smarty->assign('tikical',$tikical);
$smarty->assign('displayedtikicals',$_SESSION['CalendarViewTikiCals']);

if (isset($_REQUEST["save"]) and ($_REQUEST["save"])) {
	$calendarlib->set_item($user,$calitemId,array(
	"organizer" => $_REQUEST["organizer"],
	"calendarId" => $_REQUEST["calendarId"],
	"start" => $tikilib->make_server_time($_REQUEST["starth_Hour"],$_REQUEST["starth_Minute"],0,
		$_REQUEST["start_Month"],$_REQUEST["start_Day"],$_REQUEST["start_Year"],$tikilib->get_display_timezone($user)),
	"end" => $tikilib->make_server_time($_REQUEST["endh_Hour"],$_REQUEST["endh_Minute"],0,
		$_REQUEST["end_Month"],$_REQUEST["end_Day"],$_REQUEST["end_Year"],$tikilib->get_display_timezone($user)),
	"locationId" => $_REQUEST["locationId"],
	"newloc" => addslashes($_REQUEST["newloc"].' '),
	"categoryId" => $_REQUEST["categoryId"],
	"newcat" => addslashes($_REQUEST["newcat"].' '),
	"public" => $_REQUEST["public"],
	"priority" => $_REQUEST["priority"],
	"status" => $_REQUEST["status"],
	"url" => $_REQUEST["url"],
	"lang" => $_REQUEST["lang"],
	"name" => addslashes(@$_REQUEST["name"]." "),
	"description" => addslashes(@$_REQUEST["description"]." ")
	));
}

if (!isset($_REQUEST["calitemId"])) $_REQUEST["calitemId"] = 0;
if($_REQUEST["calitemId"]) {
	$info = $calendarlib->get_item($_REQUEST["calitemId"]);
	if (!isset($_REQUEST["calendarId"]) or !$_REQUEST["calendarId"]) {
		$_REQUEST["calendarId"] = $info["calendarId"];
	}
} else {
	$info = array();
	$info["calendarId"] = $_SESSION['CalendarViewGroups'][0];
	$info["organizer"] = $user;
	$info["participants"] = '';
	$info["start"] = time();
	$info["end"] = time() + 60*60*2;
	$info["locationId"] = 0;
	$info["categoryId"] = 0;
	$info["priority"] = 5;
	$info["url"] = '';
	$info["lang"] = $lang;
	$info["name"] = '';
	$info["description"] = '';
	$info["created"] = time();
	$info["lastModif"] = time();
}
$smarty->assign('calendarId',$info["calendarId"]);
$smarty->assign('organizer',$info["organizer"]);
$smarty->assign('start',$info["start"]);
$smarty->assign('end',$info["end"]);
$smarty->assign('locationId',$info["locationId"]);
$smarty->assign('categoryId',$info["categoryId"]);
$smarty->assign('priority',$info["priority"]);
$smarty->assign('url',$info["url"]);
$smarty->assign('lang',$info["lang"]);
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('created',$info["created"]);
$smarty->assign('lastModif',$info["lastModif"]);

if(!isset($_REQUEST["editmode"])) $_REQUEST["editmode"]=0;
$smarty->assign('editmode',$_REQUEST["editmode"]);

if ($_REQUEST["editmode"]) {
	$listcat[] = $calendarlib->list_categories($info["calendarId"]);
	$smarty->assign('listcat',$listcat);
	$listloc[] = $calendarlib->list_locations($info["calendarId"]);
	$smarty->assign('listloc',$listloc);
	$listpeople = $calendarlib->list_cal_users($info["calendarId"]);
	$smarty->assign('listpeople',$listpeople);
	$languages=Array();
	$h=opendir("lang/");
	while($file=readdir($h)) {
		if($file!='.' && $file!='..' && is_dir('lang/'.$file) && strlen($file)==2) {
			$languages[]=$file;
		}
	}
	closedir($h);
	$smarty->assign_by_ref('languages',$languages);
}

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];
} else {
  $find = '';
}
$smarty->assign('find',$find);


if(isset($_REQUEST['drop'])) {
	if (is_array($_REQUEST['drop'])) {
		foreach ($_REQUEST['drop'] as $dropme) {
			$calendarlib->drop_item($user, $dropme);
		}
	} else {
  	$calendarlib->drop_item($user, $_REQUEST['drop']);
	}
}

if(!isset($_REQUEST["todate"])) $_REQUEST["todate"]=0;
if ($_REQUEST['todate']) {
	$_SESSION['CalendarFocusDate'] = $_REQUEST['todate'];
}

if(isset($_SESSION['CalendarFocusDate'])) {
	list($focus_day,$focus_month,$focus_year) = array(date("d",$_SESSION['CalendarFocusDate']),date("m",$_SESSION['CalendarFocusDate']),date("Y",$_SESSION['CalendarFocusDate']));
} else {
  list($focus_day,$focus_month,$focus_year) = array(date("d"),date("m"),date("Y"));
}
$z = date("z");

$focus_date = mktime(0,0,0,$focus_month,$focus_day,$focus_year);
$focus_prevday = mktime(0,0,0,$focus_month,$focus_day-1,$focus_year);
$focus_nextday = mktime(0,0,0,$focus_month,$focus_day+1,$focus_year);
$focus_prevweek = mktime(0,0,0,$focus_month,$focus_day-7,$focus_year);
$focus_nextweek = mktime(0,0,0,$focus_month,$focus_day+7,$focus_year);
$focus_prevmonth = mktime(0,0,0,$focus_month-1,$focus_day,$focus_year);
$focus_nextmonth = mktime(0,0,0,$focus_month+1,$focus_day,$focus_year);

$smarty->assign('daybefore',$focus_prevday);
$smarty->assign('weekbefore',$focus_prevweek);
$smarty->assign('monthbefore',$focus_prevmonth);
$smarty->assign('dayafter',$focus_nextday);
$smarty->assign('weekafter',$focus_nextweek);
$smarty->assign('monthafter',$focus_nextmonth);
$smarty->assign('focusmonth',$focus_month);
$smarty->assign('focusdate',$focus_date);
$smarty->assign('now',time());

$weekdays = range(0,6);

$d = 60*60*24;
$currentweek = date("W",$focus_date + $d) - 1;
$wd = date('w',$focus_date);
#if ($wd == 0) $w = 7;
#$wd--;

// calculate timespan for sql query
if ($_SESSION['CalendarViewMode'] == 'month') {
	$firstweek = date("W",mktime(0,0,0,$focus_month,2,$focus_year)) - 1;
	$lastweek = date("W",mktime(0,0,0,$focus_month+1,1,$focus_year)) - 1;
	if ($lastweek < $firstweek) {
		$lastweek += 52;
		$currentweek += 52;
	}
	$viewstart = mktime(0,0,0,1,(7*$firstweek) - 2,$focus_year);
	$viewend = mktime(0,0,-1,1,(7*$lastweek + 1) + 6,$focus_year);
	$numberofweeks = $lastweek - $firstweek;
} elseif ($_SESSION['CalendarViewMode'] == 'week') {
	$firstweek = $currentweek;
	$lastweek = $currentweek;
	$viewstart = $focus_date - ($wd * $d);
	$viewend = $viewstart + ((7 * $d) - 1);
	$numberofweeks = 0;
} else {
	$firstweek = $currentweek;
	$lastweek = $currentweek;
	$viewstart = $focus_date;
	$viewend = $focus_date + ($d - 1);
	$weekdays = array(date('w',$focus_date));
	$numberofweeks = 0;
}
$smarty->assign('viewstart',$viewstart);
$smarty->assign('viewend',$viewend);
$smarty->assign('numberofweeks',$numberofweeks);

$daysnames = array("Sunday","Monday","Thursday","Wednesday","Tuesday","Friday","Saturday");
$weeks = array();
$cell = array();

$listevents = $calendarlib->list_items($_SESSION['CalendarViewGroups'],$user,$viewstart,$viewend,0,50,'name_desc','');
$listtikievents = $calendarlib->list_tiki_items($_SESSION['CalendarViewTikiCals'],$user,$viewstart,$viewend,0,50,'name_desc','');
for ($i=0;$i<=$numberofweeks;$i++) {
	$wee = $firstweek + $i;
	$weeks[] = $wee;
	foreach ($weekdays as $w) {
		$leday = array();
		// hrum. -2 and -1 are black magic. please exorcize if you can
		$dday = mktime(0,0,0,1,(7*($wee))+$w-2,$focus_year);
		$ddayend = mktime(0,0,0,1,(7*($wee))+$w-1,$focus_year);
		$cell[$i][$w]['day'] = $dday;
		if (isset($listevents["$dday"])) {
			foreach ($listevents["$dday"] as $le) {
				$leday["{$le['time']}"] = $le;
			}
		}
		if (isset($listtikievents["$dday"])) {
			foreach ($listtikievents["$dday"] as $lte) {
				$leday["{$lte['time']}"] = $lte;
			}
		}
		if (is_array($leday)) {
			ksort($leday);
			$cell[$i][$w]['items'] = array_values($leday);
		}
	}
}

$smarty->assign('currentweek',$currentweek);
$smarty->assign('firstweek',$firstweek);
$smarty->assign('lastweek',$lastweek);
$smarty->assign('weekdays',$weekdays);
$smarty->assign('weeks',$weeks);
$smarty->assign('daysnames',$daysnames);
$smarty->assign('cell',$cell);

$smarty->assign('mid','tiki-calendar.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
