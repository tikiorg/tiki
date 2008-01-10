<?php

include 'tiki-setup.php';
include 'lib/calendar/tikicalendarlib.php';

if ($prefs['feature_action_calendar'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_action_calendar");
  $smarty->display("error.tpl");
  die;
}

if ($tiki_p_view_tiki_calendar != 'y') {
  $smarty->assign('msg', tra("Permission denied you cannot view the Tiki calendar"));
	$smarty->display("error.tpl");
	die;
}

$headerlib->add_cssfile('css/calendar.css',20);

$tikiItems = array(
  "wiki" => array( "label" => tra("Wiki"), "feature" => ''.$prefs['feature_wiki'], "right" => "$tiki_p_view"),
  "gal" => array( "label" => tra("Image Gallery"), "feature" => ''.$prefs['feature_galleries'], "right" => "$tiki_p_view_image_gallery"),
  "art" => array( "label" => tra("Articles"), "feature" => ''.$prefs['feature_articles'], "right" => "$tiki_p_read_article"),
  "blog" => array( "label" => tra("Blogs"), "feature" => ''.$prefs['feature_blogs'], "right" => "$tiki_p_read_blog"),
  "forum" => array( "label" => tra("Forums"), "feature" => ''.$prefs['feature_forums'], "right" => "$tiki_p_forum_read"),
  "dir" => array( "label" => tra("Directory"), "feature" => ''.$prefs['feature_directory'], "right" => "$tiki_p_view_directory"),
  "fgal" => array( "label" => tra("File Gallery"), "feature" => ''.$prefs['feature_file_galleries'], "right" => "$tiki_p_view_file_gallery"),
  "faq" => array( "label" => tra("FAQs"), "feature" => ''.$prefs['feature_faqs'], "right" => $tiki_p_view_faqs),
  "quiz" => array( "label" => tra("Quizzes"), "feature" => ''.$prefs['feature_quizzes'], "right" => $tiki_p_take_quiz),
  "track" => array( "label" => tra("Trackers"), "feature" => ''.$prefs['feature_trackers'], "right" => "$tiki_p_view_trackers"),
  "surv" => array( "label" => tra("Survey"), "feature" => ''.$prefs['feature_surveys'], "right" => "$tiki_p_take_survey"),
  "nl" => array( "label" => tra("Newsletter"), "feature" => ''.$prefs['feature_newsletters'], "right" => "$tiki_p_subscribe_newsletters"),
  "chart" => array( "label" => tra("Charts"), "feature" => ''.$prefs['feature_charts'], "right" => "$tiki_p_view_chart")
);

// Register selected tikiItems in session vars if a refresh is requested
//   If no refresh is requested, either keep existing session values if they exists, either view all tikiItems by default
//   If a refresh has been requested without tikicals, view no tikiItem
if ( empty($_REQUEST['refresh']) ) {
	if ( ! array_key_exists('CalendarViewTikiCals', $_SESSION) )
		$_SESSION['CalendarViewTikiCals'] = array_keys($tikiItems);
} elseif ( !empty($_REQUEST["tikicals"]) and is_array($_REQUEST["tikicals"]) ) {
	$_SESSION['CalendarViewTikiCals'] = $_REQUEST["tikicals"];
} else {
	unset($_SESSION['CalendarViewTikiCals']);
}

$smarty->assign('tikiItems', $tikiItems);

include_once("tiki-calendar_setup.php");

$listtikievents = $tikicalendarlib->list_tiki_items($_SESSION['CalendarViewTikiCals'] , $user, $viewstart, $viewend, 0, 50, 'name_desc', '');
$smarty->assign_by_ref('listtikievents', $listtikievents);

define("weekInSeconds", 604800);
$mloop = TikiLib::date_format("%m", $viewstart);
$dloop = TikiLib::date_format("%d", $viewstart);
$yloop = TikiLib::date_format("%Y", $viewstart);

// note that number of weeks starts at ZERO (i.e., zero = 1 week to display).
for ($i = 0; $i <= $numberofweeks; $i++) {
  $wee = TikiLib::date_format("%U",$viewstart + ($i * weekInSeconds) + $d);

  $weeks[] = $wee;

   // $startOfWeek is a unix timestamp
   $startOfWeek = $viewstart + $i * weekInSeconds;

  foreach ($weekdays as $w) {
    $leday = array();
    If ($calendarViewMode == 'day') {
      $dday = $daystart;
    } else {
      //$dday = $startOfWeek + $d * $w;
      $dday = TikiLib::make_time(0,0,0, $mloop, $dloop++, $yloop);
    }
    $cell[$i][$w]['day'] = $dday;

    If ($calendarViewMode == 'day' or ($dday>=$daystart && $dday<=$dayend)) {
      $cell[$i][$w]['focus'] = true;
    } else {
      $cell[$i][$w]['focus'] = false;
    }
    if (isset($listtikievents["$dday"])) {
      $e = -1;


      foreach ($listtikievents["$dday"] as $lte) {
      	$lte['desc_name'] = $lte['name'];
      	if ( $calendarGroupByItem != 'n' ) {
		$key = $lte['id'].'|'.$lte['type'];
        	if ( ! isset($leday[$key]) ) {
			$leday[$key] = $lte;
			$leday[$key]['description'] = ' - <b>'.$lte['when'].'</b> : '.tra($lte['action']).' '.$lte['description'];
			$leday[$key]['head'] = $lte['name'].', <i>'.tra('in').' '.$lte['where'].'</i>';
			$leday[$key]['desc_name'] = '';
		} else {
			$leday_item =& $leday[$key];
			$leday_item['user'] .= ', '.$lte['user'];

			if ( ! is_integer($leday_item['action']) ) $leday_item['action'] = 1;
			$leday_item['action']++;

			$leday_item['name'] = $lte['name'].' (x<b>'.$leday_item['action'].'</b>)';
			$leday_item['desc_name'] = $leday_item['action'].' '.tra('Actions');

			if ( $lte['show_description'] == 'y' && ! empty($lte['description']) ) {
				$leday_item['description'] .= ",\n<br /> - <b>".$lte['when'].'</b> : '.tra($lte['action']).' '.$lte['description'];
				$leday_item['show_description'] = 'y';
			}

		}
	} else {
		$e++;
		$key = "{$lte['time']}$e";
        	$leday[$key] = $lte;
		$lte['desc_name'] .= tra($lte['action']);
	}
      }

      foreach ( $leday as $key => $lte ) {
        $smarty->assign_by_ref('cellhead', $lte["head"]);
        $smarty->assign_by_ref('cellprio', $lte["prio"]);
        $smarty->assign_by_ref('cellcalname', $lte["calname"]);
        $smarty->assign('celllocation', "");
        $smarty->assign('cellcategory', "");
        $smarty->assign_by_ref('cellname', $lte["desc_name"]);
        $smarty->assign('cellid', "");
        $smarty->assign_by_ref('celldescription', $lte["description"]);
        $smarty->assign('show_description', $lte["show_description"]);

	if ( ! isset($leday[$key]["over"]) ) {
		$leday[$key]["over"] = '';
	} else {
		$leday[$key]["over"] .= "<br />\n";
	}
        $leday[$key]["over"] .= $smarty->fetch("tiki-calendar_box.tpl");
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

$smarty->assign('tikicals', $_SESSION['CalendarViewTikiCals']);
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

if ( $_SESSION['CalendarViewList'] == 'list' )
	if ( is_array($listtikievents) ) foreach ( $listtikievents as $le )
		if ( is_array($le) ) foreach ( $le as $e )
			$listevents[] = $e;

$smarty->assign('listevents', $listevents);
$smarty->assign('var', '');
$smarty->assign('myurl', 'tiki-action_calendar.php');

$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-action_calendar.tpl');
$smarty->display("tiki.tpl");
?>
