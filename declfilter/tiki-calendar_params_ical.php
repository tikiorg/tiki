<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-calendar_edit_item.php,v 1.21.2.4 2008-01-17 15:53:26 tombombadilom Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$section = 'calendar';
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/newsletters/nllib.php');

if ($prefs['feature_calendar'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
  $smarty->display("error.tpl");
  die;
}
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}

$smarty->assign('edit',false);

$caladd = array();
$rawcals = $calendarlib->list_calendars();
foreach ($rawcals["data"] as $cal_id=>$cal_data)
	$caladd["$cal_id"] = $cal_data;
$smarty->assign('listcals',$caladd);
$smarty->assign('mid', 'tiki-calendar_params_ical.tpl');
$smarty->display("tiki.tpl");
?>
