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
if(isset($_REQUEST["calIds"]) and is_array($_REQUEST["calIds"]) and count($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = $_REQUEST["calIds"];
} elseif (!isset($_SESSION['CalendarViewGroups'])) {
	$_SESSION['CalendarViewGroups'] = $usercals;
} elseif (isset($_REQUEST["refresh"]) and !isset($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array();
}

if(isset($_REQUEST["tikicals"]) and is_array($_REQUEST["tikicals"]) and count($_REQUEST["tikicals"])) {
	$_SESSION['CalendarViewTikiCals'] = $_REQUEST["tikicals"];
} elseif (!isset($_SESSION['CalendarViewTikiCals'])) {
	$_SESSION['CalendarViewTikiCals'] = array();
} elseif (isset($_REQUEST["refresh"]) and !isset($_REQUEST["tikicals"])) {
	$_SESSION['CalendarViewTikiCals'] = array();
}

function dropthat($value) {
	global $match;
	return ($value != $match);
}

if(isset($_REQUEST["hidegroup"]) and $_REQUEST["hidegroup"]) {
	if (is_array($_REQUEST["hidegroup"])) {
		foreach ($_REQUEST["hidegroup"] as $h) {
			$match = $h;
			$_SESSION['CalendarViewGroups'] = array_filter($_SESSION['CalendarViewGroups'],"dropthat");
		}
	} else {
		$match = $_REQUEST["hidegroup"];
		$_SESSION['CalendarViewGroups'] = array_filter($_SESSION['CalendarViewGroups'],"dropthat");
	}
}

if(isset($_REQUEST["hidetiki"]) and $_REQUEST["hidetiki"]) {
	if (is_array($_REQUEST["hidetiki"])) {
		foreach ($_REQUEST["hidetiki"] as $h) {
			$match = $h;
			$_SESSION['CalendarViewTikiCals'] = array_filter($_SESSION['CalendarViewTikiCals'],"dropthat");
		}
	} else {
		$match = $_REQUEST["hidetiki"];
		$_SESSION['CalendarViewTikiCals'] = array_filter($_SESSION['CalendarViewTikiCals'],"dropthat");
	}
}

if (!$_SESSION['CalendarViewGroups'] and !$_SESSION['CalendarViewTikiCals']) {
	$_SESSION['CalendarViewTikiCals'] = array("wiki");
}

if (isset($_REQUEST["todate"]) && $_REQUEST['todate']) {
	$_SESSION['CalendarFocusDate'] = $_REQUEST['todate'];
} elseif (isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
} else {
	$_SESSION['CalendarFocusDate'] = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
}
$focusdate = $_REQUEST['todate'];
list($focus_day,$focus_month,$focus_year) = array(date("d",$focusdate),date("m",$focusdate),date("Y",$focusdate));

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

if (isset($_REQUEST["delete"]) and ($_REQUEST["delete"])) {
	$calendarlib->drop_item($user,$calitemId);
	$_REQUEST["calitemId"] = 0;
}

if (!isset($_REQUEST["calitemId"])) $_REQUEST["calitemId"] = 0;
if (!isset($_REQUEST["locationId"])) $_REQUEST["locationId"] = 0;
if (!isset($_REQUEST["categoryId"])) $_REQUEST["categoryId"] = 0;
if (!isset($_REQUEST["status"])) $_REQUEST["status"] = 0;

if (isset($_REQUEST["copy"]) and ($_REQUEST["copy"])) {
	$_REQUEST["calitemId"] = 0;
	$_REQUEST["save"] = true;
}

if (isset($_REQUEST["save"]) and ($_REQUEST["save"])) {
	if (!isset($_REQUEST["name"]) or !(trim($_REQUEST["name"]))) {
		$_REQUEST["name"] = tra("event without name");
	}
	$_REQUEST["calitemId"] = $calendarlib->set_item($user,$_REQUEST["calitemId"],array(
	"user" => $user,
	"organizers" => $_REQUEST["organizers"],
	"participants" => $_REQUEST["participants"],
	"calendarId" => $_REQUEST["calendarId"],
	"start" => mktime($_REQUEST["starth_Hour"],$_REQUEST["starth_Minute"],0,
		$_REQUEST["start_Month"],$_REQUEST["start_Day"],$_REQUEST["start_Year"]),
	"end" => mktime($_REQUEST["endh_Hour"],$_REQUEST["endh_Minute"],0,
		$_REQUEST["end_Month"],$_REQUEST["end_Day"],$_REQUEST["end_Year"]),
	"locationId" => $_REQUEST["locationId"],
	"newloc" => addslashes($_REQUEST["newloc"].' '),
	"categoryId" => $_REQUEST["categoryId"],
	"newcat" => addslashes($_REQUEST["newcat"].' '),
	"public" => $_REQUEST["public"],
	"priority" => $_REQUEST["priority"],
	"status" => $_REQUEST["status"],
	"url" => $_REQUEST["url"],
	"lang" => $_REQUEST["lang"],
	"name" => addslashes($_REQUEST["name"]),
	"description" => addslashes(@$_REQUEST["description"]." ")
	));
}

if (isset($_SESSION['CalendarViewGroups'][0])) {
	$defaultcalId = $_SESSION['CalendarViewGroups'][0];
} else {
	$defaultcalId	= 0;
}

if($_REQUEST["calitemId"]) {
	$info = $calendarlib->get_item($_REQUEST["calitemId"]);
} else {
	$info = array();
	$info["calitemId"] = "";
	$info["calendarId"] = $defaultcalId;
	$info["user"] = "";
	$info["groupname"] = "";
	$info["calname"] = "";
	$info["organizers"] = $user.",";
	$info["participants"] = $user.":0,";
	$info["start"] = $focusdate + date("H")*60*60;
	$info["end"] = $focusdate + (date("H")+2)*60*60;
	$info["locationId"] = 0;
	$info["locationName"] = '';
	$info["categoryId"] = 0;
	$info["categoryName"] = '';
	$info["priority"] = 5;
	$info["url"] = '';
	$info["lang"] = $tikilib->get_user_preference($user,"language");
	$info["name"] = '';
	$info["description"] = '';
	$info["created"] = time();
	$info["lastModif"] = time();
	$info["public"] = 'y';
	$info["status"] = '0';
}
if (!isset($_REQUEST["calendarId"]) or !$_REQUEST["calendarId"]) {
	$_REQUEST["calendarId"] = $info["calendarId"];
}
$smarty->assign('calitemId',$info["calitemId"]);
$smarty->assign('calendarId',$_REQUEST["calendarId"]);
$smarty->assign('organizers',$info["organizers"]);
$smarty->assign('participants',$info["participants"]);
$smarty->assign('groupname',$info["groupname"]);
$smarty->assign('calname',$info["calname"]);
$smarty->assign('start',$info["start"]);
$smarty->assign('end',$info["end"]);
$smarty->assign('locationId',$info["locationId"]);
$smarty->assign('locationName',$info["locationName"]);
$smarty->assign('categoryId',$info["categoryId"]);
$smarty->assign('categoryName',$info["categoryName"]);
$smarty->assign('priority',$info["priority"]);
$smarty->assign('public',$info["public"]);
$smarty->assign('url',$info["url"]);
$smarty->assign('lang',$info["lang"]);
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('created',$info["created"]);
$smarty->assign('lastModif',$info["lastModif"]);
$smarty->assign('lastUser',$info["user"]);
$smarty->assign('status',$info["status"]);

if(!isset($_REQUEST["editmode"])) $_REQUEST["editmode"]=0;
$smarty->assign('editmode',$_REQUEST["editmode"]);

if ($_REQUEST["editmode"]) {
	$listcat = $calendarlib->list_categories($_REQUEST["calendarId"]);
	$smarty->assign('listcat',$listcat);
	$listloc = $calendarlib->list_locations($_REQUEST["calendarId"]);
	$smarty->assign('listloc',$listloc);
	$listpeople = $calendarlib->list_cal_users($_REQUEST["calendarId"]);
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
$z = date("z");

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
$smarty->assign('focusdate',$focusdate);
$smarty->assign('now',mktime(0,0,0,date('m'),date('d'),date('Y')) );

$weekdays = range(0,6);

$d = 60*60*24;
$currentweek = date("W",$focusdate + $d) - 1;
$wd = date('w',$focusdate);
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
	$viewstart = $focusdate - ($wd * $d);
	$viewend = $viewstart + ((7 * $d) - 1);
	$numberofweeks = 0;
} else {
	$firstweek = $currentweek;
	$lastweek = $currentweek;
	$viewstart = $focusdate;
	$viewend = $focusdate + ($d - 1);
	$weekdays = array(date('w',$focusdate));
	$numberofweeks = 0;
}
$smarty->assign('viewstart',$viewstart);
$smarty->assign('viewend',$viewend);
$smarty->assign('numberofweeks',$numberofweeks);

$daysnames = array("Sunday","Monday","Thursday","Wednesday","Tuesday","Friday","Saturday");
$weeks = array();
$cell = array();

if ($_SESSION['CalendarViewGroups']) {
	$listevents = $calendarlib->list_items($_SESSION['CalendarViewGroups'],$user,$viewstart,$viewend,0,50,'name_desc','');
} else {
	$listevents = array();
}
if ($_SESSION['CalendarViewTikiCals']) {
	$listtikievents = $calendarlib->list_tiki_items($_SESSION['CalendarViewTikiCals'],$user,$viewstart,$viewend,0,50,'name_desc','');
} else {
	$listtikievents = array();
}
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
 
 
