<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include 'tiki-setup.php';
include 'lib/calendar/tikicalendarlib.php';
$access->check_feature('feature_action_calendar');
$access->check_permission('tiki_p_view_tiki_calendar');

$headerlib->add_cssfile('css/calendar.css',20);

$myurl = 'tiki-action_calendar.php';
include_once("tiki-calendar_setup.php");

$tikiItems = $tikicalendarlib->getTikiItems(true);
$smarty->assign('tikiItems', $tikiItems);

// Register selected tikiItems in session vars if a refresh is requested
//   If no refresh is requested, either keep existing session values if they exists, either view all tikiItems by default
//   If a refresh has been requested without tikicals, view no tikiItem
if ( empty($_REQUEST['refresh']) ) {
	if ( ! array_key_exists('CalendarViewTikiCals', $_SESSION) ) {
		$_SESSION['CalendarViewTikiCals'] = array_keys($tikiItems);
	}
} elseif ( !empty($_REQUEST['tikicals']) and is_array($_REQUEST['tikicals']) ) {
	$_SESSION['CalendarViewTikiCals'] = $_REQUEST['tikicals'];
} else {
	unset($_SESSION['CalendarViewTikiCals']);
}
$smarty->assign('tikicals', $_SESSION['CalendarViewTikiCals']);

$tc_infos = $tikicalendarlib->getCalendar($_SESSION['CalendarViewTikiCals'], $viewstart, $viewend);
foreach ( $tc_infos as $tc_key => $tc_val ) {
	$smarty->assign($tc_key, $tc_val);
}

$hrows = array();
$hours = array();
if ($calendarViewMode['casedefault'] == 'day') {
	$hours = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
	foreach ($tc_infos['cell'][0]["{$tc_infos['weekdays'][0]}"]['items'] as $dayitems) {
		$rawhour = intval(substr($dayitems['time'],0,2));
		$dayitems['mins'] = substr($dayitems['time'],2);
		$hrows["$rawhour"][] = $dayitems;
	}
} else {
	$smarty->assign('currMonth', $focusdate);
}

$smarty->assign('hrows', $hrows);
$smarty->assign('hours', $hours);

$smarty->assign('var', '');
$smarty->assign('daformat2', $tikilib->get_long_date_format());
$smarty->assign('myurl', $myurl);
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('calendarViewMode',$calendarViewMode['casedefault']);
$smarty->assign('calendar_type', 'tiki_actions');

$smarty->assign('mid', 'tiki-action_calendar.tpl');
$smarty->display("tiki.tpl");
