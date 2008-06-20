<?php
// Includes rss feed output in a wiki page
// Usage:
// {RSS(id=>feedId,max=>3,date=>1,author=>1,desc=>1)}{RSS}
//

function wikiplugin_events_help() {
	return tra("~np~{~/np~EVENTS(calendarid=>1,maxdays=>365,max=>-1,datetime=>0|1,desc=>0|1)}{EVENTS} Insert rss feed output into a wikipage");
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
	if (isset($calendarid)) { $calendarids=explode("|",$calendarid); }
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


	$events = $calendarlib->upcoming_events($max,
    array_intersect(isset($calendarid) ? $calendarids : $calIds, $viewable),
    $maxdays);
 
	$smarty->assign_by_ref('datetime', $datetime);
	$smarty->assign_by_ref('desc', $desc);
	$smarty->assign_by_ref('events', $events);
	return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_events.tpl').'~/np~';

	$repl="";		
	if (count($events)<$max) $max = count($events);

	$repl .= '<table class="normal">';
	$repl .= '<tr class="heading"><td colspan="2">'.tra("Upcoming events").'</td></tr>';
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

?>
