<?php
require_once('tiki-setup.php');
include_once('lib/userslib.php');
include_once('lib/calendar/calendarlib.php');

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
if (!$_SESSION['CalendarViewMode']) $_SESSION['CalendarViewMode'] = 'week';

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

if(!isset($_REQUEST["calcatIdedit"])) $_REQUEST["calcatIdedit"]=$_SESSION['CalendarViewGroups'][0];
if(!isset($_REQUEST["editmode"])) $_REQUEST["editmode"]=0;
$smarty->assign('editmode',$_REQUEST["editmode"]);

if ($_REQUEST["editmode"]) {
	$listcat[] = $calendarlib->list_categories($_REQUEST["calcatIdedit"]);
	$smarty->assign('listcat',$listcat);
	$listloc[] = $calendarlib->list_locations($_REQUEST["calcatIdedit"]);
	$smarty->assign('listloc',$listloc);
	if(!isset($_REQUEST["calcatIdedit"])) {
		$_REQUEST["calcatIdedit"] = $_SESSION['CalendarViewGroups'][0];
	}
	$smarty->assign('calcatIdedit',$_REQUEST["calcatIdedit"]);
	$listpeople = $calendarlib->list_cal_users($_REQUEST["calcatIdedit"]);
	$smarty->assign('listpeople',$listpeople);
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
$smarty->assign('day',$focus_day);
$smarty->assign('month',$focus_month);
$smarty->assign('year',$focus_year);
$smarty->assign('focusdate',$focus_date);
$smarty->assign('now',time());


$firstweek = date("W",mktime(0,0,0,$focus_month,-5,$focus_year));
$firstday = mktime(0,0,0,1,(7*$firstweek)-2,$focus_year);
$lastweek = date("W",mktime(0,0,0,$focus_month+1,-7,$focus_year));
$lastday = mktime(0,0,0,1,(7*$lastweek)+4,$focus_year);
$firstweekday = mktime(0,0,0,0,7*$firstweek,0,$focus_year);
$weekdays = array(0,1,2,3,4,5,6);
$daysnames = array("Sunday","Monday","Thursday","Wednesday","Tuesday","Friday","Saturday");
$weeks = array();
$cell = array();

$listevents = $calendarlib->list_items($_SESSION['CalendarViewGroups'],$user,$firstday,$lastday,0,50,'name_desc','');
$listtikievents = $calendarlib->list_tiki_items($_SESSION['CalendarViewTikiCals'],$user,$firstday,$lastday,0,50,'name_desc','');
for ($i=0;$i<=$lastweek-$firstweek;$i++) {
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

$smarty->assign('firstweek',$firstweek);
$smarty->assign('lastweek',$lastweek);
$smarty->assign('firstday',$firstday);
$smarty->assign('lastday',$lastday);
$smarty->assign('weekdays',$weekdays);
$smarty->assign('weeks',$weeks);
$smarty->assign('daysnames',$daysnames);
$smarty->assign('cell',$cell);



$smarty->assign('mid','tiki-calendar.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
 
 
