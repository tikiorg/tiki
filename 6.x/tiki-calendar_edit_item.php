<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'calendar';
require_once ('tiki-setup.php');

$access->check_feature('feature_calendar');

include_once ('lib/calendar/calendarlib.php');
include_once ('lib/newsletters/nllib.php');
include_once ('lib/calendar/calrecurrence.php');
if ($prefs['feature_groupalert'] == 'y') {
	include_once ('lib/groupalert/groupalertlib.php');
}
if ($prefs['ajax_xajax'] == "y") {
	require_once ('lib/ajax/ajaxlib.php');
}

$auto_query_args = array('calitemId');

$daysnames = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Satursday");
$daysnames_abr = array("Su","Mo","Tu","We","Th","Fr","Sa");
$monthnames = array("","January","February","March","April","May","June","July","August","September","October","November","December");
$smarty->assign('daysnames',$daysnames);
$smarty->assign('daysnames_abr',$daysnames_abr);
$smarty->assign('monthnames',$monthnames);

$smarty->assign('edit',false);
$smarty->assign('recurrent', '');
$hours_minmax = '';

$caladd = array();
$rawcals = $calendarlib->list_calendars();
if ($rawcals['cant'] == 0 && $tiki_p_admin_calendar == 'y') {
	$smarty->assign('msg', tra('You need to <a href="tiki-admin_calendars.php">create a calendar</a>'));
	$smarty->display("error.tpl");
	die;
}

$rawcals['data'] = Perms::filter( array( 'type' => 'calendar' ), 'object', $rawcals['data'], array( 'object' => 'calendarId' ), 'view_calendar' );

foreach ($rawcals["data"] as $cal_data) {
  $cal_id = $cal_data['calendarId'];
  $calperms = Perms::get( array( 'type' => 'calendar', 'object' => $cal_id ) );
  if ($cal_data["personal"] == "y") {
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
      $cal_data["tiki_p_view_calendar"] = $calperms->view_calendar ? "y" : "n";
      $cal_data["tiki_p_view_events"] = $calperms->view_events ? "y" : "n";
      $cal_data["tiki_p_add_events"] = $calperms->add_events ? "y" : "n";
      $cal_data["tiki_p_change_events"] = $calperms->change_events ? "y" : "n";
  }
	$caladd["$cal_id"] = $cal_data;
	if ($cal_data['tiki_p_add_events'] == 'y' && empty($calID)) {
		$calID = $cal_id;
	}
}
$smarty->assign('listcals',$caladd);

if ( ! isset($_REQUEST["calendarId"]) ) {
	if (isset($_REQUEST['calitemId'])) {
		$calID = $calendarlib->get_calendarid($_REQUEST['calitemId']);
	} elseif (isset($_REQUEST['viewcalitemId'])) {
		$calID = $calendarlib->get_calendarid($_REQUEST['viewcalitemId']);
	}
} elseif (isset($_REQUEST['calendarId'])) {
	$calID = $_REQUEST['calendarId'];
} elseif (isset($_REQUEST['save']) && isset($_REQUEST['save']['calendarId'])) {
	$calID = $_REQUEST['save']['calendarId'];
}

if ($prefs['feature_groupalert'] == 'y' && !empty($calID) ) {
	$groupforalert=$groupalertlib->GetGroup ('calendar',$calID);
	$showeachuser = '';
	if ( $groupforalert != '' ) {
		$showeachuser=$groupalertlib->GetShowEachUser('calendar',$calID, $groupforalert) ;
		$listusertoalert=$userlib->get_users(0,-1,'login_asc','','',false,$groupforalert,'') ;
		$smarty->assign_by_ref('listusertoalert',$listusertoalert['data']);
	}
	$smarty->assign_by_ref('groupforalert',$groupforalert);
	$smarty->assign_by_ref('showeachuser',$showeachuser);
}

$tikilib->get_perm_object( $calID, 'calendar' );
$calendar = $calendarlib->get_calendar($calID);
if ($calendar['personal'] == 'y') {
	$ownCal = ($user && $user == $calendar["user"]) ? 'y' : 'n';
	$tiki_p_view_calendar = $ownCal;
	$tiki_p_view_events = $ownCal;
	$tiki_p_add_events = $ownCal;
	$tiki_p_change_events = $ownCal;
}

$access->check_permission('tiki_p_view_calendar');

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

    $save['allday'] = (isset($_REQUEST['allday']) && $_REQUEST['allday'] == 'true') ? 1 : 0;
	if ($_REQUEST['allday'] == 'true') {
		$save['start'] = TikiLib::make_time(
			0,
			0,
			0,
			$_REQUEST['start_date_Month'],
			$_REQUEST['start_date_Day'],
			$_REQUEST['start_date_Year']
		);

		if ($save['end_or_duration'] == 'duration') {
			$save['duration'] = 86399;
			$save['end'] = $save['start'] + $save['duration'];
		} else {
			$save['end'] = TikiLib::make_time(
				23,
				59,
				59,
				$_REQUEST['end_date_Month'],
				$_REQUEST['end_date_Day'],
				$_REQUEST['end_date_Year']
			);
			$save['duration'] = max(0, $save['end'] - $save['start']);
		}
	} else {
		if (!empty($_REQUEST['start_Meridian']) && $_REQUEST['start_Meridian'] == 'pm') {
			$_REQUEST['start_Hour'] += 12;
		}
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
			if (!empty($_REQUEST['end_Meridian']) && $_REQUEST['end_Meridian'] == 'pm') {
				$_REQUEST['end_Hour'] += 12;
			}
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
}

$impossibleDates = false;
if (isset($save['start']) && isset($save['end'])) {
	if (($save['end'] - $save['start']) < 0)
		$impossibleDates = true;
}

if (isset($_POST['act'])) {
	// Check antibot code if anonymous and allowed
	if (empty($user) && $prefs['feature_antibot'] == 'y' && (!$captchalib->validate())) {
		$smarty->assign('msg', $captchalib->getErrors());
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->display("error.tpl");
		die;
	}
	if (empty($save['user'])) $save['user'] = $user;
	$newcalid = $save['calendarId'];
	if ((empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_add_events'] == 'y')
	or (!empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_change_events'] == 'y')) {
		if (empty($save['name'])) $save['name'] = tra("event without name");
		if (empty($save['priority'])) $save['priority'] = 1;
		if (empty($save['status'])) {
			if (empty($calendar['defaulteventstatus'])) {
				$save['status'] = 1; // Confirmed
			} else {
				$save['status'] = $calendar['defaulteventstatus'];
			}
		}
		
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
					// Save the ip at the log for the addition of new calendar items when done by anonymous users
					if (empty($user) && empty($save['calitemId']) && $caladd["$newcalid"]['tiki_p_add_events']) { 
						$logslib->add_log('calendar','Recurrent calendar item starting on '.$_POST['startPeriod'].' added to calendar '.$save['calendarId']);
					}
					if (empty($user) && !empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_change_events']) { 
						$logslib->add_log('calendar','Recurrent calendar item starting on '.$_POST['startPeriod'].' changed in calendar '.$save['calendarId']);
					}
				header('Location: tiki-calendar.php?todate='.$save['start']);
				die;
			}
		} else {
			if (!$impossibleDates) {
				if (array_key_exists('recurrenceId',$_POST)) {
					$save['recurrenceId'] = $_POST['recurrenceId'];
					$save['changed'] = true;
				}
				$calitemId = $calendarlib->set_item($user,$save['calitemId'],$save);
					// Save the ip at the log for the addition of new calendar items when done by anonymous users
					if (empty($user) && empty($save['calitemId']) && $caladd["$newcalid"]['tiki_p_add_events']) { 
						$logslib->add_log('calendar','Calendar item '.$calitemId.' added to calendar '.$save['calendarId']);
					}
					if (empty($user) && !empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_change_events']) { 
						$logslib->add_log('calendar','Calendar item '.$calitemId.' changed in calendar '.$save['calendarId']);
					}
            if ($prefs['feature_groupalert'] == 'y') {
              $groupalertlib->Notify($_REQUEST['listtoalert'],"tiki-calendar_edit_item.php?viewcalitemId=".$calitemId);
            }
            header('Location: tiki-calendar.php?todate='.$save['start']);
            die;
			}
		}
	}
}

if (!empty($_REQUEST['viewcalitemId']) && isset($_REQUEST['del_me']) && $tiki_p_calendar_add_my_particip == 'y') {
	$calendarlib->update_participants($_REQUEST['viewcalitemId'], null, array($user));
}

if (!empty($_REQUEST['viewcalitemId']) && isset($_REQUEST['add_me']) && $tiki_p_calendar_add_my_particip == 'y') {
	$calendarlib->update_participants($_REQUEST['viewcalitemId'], array(array('name'=>$user)), null);
}

if (!empty($_REQUEST['viewcalitemId']) && !empty($_REQUEST['guests']) && isset($_REQUEST['add_guest']) && $tiki_p_calendar_add_guest_particip == 'y') {
	$guests = preg_split('/ *, */', $_REQUEST['guests']);
	foreach ($guests as $i=>$guest) {
		$guests[$i] = array('name'=>$guest);
	}
	$calendarlib->update_participants($_REQUEST['viewcalitemId'], $guests);
}

if (isset($_REQUEST["delete"]) and ($_REQUEST["delete"]) and isset($_REQUEST["calitemId"]) and $tiki_p_change_events == 'y') {
	// There is no check for valid antibot code if anonymous allowed to delete events since this comes from a JS button at the tpl and bots are not know to use JS
	$access->check_authenticity();
	$calitem = $calendarlib->get_item($_REQUEST['calitemId']);
	$calendarlib->drop_item($user, $_REQUEST["calitemId"]);
	if (empty($user)) { 
		$logslib->add_log('calendar','Calendar item '.$_REQUEST['calitemId'].' deleted');
	}
	$_REQUEST["calitemId"] = 0;
	header('Location: tiki-calendar.php?todate='.$calitem['start']);
	exit;
} elseif (isset($_REQUEST["delete"]) and ($_REQUEST["delete"]) and isset($_REQUEST["recurrenceId"]) and $tiki_p_change_events == 'y') {
	// There is no check for valid antibot code if anonymous allowed to delete events since this comes from a JS button at the tpl and bots are not know to use JS
	$calRec = new CalRecurrence($_REQUEST['recurrenceId']);
	$calRec->delete();
	if (empty($user)) { 
		$logslib->add_log('calendar','Recurrent calendar items (recurrenceId = '.$_REQUEST["recurrenceId"].') deleted');
	}
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
	if (empty($user)) { 
		$logslib->add_log('calendar','Calendar item/s '.$_REQUEST['calitemId'].' droped');
	}
	header('Location: tiki-calendar.php');
	die;
}  elseif (isset($_REQUEST['duplicate']) and $tiki_p_add_events == 'y') {
	// Check antibot code if anonymous and allowed
	if (empty($user) && $prefs['feature_antibot'] == 'y' && (!$captchalib->validate())) {
		$smarty->assign('msg', $captchalib->getErrors());
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->display("error.tpl");
		die;
	}
	$calitem = $calendarlib->get_item($_REQUEST['calitemId']);
	$calitem['calendarId'] = $calID;
	$calitem['calitemId'] = 0;
	$calendarlib->set_item($user,0,$calitem);
	$id = 0;
	if (isset($_REQUEST['calId'])) {
		$calendar = $calendarlib->get_calendar($_REQUEST['calId']);
  } else {
		$calendar = $calendarlib->get_calendar($calitem['calendarId']);
  }
	$smarty->assign('edit',true);
	$hour_minmax = ceil(($calendar['startday']-1)/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} elseif (isset($_REQUEST['preview']) || $impossibleDates) {
	$save['parsed'] = $tikilib->parse_data($save['description']);
	$save['parsedName'] = $tikilib->parse_data($save['name']);
	$id = $save['calitemId'];
	$calitem = $save;

	$recurrence = array(
		'weekly' => isset($_POST['recurrenceType']) && $_POST['recurrenceType'] = 'weekly',
		'weekday' => isset($_POST['weekday']) ? $_POST['weekday'] : '',
		'monthly' => isset($_POST['recurrenceType']) && $_POST['recurrenceType'] = 'monthly',
		'dayOfMonth' => isset($_POST['dayOfMonth']) ? $_POST['dayOfMonth'] : '',
		'yearly' => isset($_POST['recurrenceType']) && $_POST['recurrenceType'] = 'yearly',
		'dateOfYear_day' => isset($_POST['dateOfYear_day']) ? $_POST['dateOfYear_day'] : '',
		'dateOfYear_month' => isset($_POST['dateOfYear_month']) ? $_POST['dateOfYear_month'] : '',
		'startPeriod' => isset($_POST['startPeriod']) ? $_POST['startPeriod'] : '',
		'nbRecurrences' => isset($_POST['nbRecurrences']) ? $_POST['nbRecurrences'] : '',
		'endPeriod' => isset($_POST['endPeriod']) ? $_POST['endPeriod'] : ''
	);	
	if ( isset($_POST['recurrent']) && $_POST['recurrent'] == 1 ) {
		$smarty->assign('recurrent', $_POST['recurrent']);
	}
	$smarty->assign_by_ref('recurrence', $recurrence);
	
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$smarty->assign('edit',true);
	$smarty->assign('preview', isset($_REQUEST['preview']));
} elseif (isset($_REQUEST['changeCal'])) {
	$calitem = $save;
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$smarty->assign('edit',true);
	$id = isset($save['calitemId'])?$save['calitemId']: 0;
	$hour_minmax = ceil(($calendar['startday'])/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
	$smarty->assign('changeCal', isset($_REQUEST['changeCal']));
} elseif (isset($_REQUEST['viewcalitemId']) and $tiki_p_view_events == 'y') {
	$calitem = $calendarlib->get_item($_REQUEST['viewcalitemId']);
	$id = $_REQUEST['viewcalitemId'];
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$hour_minmax = ceil(($calendar['startday'])/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} elseif (isset($_REQUEST['calitemId']) and ($tiki_p_change_events == 'y' or $tiki_p_view_events == 'y')) {
	$calitem = $calendarlib->get_item($_REQUEST['calitemId']);
	$id = $_REQUEST['calitemId'];
	$calendar = $calendarlib->get_calendar($calitem['calendarId']);
	$smarty->assign('edit',true);
	$hour_minmax = ceil(($calendar['startday'])/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} elseif (isset($calID) and $tiki_p_add_events == 'y') {
	if (isset($_REQUEST['todate'])) {
		$now = $_REQUEST['todate'];
	} else {
		$now = $tikilib->now;
	}
	$calendar = $calendarlib->get_calendar($calID);
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
		'duration'=>(60*60),
		'recurrenceId'=>0,
		);
	$id = 0;
	$smarty->assign('edit',true);
	$hour_minmax = ceil(($calendar['startday']-1)/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} else {
  $smarty->assign('errortype', 401);
  $smarty->assign('msg', tra("You do not have permission to view this page"));
  $smarty->display("error.tpl");
  die;
}

if (!empty($calendar['eventstatus'])) {
    $calitem['status'] = $calendar['eventstatus'];
}

if ($calendar['customlocations'] == 'y') {
	$listlocs = $calendarlib->list_locations($calID);
} else {
	$listlocs = array();
}
$smarty->assign('listlocs', $listlocs);

if ($calendar['customcategories'] == 'y') {
	$listcats = $calendarlib->list_categories($calID);
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
  $cat_objid = $calID;
  include('tiki-tc.php');
}

$headerlib->add_cssfile('css/calendar.css',20);

$smarty->assign('referer', empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'tiki-calendar_edit_item.php') !== false ? 'tiki-calendar.php' : $_SERVER['HTTP_REFERER']);
$smarty->assign('myurl', 'tiki-calendar_edit_item.php');
$smarty->assign('id', $id);
$smarty->assign('hour_minmax', $hour_minmax);
if ($calitem['recurrenceId'] > 0) {
	$cr = new CalRecurrence($calitem['recurrenceId']);
	$smarty->assign('recurrence',$cr->toArray());
}
$headerlib->add_js('
function checkDateOfYear(day,month) {
	var mName = new Array("-","'.tra('January').'","'.tra('February').'","'.tra('March').'","'.tra('April').'","'.tra('May').'","'.tra('June').'","'.tra('July').'","'.tra('August').'","'.tra('September').'","'.tra('October').'","'.tra('November').'","'.tra('December').'}");
	var error = false;
	if (month == 4 || month == 6 || month == 9 || month == 11)
		if (day == 31)
			error = true;
	if (month == 2)
		if (day > 29)
			error = true;
	if (error) {
		document.getElementById("errorDateOfYear").innerHTML = "<em>'.tra('There\'s no such date as').' " + day + " '.tra('of').' " + mName[month] + "</em>";
	} else {
		document.getElementById("errorDateOfYear").innerHTML = "";
	}
}
');
$smarty->assign('calitem', $calitem);
$smarty->assign('calendar', $calendar);
$smarty->assign('calendarId', $calID);
if (array_key_exists('CalendarViewGroups',$_SESSION) && count($_SESSION['CalendarViewGroups']) == 1)
	$smarty->assign('calendarView',$_SESSION['CalendarViewGroups'][0]);
if ($prefs['ajax_xajax'] == "y") {
function edit_calendar_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-calendar_edit_item.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
edit_calendar_ajax();
}

global $wikilib; include_once('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true, 'editwiki');
$smarty->assign_by_ref('plugins', $plugins);
$smarty->assign('headtitle',tra('Calendar event : ').$calitem['name']);
$smarty->assign('impossibleDates',$impossibleDates);
$smarty->assign('mid', 'tiki-calendar_edit_item.tpl');
$smarty->display("tiki.tpl");
