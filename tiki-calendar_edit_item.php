<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-calendar_edit_item.php,v 1.21.2.2 2007-11-29 14:31:07 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$section = 'calendar';
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');
include_once ('lib/newsletters/nllib.php');

if ($prefs['feature_calendar'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
  $smarty->display("error.tpl");
  die;
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
    if (isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot view this page"));
      $smarty->display("error.tpl");
      die;
    }
  } 
}

if (isset($_POST['act']) || isset($_POST['preview'])) {
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

if (isset($_POST['act'])) {
	if (empty($save['user'])) $save['user'] = $user;
	$newcalid = $save['calendarId'];

	if ((empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_add_events']) 
	or (!empty($save['calitemId']) and $caladd["$newcalid"]['tiki_p_change_events'])) {
		if (empty($save['name'])) $save['name'] = tra("event without name");

		$calendarlib->set_item($user,$save['calitemId'],$save);
		header('Location: tiki-calendar.php');
		die;
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
	$calitem = $calendarlib->get_item($_REQUEST['duplicate']);
	$id = 0;
	if (isset($_REQUEST['calId'])) {
		$calendar = $calendarlib->get_calendar($_REQUEST['calId']);
  } else {
		$calendar = $calendarlib->get_calendar($calitem['calendarId']);
  }
	$smarty->assign('edit',true);
	$hour_minmax = floor(($calendar['startday']-1)/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} elseif (isset($_REQUEST['preview'])) {
	$save['parsed'] = $tikilib->parse_data($save['description']);
	$save['parsedName'] = $tikilib->parse_data($save['name']);
	$id = $save['calitemId'];
	$calitem = $save;
	$smarty->assign('edit',true);
	$smarty->assign('preview', 'y');
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
	$calitem = array(
		'calitemId'=>0,
		'user'=>$user,
		'name'=>'',
		'url'=>'',
		'description'=>'',
		'status'=>0,
		'priority'=>0,
		'locationId'=>0,
		'categoryId'=>0,
		'nlId'=>0,
		'start'=>$now,
		'end'=>$now+(60*60),
		'duration'=>(60*60) 
		);
	$id = 0;
	$calendar = $calendarlib->get_calendar($_REQUEST['calendarId']);
	$smarty->assign('edit',true);
	$hour_minmax = floor(($calendar['startday']-1)/(60*60)).'-'. ceil(($calendar['endday'])/(60*60));
} else {
  $smarty->assign('msg', tra("Permission denied you can not view this page"));
  $smarty->display("error.tpl");
  die;
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
$smarty->assign('calitem', $calitem);
$smarty->assign('calendar', $calendar);
$smarty->assign('calendarId', $_REQUEST['calendarId']);
$smarty->assign('mid', 'tiki-calendar_edit_item.tpl');
$smarty->display("tiki.tpl");
?>
