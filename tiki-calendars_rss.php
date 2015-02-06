<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$rsslib = TikiLib::lib('rss');
$calendarlib = TikiLib::lib('calendar');

if (!isset($prefs['feed_calendar']) || $prefs['feed_calendar'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_view_calendar','tiki_p_admin_calendar'));
if ($res) {
	if ($res['header'] == 'y') {
		header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
		header('HTTP/1.0 401 Unauthorized');
	}
	$errmsg=$res['msg'];
	require_once ('tiki-rss_error.php');
}

$feed = "calendar";
$calendarIds=array();
if (isset($_REQUEST["calendarIds"])) {
    $calendarIds = $_REQUEST["calendarIds"];
    if (!is_array($calendarIds)) {
		$calendarIds = array($calendarIds);
    }
    $uniqueid = $feed.".".implode(".", $calendarIds);
} else {
    $uniqueid = $feed;
    $calendarIds = array();
}
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = $prefs['feed_'.$feed.'_title'];
	$desc = $prefs['feed_'.$feed.'_desc'];
	$id = "calitemId";
	$titleId = "name";
	$descId = "body";
	$dateId = "start";
	$authorId = "user";
	$readrepl = "tiki-calendar_edit_item.php?viewcalitemId=%s";

	$rawcals = $calendarlib->list_calendars();
	$rawcals['data'] = Perms::filter(array( 'type' => 'calendar' ), 'object', $rawcals['data'], array( 'object' => 'calendarId' ), 'view_calendar');

	// build a list of viewable calendars
	$calendars = array();
	foreach ($rawcals['data'] as $cal) {
		$calendars[] = $cal['calendarId'];
	}

	$maxCalEntries = $prefs['feed_calendar_max'];
	$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
	$items = $calendarlib->list_raw_items($calendars, "", $tikilib->now, $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1), 0, $maxCalEntries);

	require_once("lib/smarty_tiki/modifier.tiki_short_datetime.php");
	require_once("lib/smarty_tiki/modifier.tiki_long_datetime.php");
	require_once("lib/smarty_tiki/modifier.compactisodate.php");

	foreach ($items as &$item) {
		$start_d = smarty_modifier_compactisodate($item["start"]);
		$end_d = smarty_modifier_compactisodate($item["end"]);

		$item["body"] = "<div class=\"vevent\"> <span class=\"summary\">" . $item["name"] . "</span>"."<br />\n";
 	    $item["body"] .=  "<abbr class=\"dtstart\" title=\"" .$start_d ."\">" .tra("Start:") . " " .smarty_modifier_tiki_long_datetime($item["start"]) . "</abbr>" ."<br />\n";
	    $item["body"] .=  "<abbr class=\"dtend\" title=\""  .$end_d ."\">" . tra("End:") . " " .smarty_modifier_tiki_long_datetime($item["end"]). "</abbr>"."<br />\n";
	    $item["body"] .=  "<span class=\"descprition\">".($item["description"]) . "</span>". "</div>";
	}
	unset($item);

	$changes = array('data' => $items);
	unset($items);

	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
