<?php

include 'tiki-setup.php';
include 'lib/calendar/tikicalendarlib.php';

if ($feature_action_calendar != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_action_calendar");
  $smarty->display("error.tpl");
  die;
}

if ($tiki_p_view_tiki_calendar != 'y') {
  $smarty->assign('msg', tra("Permission denied you cannot view the Tiki calendar"));
	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST["tikicals"]) and is_array($_REQUEST["tikicals"])) {
	$_SESSION['CalendarViewTikiCals'] = $_REQUEST["tikicals"];
}

$headerlib->add_cssfile('css/calendar.css',20);

$tikiItems = array(
  "wiki" => array( "label" => tra("Wiki"), "feature" => "$feature_wiki", "right" => "$tiki_p_view"),
  "gal" => array( "label" => tra("Image Gallery"), "feature" => "$feature_galleries", "right" => "$tiki_p_view_image_gallery"),
  "art" => array( "label" => tra("Articles"), "feature" => "$feature_articles", "right" => "$tiki_p_read_article"),
  "blog" => array( "label" => tra("Blogs"), "feature" => "$feature_blogs", "right" => "$tiki_p_read_blog"),
  "forum" => array( "label" => tra("Forums"), "feature" => "$feature_forums", "right" => "$tiki_p_forum_read"),
  "dir" => array( "label" => tra("Directory"), "feature" => "$feature_directory", "right" => "$tiki_p_view_directory"),
  "fgal" => array( "label" => tra("File Gallery"), "feature" => "$feature_file_galleries", "right" => "$tiki_p_view_file_gallery"),
  "faq" => array( "label" => tra("FAQs"), "feature" => $feature_faqs, "right" => $tiki_p_view_faqs),
  "quiz" => array( "label" => tra("Quizzes"), "feature" => $feature_quizzes, "right" => $tiki_p_take_quiz),
  "track" => array( "label" => tra("Trackers"), "feature" => "$feature_trackers", "right" => "$tiki_p_view_trackers"),
  "surv" => array( "label" => tra("Survey"), "feature" => "$feature_surveys", "right" => "$tiki_p_take_survey"),
  "nl" => array( "label" => tra("Newsletter"), "feature" => "$feature_newsletters", "right" => "$tiki_p_subscribe_newsletters"),
  "eph" => array( "label" => tra("Ephemerides"), "feature" => "$feature_eph", "right" => "$tiki_p_view_eph"),
  "chart" => array( "label" => tra("Charts"), "feature" => "$feature_charts", "right" => "$tiki_p_view_chart")
);

$smarty->assign('tikiItems', $tikiItems);

include_once("tiki-calendar_setup.php");
include_once("tiki-calendar_nav.php");

$listtikievents = $tikicalendarlib->list_tiki_items($_SESSION['CalendarViewTikiCals'] , $user, $viewstart, $viewend, 0, 50, 'name_desc', '');
$smarty->assign_by_ref('listtikievents', $listtikievents);

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
$smarty->assign('var', '');
$smarty->assign('myurl', 'tiki-action_calendar.php');

$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-action_calendar.tpl');
$smarty->display("tiki.tpl");
?>
