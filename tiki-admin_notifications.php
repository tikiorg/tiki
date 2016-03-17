<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array(
		'staticKeyFilters' => array(
			'offset' => 'digits',
			'maxRecords' => 'digits',
			'removeevent' => 'digits',
			'removetype' => 'word',
			'daconfirm' => 'word',
			'ticket' => 'word',
			'sort_mode' => 'word',
			'find' => 'striptags',
			'email' => 'email',
			'event' => 'text',
			'add' => 'alpha',
			'delsel_x' => 'alpha',
		) ,
		'staticKeyFiltersForArrays' => array(
			'checked' => 'alnum',
		) ,
	)
);
// Initialization
require_once ('tiki-setup.php');
$access->check_permission(array('tiki_p_admin_notifications'));

$notificationlib = TikiLib::lib('notification');

$auto_query_args = array(
	'offset',
	'sort_mode',
	'find',
	'maxRecords'
);
$watches = $notificationlib->get_global_watch_types();

$save = true;
$login = '';
if (isset($_REQUEST["add"])) {
	check_ticket('admin-notif');
	if (!empty($_REQUEST['login'])) {
		if ($userlib->user_exists($_REQUEST['login'])) {
			$login = $_REQUEST['login'];
		} else {
			$tikifeedback[] = array(
				'num' => 0,
				'mes' => tra("Invalid username")
			);
			$save = false;
		}
	} elseif (!empty($_REQUEST['email'])) {
		if (validate_email($_REQUEST['email'], $prefs['validateEmail'])) {
			$email = $_REQUEST['email'];
		} else {
			$tikifeedback[] = array(
				'num' => 0,
				'mes' => tra("Invalid email")
			);
			$save = false;
		}
	} else {
		$tikifeedback[] = array(
			'num' => 0,
			'mes' => tra("You need to provide a username or an email")
		);
		$save = false;
	}
	if ($save and isset($_REQUEST['event']) and isset($watches[$_REQUEST['event']])) {
		$result = $tikilib->add_user_watch($login, $_REQUEST["event"], $watches[$_REQUEST['event']]['object'], $watches[$_REQUEST['event']]['type'], $watches[$_REQUEST['event']]['label'], $watches[$_REQUEST['event']]['url'], isset($email) ? $email : NULL);
		if (!$result) {
			$tikifeedback[] = array(
				'mes' => tra("The user has no email set. No notifications will be sent.")
			);			
		}
	}
}
if (!empty($tikifeedback)) {
	$smarty->assign_by_ref('tikifeedback', $tikifeedback);
}
if (isset($_REQUEST["removeevent"]) && isset($_REQUEST['removetype'])) {
	$access->check_authenticity();
	if ($_REQUEST['removetype'] == 'user') {
		$tikilib->remove_user_watch_by_id($_REQUEST["removeevent"]);
	} else {
		$tikilib->remove_group_watch_by_id($_REQUEST["removeevent"]);
	}
}
if (isset($_REQUEST['delsel_x']) && isset($_REQUEST['checked'])) {
	check_ticket('admin-notif');
	foreach ($_REQUEST['checked'] as $id) {
		if (strpos($id, 'user') === 0) $tikilib->remove_user_watch_by_id(substr($id, 4));
		else $tikilib->remove_group_watch_by_id(substr($id, 5));
	}
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'event_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('find', $find);
if (!empty($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
}
$smarty->assign_by_ref('watches', $watches);
$smarty->assign_by_ref('maxRecords', $maxRecords);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $tikilib->list_watches($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant', $channels['cant']);
$smarty->assign_by_ref('channels', $channels["data"]);
if ($prefs['feature_trackers'] == 'y') {
	$trklib = TikiLib::lib('trk');
	$trackers = $trklib->get_trackers_options(0, 'outboundemail', $find, 'empty');
	$smarty->assign_by_ref('trackers', $trackers);
}
if ($prefs['feature_forums'] == 'y') {
	$commentslib = TikiLib::lib('comments');
	$forums = $commentslib->get_outbound_emails();
	$smarty->assign_by_ref('forums', $forums);
}
ask_ticket('admin-notif');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_notifications.tpl');
$smarty->display("tiki.tpl");
