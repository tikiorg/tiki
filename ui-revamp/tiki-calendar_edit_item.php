<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-calendar_edit_item.php,v 1.21.2.4 2008-01-17 15:53:26 tombombadilom Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$section = 'calendar';
require_once ('tiki-setup.php');

if ($prefs['feature_calendar'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
  $smarty->display("error.tpl");
  die;
}
include_once ('lib/calendar/calendarlib.php');
include_once ('lib/newsletters/nllib.php');
include_once ('lib/calendar/calrecurrence.php');
if ($prefs['feature_groupalert'] == 'y') {
	include_once ('lib/groupalert/groupalertlib.php');
}
if ($prefs['feature_ajax'] == "y") {
	require_once ('lib/ajax/ajaxlib.php');
}
/*
if (isset($_REQUEST['calendarId']) and $userlib->object_has_one_permission($_REQUEST['calendarId'],'calendar')) {
  if ($tiki_p_admin != 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'calendar');
    foreach ($perms["data"] as $perm) {
      $permName = $perm["permName"];
      if ($userlib->object_has_permission($user, $calendarId, 'calendar', $permName)) {
        $$permName = 'y';
        $smarty->assign("$permName", 'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName", 'n');
      }
    }
  }
}
*/

$daysnames = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Satursday");
$monthnames = array("","January","February","March","April","May","June","July","August","September","October","November","December");
$smarty->assign('daysnames',$daysnames);
$smarty->assign('monthnames',$monthnames);

$smarty->assign('edit',false);
$hours_minmax = '';

if ($tiki_p_admin_calendar == 'y') {
  $tiki_p_add_events = 'y';
  $smarty->assign('tiki_p_add_events','y');
  $tiki_p_change_events = 'y';
  $smarty->assign('tiki_p_change_events','y');
  $tiki_p_view_events = 'y';
  $smarty->assign('tiki_p_view_events','y');
  $tiki_p_view_calendar = 'y';
  $smarty->assign('tiki_p_view_calendar','y');
}

$caladd = array();
$rawcals = $calendarlib->list_calendars();
if ($rawcals['cant'] == 0 && $tiki_p_admin_calendar == 'y') {
	$smarty->assign('msg', tra('You need to create one calendar'));
	$smarty->display("error.tpl");
	die;
}
foreach ($rawcals["data"] as $cal_id=>$cal_data) {
  if ($tiki_p_admin == 'y') {
    $cal_data["tiki_p_view_calendar"] = 'y';
    $cal_data["tiki_p_view_events"] = 'y';
    $cal_data["tiki_p_add_events"] = 'y';
    $cal_data["tiki_p_change_events"] = 'y';
  } elseif ($cal_data["personal"] == "y") {
    if ($user) {
      $cal_data["tiki_p_view_calendar"] = 'y';
    	$cal_data["tiki_p_view_events"] = 'y';
      $cal_data["tiki_p_add_events"] = 'y';
      $cal_data["tiki_p_change_events"] = 'y';
    } else {
      $cal_data["tiki_p_view_calendar"] = 'n';
    	$cal_data["tiki_p_view_events"] = 'y';
      $cal_data["tiki_p_add_events"] = 'n';
      $cal_data["tiki_p_change_events"] = 'n';
    }
  } else {
    if ($userlib->object_has_one_permission($cal_id,'calendar')) {
      if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_admin_calendar')) {
        $cal_data["tiki_p_view_calendar"] = 'y';
        $cal_data["tiki_p_add_events"] = 'y';
        $cal_data["tiki_p_change_events"] = 'y';
      } else {
				if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
					$cal_data["tiki_p_view_calendar"] = 'y';
				} else {
					$cal_data["tiki_p_view_calendar"] = 'n';
				}
				if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_view_events')) {
					$cal_data["tiki_p_view_events"] = 'y';
				} else {
					$cal_data["tiki_p_view_events"] = 'n';
				}
				if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_add_events')) {
					$cal_data["tiki_p_add_events"] = 'y';
					$tiki_p_add_events = "y";
					$smarty->assign("tiki_p_add_events", "y");
				} else {
					$cal_data["tiki_p_add_events"] = 'n';
				}
				if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_change_events')) {
					$cal_data["tiki_p_change_events"] = 'y';
				} else {
					$cal_data["tiki_p_change_events"] = 'n';
				}
				$smarty->assign("tiki_p_change_events", $cal_data["tiki_p_change_events"] );
			}
    } else {
      $cal_data["tiki_p_view_calendar"] = $tiki_p_view_calendar;
      $cal_data["tiki_p_view_events"] = $tiki_p_view_events;
      $cal_data["tiki_p_add_events"] = $tiki_p_add_events;
      $cal_data["tiki_p_change_events"] = $tiki_p_change_events;
    }
  }
	$caladd["$cal_id"] = $cal_data;
}
$smarty->assign('listcals',$caladd);

if ( ! isset($_REQUEST["calendarId"]) ) {
	if (isset($_REQUEST['calitemId'])) {
		$calID = $calendarlib->get_calendarid($_REQUEST['calitemId']);
	} elseif (isset($_REQUEST['viewcalitemId'])) {
		$calID = $calendarlib->get_calendarid($_REQUEST['viewcalitemId']);
	}
} elseif (isset($_REQUEST['calendarId'])) {
		$calID=$_REQUEST["calendarId"];
}

if ($prefs['feature_groupalert'] == 'y') {
	$groupforalert=$groupalertlib->GetGroup ('calendar',$calID);
	if ( $groupforalert != '' ) {
		$showeachuser=$groupalertlib->GetShowEachUser('calendar',$calID, $groupforalert) ;
		$listusertoalert=$userlib->get_users(0,-1,'login_asc','','',false,$groupforalert,'') ;
		$smarty->assign_by_ref('listusertoalert',$listusertoalert['data']);
	}
	$smarty->assign_by_ref('groupforalert',$groupforalert);
	$smarty->assign_by_ref('showeachuser',$showeachuser);
}

if (!isset($_REQUEST['calendarId']) and count($caladd)) {
	$keys = array_keys($caladd);
	$_REQUEST['calendarId'] = array_shift($keys);
}
if ($prefs['feature_categories'] == 'y') {
  global $categlib; include_once ('lib/categories/categlib.php');
  $perms_array = $categlib->get_object_categories_perms($user, 'calendar', $_REQUEST['calendarId']);
  if ($perms_array) {
    foreach ($perms_array as $p=>$v) {
      $$p = $v;
    }
    if (isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
		$smarty->assign('errortype', 401);
      $smarty->assign('msg',tra("Permission denied you cannot view this page"));
      $smarty->display("error.tpl");
      die;
    }
  }
}
if (isset($_REQUEST['save']) && !isset($_REQUEST['preview']) && !isset($_REQUEST['act'])) {
	$_REQUEST['changeCal'] = 'y';
}

if (isset($_REQUEST['act']) || isset($_REQUEST['preview']) || isset($_REQUEST['changeCal'])) {
	$save = $_POST['save'];
	// Take care of timestamps dates coming from jscalendar
	if ( isset($save['date_start']) || isset($save['date_end']) ) {
		$_REQUEST['start_date_Month'] = TikiLib::date_format("%m", $save['date_start']);
		$_REQUEST['start_date_Day'] = TikiLib::date_format("%d", $save['date_start']);
		$_REQUEST['start_date_Year'] = TikiLib::date_format("%Y", $save['date_start']);
		$_REQUEST['end_date_Month'] = TikiLib::date_format("%m", $save['date_end']);
		$_REQUEST['end_date_Day'] = TikiLib::date_format("%d", $save['date_end']);
		$_REQUEST['end_date_Year'] = TikiLib::date_format("%Y", $save['date_end']);

	}
    $save['allday'] = $_REQUEST['allday'] == 'true' ? 1 : 0;
	$save['start'] = TikiLib::make_time(
		$_REQUEST['start_Hour'],
		$_REQUEST['start_Minute'],
		0,
		$_REQUEST['start_date_Month'],
		$_REQUEST['start_date_Day'],
		$_REQUEST['start_date_Year']
	);

	if ($save['end_or_duration'] == 'duration') {
		$save['duration'] = max(0, $_REQUEST['duration_Hour']*60*60 + $_REQUEST['duration_Minute']*60);
		$save['end'] = $save['start'] + $save['duration'];
	} else {
		$save['end'] = TikiLib::make_time(
			$_REQUEST['end_Hour'],
			$_REQUEST['end_Minute'],
			0,
			$_REQUEST['end_date_Month'],
			$_REQUEST['end_date_Day'],
			$_REQUEST['end_date_Year']
		);
		$save['duration'] = max(0, $save['end'] - $save['start']);
	}
}

$impossibleDates = false;
if (isset($save['start']) && isset($save['end'])) {
	if (($save['end'] - $save['start']) < 0)
		$impossibleDates = true;
}

if (isset($_POST['act'])) {
	if (empty($save['user'])) $save['user'] = $user;
	$newcalid = $save['calendarId'];

	if ((empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_add_events'])
	or (!empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_change_events'])) {
		if (empty($save['name'])) $save['name'] = tra("event without name");
		if (empty($save['priority'])) $save['priority'] = 0;

		if (array_key_exists('recurrent',$_POST) && ($_POST['recurrent'] == 1) && $_POST['affect']!='event') {
			$impossibleDates = false;
			if ($_POST['end_Hour'] < $_POST['start_Hour']) {
				$impossibleDates = true;
			} elseif (($_POST['end_Hour'] == $_POST['start_Hour']) && ($_POST['end_Minute'] < $_POST['start_Minute'])) {
				$impossibleDates = true;
			} else
				$impossibleDates = false;
			if (!$impossibleDates) {
			$calRecurrence = new CalRecurrence($_POST['recurrenceId'] ? $_POST['recurrenceId'] : -1);
			$calRecurrence->setCalendarId($save['calendarId']);
			$calRecurrence->setStart($_POST['start_Hour'] . str_pad($_POST['start_Minute'],2,'0',STR_PAD_LEFT));
			$calRecurrence->setEnd($_POST['end_Hour'] . str_pad($_POST['end_Minute'],2,'0',STR_PAD_LEFT));
			$calRecurrence->setAllday($save['allday'] == 1);
			$calRecurrence->setLocationId($save['locationId']);
			$calRecurrence->setCategoryId($save['categoryId']);
			$calRecurrence->setNlId(0); //TODO : What id nlId ?
			$calRecurrence->setPriority($save['priority']);
			$calRecurrence->setStatus($save['status']);
			$calRecurrence->setUrl($save['url']);
			$calRecurrence->setLang(strLen($save['lang']) > 0 ? $save['lang'] : 'en');
			$calRecurrence->setName($save['name']);
			$calRecurrence->setDescription($save['description']);
			switch($_POST['recurrenceType']) {
				case "weekly":
					$calRecurrence->setWeekly(true);
					$calRecurrence->setWeekday($_POST['weekday']);
					$calRecurrence->setMonthly(false);
					$calRecurrence->setYearly(false);
					break;
				case "monthly":
					$calRecurrence->setWeekly(false);
					$calRecurrence->setMonthly(true);
					$calRecurrence->setDayOfMonth($_POST['dayOfMonth']);
					$calRecurrence->setYearly(false);
					break;
				case "yearly":
					$calRecurrence->setWeekly(false);
					$calRecurrence->setMonthly(false);
					$calRecurrence->setYearly(true);
					$calRecurrence->setDateOfYear(str_pad($_POST['dateOfYear_month'],2,'0',STR_PAD_LEFT) . str_pad($_POST['dateOfYear_day'],2,'0',STR_PAD_LEFT));
					break;
			}
			$calRecurrence->setStartPeriod($_POST['startPeriod']);
			if ($_POST['endType'] == "dt")
				$calRecurrence->setEndPeriod($_POST['endPeriod']);
			else {
				$calRecurrence->setNbRecurrences($_POST['nbRecurrences']);
			}
			$calRecurrence->setUser($save['user']);
			$calRecurrence->save($_POST['affect'] == 'all');
			header('Location: tiki-calendar.php');
			die;
			}
		} else {
			if (!$impossibleDates) {
			if (array_key_exists('recurrenceId',$_POST)) {
				$save['recurrenceId'] = $_POST['recurrenceId'];
				$save['changed'] = true;
			}
			$calitemId = $calendarlib->set_item($user,$save['calitemId'],$save);
			header('Location: tiki-calendar.php');
			die;
		}

		if ($prefs['feature_groupalert'] == 'y') {
			$groupalertlib->Notify($_REQUEST['listtoalert'],"tiki-calendar_edit_item.php?viewcalitemId=".$calitemId);
		}

		header('Location: tiki-calendar.php');
		die;
	}
}
}

if (isset($_REQUEST["delete"]) and ($_REQUEST["delete"]) and isset($_REQUEST["calitemId"]) and $tiki_p_change_events == 'y') {
  $area = 'delcalevent';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
    $calendarlib->drop_item($user, $_REQUEST["calitemId"]);
    $_REQUEST["calitemId"] = 0;
		header('Location: tiki-calendar.php');
		die;
  } else {
    key_get($area);
  }
} elseif (isset($_REQUEST["delete"]) and ($_REQUEST["delete"]) and isset($_REQUEST["recurrenceId"]) and $tiki_p_change_events == 'y') {
	$calRec = new CalRecurrence($_REQUEST['recurrenceId']);
	$calRec->delete();
    $_REQUEST["recurrenceTypeId"] = 0;
    $_REQUEST["calitemId"] = 0;
	header('Location: tiki-calendar.php');
	die;
} elseif (isset($_REQUEST['drop']) and $tiki_p_change_events == 'y') {
  check_ticket('calendar');
  if (is_array($_REQUEST['drop'])) {
    foreach ($_REQUEST['drop'] as $dropme) {
      $calendarlib->drop_item($user, $dropme);
    }
  } else {
    $calendarlib->drop_item($user, $_REQUEST['drop']);
  }
	header('Location: tiki-calendar.php');
	die;
}  elseif (isset($_REQUEST['duplicate']) and $tiki_p_add_events == 'y') {
	$calitem = $calendarlib->get_item($_REQUEST['calitemId']);
	$calitem['calendarId'] = $_REQUEST['calendarId'];
	$calitem['calitemId'] = 0;
	$calendarlib->set_item($user,0,$calitem);
	$id = 0;
	if (isset($_REQUEST['calId'])) {
		$calendar = $calendarlib->get_calendar($_REQUEST['calId']);
  } else {
		$calendar = $calendarlib->get_calendar($calitem['calendarId']);
  }
	$smarty->assign('edit',true);
	$hour_minmax = floor(($calendar['startday']-1)/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} elseif (isset($_REQUEST['preview']) || $impossibleDates) {
	$save['parsed'] = $tikilib->parse_data($save['description']);
	$save['parsedName'] = $tikilib->parse_data($save['name']);
	$id = $save['calitemId'];
	$calitem = $save;
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$smarty->assign('edit',true);
	$smarty->assign('preview', isset($_REQUEST['preview']));
} elseif (isset($_REQUEST['changeCal'])) {
	$calitem = $save;
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$smarty->assign('edit',true);
	$smarty->assign('changeCal', isset($_REQUEST['changeCal']));
	$_REQUEST['calendarId'] = $save['calendarId'];
} elseif (isset($_REQUEST['viewcalitemId']) and $tiki_p_view_events == 'y') {
	$calitem = $calendarlib->get_item($_REQUEST['viewcalitemId']);
	$id = $_REQUEST['viewcalitemId'];
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$hour_minmax = ceil(($calendar['startday'])/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
	$_REQUEST['calendarId'] = $calitem['calendarId'];
} elseif (isset($_REQUEST['calitemId']) and ($tiki_p_change_events == 'y' or $tiki_p_view_events == 'y')) {
	$calitem = $calendarlib->get_item($_REQUEST['calitemId']);
	$id = $_REQUEST['calitemId'];
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$smarty->assign('edit',true);
	$hour_minmax = ceil(($calendar['startday'])/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
	$_REQUEST['calendarId'] = $calitem['calendarId'];
} elseif (isset($_REQUEST['calendarId']) and $tiki_p_add_events == 'y') {
	if (isset($_REQUEST['todate'])) {
		$now = $_REQUEST['todate'];
	} else {
		$now = $tikilib->now;
	}
	$calendar = $calendarlib->get_calendar($_REQUEST['calendarId']);
	$calitem = array(
		'calitemId'=>0,
		'user'=>$user,
		'name'=>'',
		'url'=>'',
		'description'=>'',
		'status'=>$calendar['defaulteventstatus'],
		'priority'=>0,
		'locationId'=>0,
		'categoryId'=>0,
		'nlId'=>0,
		'start'=>$now,
		'end'=>$now+(60*60),
		'duration'=>(60*60)
		);
	$id = 0;
	$smarty->assign('edit',true);
	$hour_minmax = floor(($calendar['startday']-1)/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} else {
  $smarty->assign('errortype', 401);
  $smarty->assign('msg', tra("Permission denied you can not view this page"));
  $smarty->display("error.tpl");
  die;
}

if (!empty($calendar['eventstatus'])) {
    $calitem['status'] = $calendar['eventstatus'];
}

if ($calendar['customlocations'] == 'y') {
	$listlocs = $calendarlib->list_locations($_REQUEST['calendarId']);
} else {
	$listlocs = array();
}
$smarty->assign('listlocs', $listlocs);

if ($calendar['customcategories'] == 'y') {
	$listcats = $calendarlib->list_categories($_REQUEST['calendarId']);
} else {
	$listcats = array();
}
$smarty->assign('listcats', $listcats);

if ($calendar["customsubscription"] == 'y') {
	$subscrips = $nllib->list_avail_newsletters();
} else {
	$subscrips = array();
}
$smarty->assign('subscrips', $subscrips);

if ($calendar["customlanguages"] == 'y') {
	$languages = $tikilib->list_languages();
} else {
	$languages = array();
}
$smarty->assign('listlanguages', $languages);

$smarty->assign('listpriorities',array('0','1','2','3','4','5','6','7','8','9'));
$smarty->assign('listprioritycolors',array('fff','fdd','fcc','fbb','faa','f99','e88','d77','c66','b66','a66'));
$smarty->assign('listroles',array('0'=>'','1'=>tra('required'),'2'=>tra('optional'),'3'=>tra('non participant')));


if ($prefs['feature_theme_control'] == 'y') {
  $cat_type = "calendar";
  $cat_objid = $_REQUEST['calendarId'];
  include('tiki-tc.php');
}

$headerlib->add_cssfile('css/calendar.css',20);

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','wiki');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
include_once("textareasize.php");

$smarty->assign('myurl', 'tiki-calendar_edit_item.php');
$smarty->assign('id', $id);
$smarty->assign('hour_minmax', $hour_minmax);
if ($calitem['recurrenceId'] > 0) {
	$cr = new CalRecurrence($calitem['recurrenceId']);
	$smarty->assign('recurrence',$cr->toArray());
}
$smarty->assign('calitem', $calitem);
$smarty->assign('calendar', $calendar);
$smarty->assign('calendarId', $_REQUEST['calendarId']);
if (array_key_exists('CalendarViewGroups',$_SESSION) && count($_SESSION['CalendarViewGroups']) == 1)
	$smarty->assign('calendarView',$_SESSION['CalendarViewGroups'][0]);
if ($prefs['feature_ajax'] == "y") {
function edit_calendar_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-calendar_edit_item.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
edit_calendar_ajax();
}
$smarty->assign('impossibleDates',$impossibleDates);
$smarty->assign('mid', 'tiki-calendar_edit_item.tpl');
$smarty->display("tiki.tpl");
?>
