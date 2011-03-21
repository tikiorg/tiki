<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_events_info() {
	return array(
		'name' => tra('Events'),
		'documentation' => 'PluginEvents',
		'description' => tra('Display upcoming events from calendars'),
		'prefs' => array( 'feature_calendar', 'wikiplugin_events' ),
		'icon' => 'pics/icons/calendar_view_day.png',
		'params' => array(
			'calendarid' => array(
				'required' => true,
				'name' => tra('Calendar IDs'),
				'description' => tra('ID numbers for the site calendars whose events are to be displayed, separated by vertical bars (|)'),
				'default' => '',
			),
			'maxdays' => array(
				'required' => false,
				'name' => tra('Maximum Days'),
				'description' => tra('Events occurring within this number of days in the future from today will be included in the list (unless limited by other parameter settings). Default is 365 days.'),
				'filter' => 'digits',
				'default' => 365,
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Events'),
				'description' => tra('Maximum number of events to display. Default is 10. Set to 0 to display all (unless limited by other parameter settings)'),
				'default' => 10,
				'filter' => 'digits',
			),
			'datetime' => array(
				'required' => false,
				'name' => tra('Show Time'),
				'description' => tra('Show the time along with the date (shown by default)'),
				'default' => 1,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => tra('Show the description of the event (shown by default)'),
				'default' => 1,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				),
			),
		),
	);
}

function wikiplugin_events($data,$params) {
	global $calendarlib;
	global $userlib;
	global $tikilib;
	global $tiki_p_admin;
	global $tiki_p_view_calendar, $smarty;

	if (!isset($calendarlib)) {
		include_once ('lib/calendar/calendarlib.php');
	}

	extract($params,EXTR_SKIP);

	if (!isset($maxdays)) {$maxdays=365;}
	if (!isset($max)) { $max=10; }
	if (!isset($datetime)) { $datetime=1; }
	if (!isset($desc)) { $desc=1; }
	

	$rawcals = $calendarlib->list_calendars();
	$calIds = array();
	$viewable = array();

	foreach ($rawcals["data"] as $cal_id=>$cal_data) {
		$calIds[] = $cal_id;
		if ($tiki_p_admin == 'y') {
			$canView = 'y';
		} elseif ($cal_data["personal"] == "y") {
			if ($user) {
				$canView = 'y';
			} else {
				$canView = 'n';
			}
		} else {
			if ($userlib->object_has_one_permission($cal_id,'calendar')) {
				if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
					$canView = 'y';
				} else {
					$canView = 'n';
				}		
				if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_admin_calendar')) {
					$canView = 'y';
				}
			} else {
				$canView = $tiki_p_view_calendar;
			}
		}
		if ($canView == 'y') {
			$viewable[] = $cal_id;
		}
	}

	if (isset($calendarid)) {
		$calIds=explode("|",$calendarid);
	}
	$events = $calendarlib->upcoming_events($max,
		array_intersect($calIds, $viewable),
		$maxdays);
 
	$smarty->assign_by_ref('datetime', $datetime);
	$smarty->assign_by_ref('desc', $desc);
	$smarty->assign_by_ref('events', $events);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_events.tpl').'~/np~';

	$repl="";		
	if (count($events)<$max) $max = count($events);

	$repl .= '<table class="normal">';
	$repl .= '<tr class="heading"><td colspan="2">'.tra("Upcoming Events").'</td></tr>';
	for ($j = 0; $j < $max; $j++) {
	  if ($datetime!=1) {
			$eventStart=str_replace(" ","&nbsp;",strftime($tikilib->get_short_date_format(),$events[$j]["start"]));
			$eventEnd=str_replace(" ","&nbsp;",strftime($tikilib->get_short_date_format(),$events[$j]["end"]));	  
	  } else {
			$eventStart=str_replace(" ","&nbsp;",strftime($tikilib->get_short_datetime_format(),$events[$j]["start"]));
			$eventEnd=str_replace(" ","&nbsp;",strftime($tikilib->get_short_datetime_format(),$events[$j]["end"]));
		}
		if ($j%2) {
			$style="odd";
		} else {
			$style="even";
		}
		$repl .= '<tr class="'.$style.'"><td width="5%">~np~'.$eventStart.'<br/>'.$eventEnd.'~/np~</td>';
		$repl .= '<td><a class="linkmodule" href="tiki-calendar.php?editmode=details&calitemId='.$events[$j]["calitemId"].'"><b>'.$events[$j]["name"].'</b></a>';
		if ($desc==1) {
			$repl .= '<br/>'.nl2br($events[$j]["description"]);
		}
		$repl .='</td></tr>';
	}
	$repl .= '</table>';
	return $repl;
}
