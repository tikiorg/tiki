<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once ('tiki-setup.php');

$access->check_feature('bigbluebutton_feature');

$bigbluebuttonlib = TikiLib::lib('bigbluebutton');
$loglib = TikiLib::lib('logs');
$user_count = $users = $rooms = $join_log = $total_log = $log_out = $log_in = array();
$total_log = $loglib->get_log_count("bigbluebutton", "Joined Room");
foreach ($total_log as $row) {
	$join_log[$row['action']][$row['object']][] = $row;
}
if ($join_log['Joined Room']) {
	foreach ($join_log['Joined Room'] as $room => $member) {
		$users[$room] = array_map(
			function ($i) {
				return $i['user'];
			},
			$member
		);
		$user_count[$room] = $bigbluebuttonlib->getAttendees($room, true);
		if (isset($user_count[$room])) {
			foreach ($user_count[$room] as $user_room) {
				$login_user[$room][] = $user_room['fullName'];
			}
		}
		if (empty($login_user[$room])) {
			foreach ($users[$room] as $user) {
				TikiLib::lib('logs')->add_action('Left Room', $room, 'bigbluebutton', "room is empty", $user);
			}
		} else {
			foreach (array_diff($users[$room], $login_user[$room]) as $user) {
				TikiLib::lib('logs')->add_action('Left Room', $room, 'bigbluebutton', "", $user);
			}
		}
	}
}
