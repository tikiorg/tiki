<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-calendars_rss.php,v 1.12.2.2 2008-03-16 23:11:14 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');
require_once ('lib/calendar/calendarlib.php');

if (!isset($prefs['rss_calendar']) || $prefs['rss_calendar'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_calendar != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "calendars";
$calendarIds=array();
if (isset($_REQUEST["calendarIds"])) {
    $calendarIds = $_REQUEST["calendarIds"];
    if (!is_array($calendarIds)) {
	$calendarIds = array($calendarIds);
    }	
    $uniqueid = $feed.".".implode(".",$calendarIds);
} else {
    $uniqueid = $feed;
    $calendarIds = array();
}
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$tmp = tra("Tiki RSS feed for calendars");
	$title = (!empty($title_rss_calendars)) ? $title_rss_calendars : $tmp;
	$tmp = tra("Upcoming events.");
	$desc = (!empty($desc_rss_calendars)) ? $desc_rss_calendars : $tmp;
	$id = "calitemId";
	$titleId = "name";
	$descId = "body";
	$dateId = "start";
	$authorId = "user";
	$readrepl = "tiki-calendar_edit_item.php?viewcalitemId=%s";

        $tmp = $prefs['title_rss_'.$feed];
        if ($tmp<>'') $title = $tmp;
        $tmp = $prefs['desc_rss_'.$feed];
        if ($desc<>'') $desc = $tmp;

	$allCalendars = $calendarlib->list_calendars();

	// build a list of viewable calendars
	$calendars = array();
	foreach ($allCalendars['data'] as $cal) {

	    $visible = false;
	    if (sizeof($calendarIds) == 0 || in_array($cal['calendarId'],$calendarIds)) {
			if ($cal["personal"] == "y") {
			    if ($user) {
					$visible = true;
			    }
			} else {
			    if ($userlib->object_has_one_permission($cal['calendarId'],'calendar')) {
					if ($userlib->object_has_permission($user, $cal['calendarId'], 'calendar', 'tiki_p_view_calendar')) {
					    $visible = true;
					} 
			    } else {
					$visible = ($tiki_p_view_calendar == 'y');
			    }
			}
	    }
	    if ($visible) {
			$calendars[] = $cal['calendarId'];
	    }
	}

	$maxCalEntries = $prefs['max_rss_calendar'];
	$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
	$items = $calendarlib->list_raw_items($calendars, "", $tikilib->now, $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1), 0, $maxCalEntries);

	require_once("lib/smarty_tiki/modifier.tiki_long_datetime.php");
	require_once("lib/smarty_tiki/modifier.isodate.php");

	for ($i = 0; $i < sizeof($items); $i++) {
		$start_d = smarty_modifier_isodate($items[$i]["start"]);
		$end_d = smarty_modifier_isodate($items[$i]["end"]);
	
		$items[$i]["body"] = "<div class=\"vevent\"> <span class=\"summary\">" . $items[$i]["name"] . "</span>"."<br />\n";
 	    $items[$i]["body"] .=  "<abbr class=\"dtstart\" title=\"" .$start_d ."\">" .tra("Start:") . " " .smarty_modifier_tiki_long_datetime($items[$i]["start"]) . "</abbr>" ."<br />\n";
	    $items[$i]["body"] .=  "<abbr class=\"dtend\" title=\""  .$end_d ."\">" . tra("End:") . " " .smarty_modifier_tiki_long_datetime($items[$i]["end"]). "</abbr>"."<br />\n";
	    $items[$i]["body"] .=  "<span class=\"descprition\">".($items[$i]["description"]) . "</span>". "</div>";
	}

	$changes = array('data' => $items);
	unset($items);

	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
