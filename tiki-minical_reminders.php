<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ("lib/init/initlib.php");
include_once ('tiki-setup_base.php');
include_once ('lib/minical/minicallib.php');
$access->check_feature('feature_minical');
if (!$prefs['minical_reminders']) die;
//$refresh=$_REQUEST['refresh']*1000;
$refresh = 1000 * 60 * 1;
$evs = $minicallib->minical_get_events_to_remind($user, $prefs['minical_reminders']);
foreach ($evs as $ev) {
	$command = "<script type='text/javascript'>alert('event " . $ev['title'] . " will start at " . date("h:i", $ev['start']) . "');</script>";
	print ($command);
	$minicallib->minical_event_reminded($user, $ev['eventId']);
}
print ('<body onload="window.setInterval(\'location.reload()\',' . $refresh . ');">');
