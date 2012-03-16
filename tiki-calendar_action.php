<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'calendar';
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/newsletters/nllib.php');

$headerlib->add_cssfile('css/calendar.css', 20);
# perms are
# 	$tiki_p_view_calendar
# 	$tiki_p_admin_calendar
# 	$tiki_p_change_events
# 	$tiki_p_add_events
$access->check_feature('feature_calendar');

if ( isset($_REQUEST['calitemId']) && !empty($_REQUEST['action']) ) {
	$cal_id = $calendarlib->get_calendarid($_REQUEST['calitemId']);
	if ( $cal_id != 0 ) {
		$calperms = Perms::get(array( 'type' => 'calendar', 'object' => $cal_id ));
		if ( $calperms->change_events ) {
			switch ($_REQUEST['action']) {
			case 'move' :
				$calendarlib->move_item($_REQUEST['calitemId'], $_REQUEST['delta']);
    			break;
			case 'resize':
				$calendarlib->resize_item($_REQUEST['calitemId'], $_REQUEST['delta']);
    			break;
			}
		}
	}
}
