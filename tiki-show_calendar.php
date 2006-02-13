<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$calendarViewMode = $_SESSION['CalendarViewMode'];
$calendarViewGroups = $_SESSION['CalendarViewGroups'];
$calendarViewTikiCals = $_SESSION['CalendarViewTikiCals'];
$calendarViewList = $_SESSION['CalendarViewList'];

$z = date("z");

if (($firstDayofWeek = $tikilib->get_user_preference($user, "")) == "") { /* 0 for Sundays, 1 for Mondays */
	$strRef = "First day of week: Sunday (its ID is 0) - translators you need to localize this string!";
//get_strings tra("First day of week: Sunday (its ID is 0) - translators you need to localize this string!");
	if (($str = tra($strRef)) != $strRef) {
		$firstDayofWeek = ereg_replace("[^0-9]", "",$str);
		if ($firstDayofWeek < 0 || $firstDayofWeek > 9)
			$firstDayofWeek = 0;
	} else
		$firstDayofWeek = 0;
}
$smarty->assign('firstDayofWeek', $firstDayofWeek);

$strRef = tra("%H:%M %Z");
if (strstr($strRef, "%h") || strstr($strRef, "%g"))
	$timeFormat12_24 = "12";
else
	$timeFormat12_24 = "24";
$smarty->assign('timeFormat12_24', $timeFormat12_24);

$short_format_day = tra("%m/%d");
$smarty->assign('short_format_day', $short_format_day);

// Windows requires clean dates!
$focus_prevday = mktime(0, 0, 0, $focus_month, $focus_day - 1, $focus_year);
$focus_nextday = mktime(0, 0, 0, $focus_month, $focus_day + 1, $focus_year);
$focus_prevweek = mktime(0, 0, 0, $focus_month, $focus_day - 7, $focus_year);
$focus_nextweek = mktime(0, 0, 0, $focus_month, $focus_day + 7, $focus_year);
$focus_prevmonth = mktime(0, 0, 0, $focus_month - 1, $focus_day, $focus_year);
$focus_nextmonth = mktime(0, 0, 0, $focus_month + 1, $focus_day, $focus_year);

$smarty->assign('daybefore', $focus_prevday);
$smarty->assign('weekbefore', $focus_prevweek);
$smarty->assign('monthbefore', $focus_prevmonth);
$smarty->assign('dayafter', $focus_nextday);
$smarty->assign('weekafter', $focus_nextweek);
$smarty->assign('monthafter', $focus_nextmonth);
$smarty->assign('focusmonth', $focus_month);
$smarty->assign('focusdate', $focusdate);
$smarty->assign('focuscell', $focuscell);
$now = mktime(date('G'), date('i'), date('s'), date('n'), date('d'), date('Y')); /* server date */
$smarty->assign('now', $now); /* server date */
if (!isset($dc->getDisplayDateFromServerDate)) {
    $dc = $tikilib->get_date_converter($user);
}
$smarty->assign('nowUser', $dc->getDisplayDateFromServerDate($now)); /* user time */


$weekdays = range(0, 6);
$hours = range(0, 23);

$d = 60 * 60 * 24;
$currentweek = date("W", $focusdate);
$wd = date('w', $focusdate);

#if ($wd == 0) $w = 7;
#$wd--;
//prepare for select first day of week (Hausi)
   if($firstDayofWeek == 1){
	$wd--;
	if($wd == -1){
		$wd=6;
	}
  }

if (isset($request_day)) $focus_day = $request_day;
if (isset($request_month)) $focus_month = $request_month;
if (isset($request_year)) $focus_year = $request_year;

$smarty->assign('viewmonth', $focus_month);
$smarty->assign('viewday', $focus_day);
$smarty->assign('viewyear', $focus_year);

// calculate timespan for sql query
if ($calendarViewMode == 'month' || $calendarViewMode == 'quarter' || $calendarViewMode == 'semester') {
	$daystart = mktime(0,0,0, $focus_month, 1, $focus_year);
} elseif ($calendarViewMode == 'year') {
	$daystart = mktime(0,0,0, 1, 1, $focus_year);
} else {
	$daystart = mktime(0,0,0,$focus_month, $focus_day, $focus_year);
}
$viewstart = $daystart; // viewstart is the beginning of the display, daystart is the beginning of the selected period
	
if ($calendarViewMode == 'month' ||
	 $calendarViewMode == 'quarter' ||
	 $calendarViewMode == 'semester' ||
	 $calendarViewMode == 'year'	) {
   $TmpWeekday = date("w",$viewstart);
//prepare for select first day of week (Hausi)
   if($firstDayofWeek == 1){
	$TmpWeekday--;
	if($TmpWeekday == -1) {
		$TmpWeekday=6;
	}
   }

   // move viewstart back to Sunday....
   $viewstart -= $TmpWeekday * $d;
   // this is the last day of $focus_month
   if ($calendarViewMode == 'month') {
     $viewend = mktime(0,0,0,$focus_month + 1, 1, $focus_year);
   } elseif ($calendarViewMode == 'quarter') {
     $viewend = mktime(0,0,0,$focus_month + 3, 1, $focus_year);
   } elseif ($calendarViewMode == 'semester') {
     $viewend = mktime(0,0,0,$focus_month + 6, 1, $focus_year);
   } elseif ($calendarViewMode == 'year') {
     $viewend = mktime(0,0,0,1, 1, $focus_year+1);
   } else {
     $viewend = mktime(0,0,0,$focus_month + 1, 0, $focus_year);
   }
   $viewend -= 1;
   $dayend=$viewend;
   $TmpWeekday = date("w", $viewend);
   $viewend += (6 - $TmpWeekday) * $d;
   // ISO weeks --- kinda mangled because ours begin on Sunday...
   $firstweek = date("W", $viewstart + $d);
   $lastweek = date("W", $viewend);
   if ($lastweek <= $firstweek) {
		   $startyear = date("Y",$daystart-1);
		   $weeksinyear = date("W",mktime(0,0,0,12,31,$startyear));
		   if ($weeksinyear == 1){
			$weeksinyear = date("W",mktime(0,0,0,12,28,$startyear));
		   }
		   $lastweek += $weeksinyear;
   }

   $numberofweeks = $lastweek - $firstweek;
} elseif ($calendarViewMode == 'week') {
   $firstweek = $currentweek;
   $lastweek = $currentweek;
   // then back up to the preceding Sunday;
   $viewstart -= $wd * $d;
   $daystart=$viewstart;
   // then go to the end of the week for $viewend
   $viewend = $viewstart + ((7 * $d) - 1);
   $dayend=$viewend;
   $numberofweeks = 0;
} else {
   $firstweek = $currentweek;
   $lastweek = $currentweek;
   $viewend = $viewstart + ($d - 1);
   $dayend = $daystart;
   $weekdays = array(date('w',$focusdate));
   $numberofweeks = 0;
}
// untested (by me, anyway!) function grabbed from the php.net site:
// [2004/01/05:rpg]
function m_weeks($y, $m){
  // monthday array
  $monthdays = array(1=>31, 3=>31, 4=>30, 5=>31, 6=>30,7=>31,
               8=>31, 9=>30, 10=>31, 11=>30, 12=>31);
  // weekdays remaining in a week starting on 7 - Sunday...(could be changed)
  $weekdays = array(7=>7, 1=>6, 2=>5, 3=>4, 4=>3, 5=>2, 6=>1);
  $date = mktime( 0, 0, 0, $m, 1, $y);
  $leap = date("L", $date);
  // if it is a leap year set February to 29 days, otherwise 28
  $monthdays[2] = ($leap ? 29 : 28);
  // get the weekday of the first day of the month
  $wn = strftime("%u",$date);
  $days = $monthdays[$m] - $weekdays[$wn];
  return (ceil($days/7) + 1);
}


$smarty->assign('viewstart', $viewstart);
$smarty->assign('viewend', $viewend);
$smarty->assign('numberofweeks', $numberofweeks);
$smarty->assign('daystart', $daystart);
$smarty->assign('dayend', $dayend);

$daysnames = array();
if ($firstDayofWeek == 0)
	$daysnames[] = tra("Sunday");
array_push($daysnames, 
	tra("Monday"),
	tra("Tuesday"),
	tra("Wednesday"),
	tra("Thursday"),
	tra("Friday"),
	tra("Saturday")
);
if ($firstDayofWeek != 0)
	$daysnames[] = tra("Sunday");

$weeks = array();
$cell = array();

if ($calendarViewGroups) {
	if ($calendarViewList == "list") {
		if (isset($sort_mode)) {
			$smarty->assign_by_ref('sort_mode', $sort_mode);
		} else
			$sort_mode = "start_asc";
		$listevents = $calendarlib->list_raw_items($calendarViewGroups, $user, $viewstart, $viewend, 0, 50, $sort_mode);
		for ($i = count($listevents) - 1; $i >= 0; --$i)
			$listevents[$i]['modifiable'] = in_array($listevents[$i]['calendarId'], $modifiable)? "y": "n";
	} else {
		$listevents = $calendarlib->list_items($calendarViewGroups, $user, $viewstart, $viewend, 0, 50);
	}
	$smarty->assign_by_ref('listevents', $listevents);
} else {
	$listevents = array();
}

if ($calendarViewTikiCals) {
    $listtikievents = $calendarlib->list_tiki_items($calendarViewTikiCals, $user, $viewstart, $viewend, 0, 50, 'name_desc', '');
	$smarty->assign_by_ref('listtikievents', $listtikievents);
} else {
	$listtikievents = array();
}


define("weekInSeconds", 604800);
$mloop = date("m", $viewstart);
$dloop = date("d", $viewstart);
$yloop = date("Y", $viewstart);

// note that number of weeks starts at ZERO (i.e., zero = 1 week to display).
for ($i = 0; $i <= $numberofweeks; $i++) {
	$wee = date("W",$viewstart + ($i * weekInSeconds) + $d);

	$weeks[] = $wee;

   // $startOfWeek is a unix timestamp
   $startOfWeek = $viewstart + $i * weekInSeconds;

	foreach ($weekdays as $w) {
		$leday = array();
		If ($calendarViewMode == 'day') {
			$dday = $daystart;
		} else {
			//$dday = $startOfWeek + $d * $w;
			$dday = mktime(0,0,0, $mloop, $dloop++, $yloop);
		}
		$cell[$i][$w]['day'] = $dday;
		
		If ($calendarViewMode == 'day' or ($dday>=$daystart && $dday<=$dayend)) {
		  $cell[$i][$w]['focus'] = true;
		} else {
		  $cell[$i][$w]['focus'] = false;
		}
		if (isset($listevents["$dday"])) {
			$e = 0;

			foreach ($listevents["$dday"] as $le) {
				$le['modifiable'] = in_array($le['calendarId'], $modifiable)? "y": "n";
				$leday["{$le['time']}$e"] = $le;

				$smarty->assign_by_ref('cellhead', $le["head"]);
				$smarty->assign_by_ref('cellprio', $le["prio"]);
				$smarty->assign_by_ref('cellcalname', $le["calname"]);
				$smarty->assign_by_ref('celllocation', $le["location"]);
				$smarty->assign_by_ref('cellcategory', $le["category"]);
				$smarty->assign_by_ref('cellname', $le["name"]);
				$smarty->assign_by_ref('cellurl', $le["url"]);
				$smarty->assign_by_ref('cellid', $le["calitemId"]);
				$smarty->assign('celldescription', $tikilib->parse_data($le["description"]));
				$smarty->assign_by_ref('cellmodif', $le['modifiable']);
				$leday["{$le['time']}$e"]["over"] = $smarty->fetch("tiki-calendar_box.tpl");
				$e++;
			}
		}

		if (isset($listtikievents["$dday"])) {
			$e = 0;

			foreach ($listtikievents["$dday"] as $lte) {
				$leday["{$lte['time']}$e"] = $lte;

				$smarty->assign_by_ref('cellhead', $lte["head"]);
				$smarty->assign_by_ref('cellprio', $lte["prio"]);
				$smarty->assign_by_ref('cellcalname', $lte["calname"]);
				$smarty->assign('celllocation', "");
				$smarty->assign('cellcategory', "");
				$smarty->assign_by_ref('cellname', $lte["name"]);
				$smarty->assign('cellid', "");
				$smarty->assign_by_ref('celldescription', $lte["description"]);
				$leday["{$lte['time']}$e"]["over"] = $smarty->fetch("tiki-calendar_box.tpl");
				$e++;
			}
		}

		if (is_array($leday)) {
			ksort ($leday);
			$cell[$i][$w]['items'] = array_values($leday);
		}
	}
}

$hrows = array();
$hours = array();
if ($calendarViewMode == 'day') {
	$hours = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
	foreach ($cell[0]["{$weekdays[0]}"]['items'] as $dayitems) {
		$rawhour = intval(substr($dayitems['time'],0,2));
		$dayitems['mins'] = substr($dayitems['time'],2);
		$hrows["$rawhour"][] = $dayitems;
	}
}
$smarty->assign('hrows', $hrows); 
$smarty->assign('hours', $hours); 
$smarty->assign('mrows', array(0=>"00", 5=>"05", 10=>"10", 15=>"15", 20=>"20", 25=>"25", 30=>"30", 35=>"35", 40=>"40", 45=>"45", 50=>"50", 55=>"55"));

$smarty->assign('trunc', $trunc); 
$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M"); 
$smarty->assign('daformat2', $tikilib->get_long_date_format()); 
$smarty->assign('currentweek', $currentweek);
$smarty->assign('firstweek', $firstweek);
$smarty->assign('lastweek', $lastweek);
$smarty->assign('weekdays', $weekdays);
$smarty->assign('weeks', $weeks);
$smarty->assign('daysnames', $daysnames);
$smarty->assign('cell', $cell);
$smarty->assign('var', '');

?>