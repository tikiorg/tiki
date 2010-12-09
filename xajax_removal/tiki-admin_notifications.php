<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
			'login' => 'username',
			'email' => 'email',
			'event' => 'word',
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
include_once ('lib/notifications/notificationlib.php');
$access->check_permission(array('tiki_p_admin_notifications'));

$auto_query_args = array(
	'offset',
	'sort_mode',
	'find',
	'maxRecords'
);
$watches['user_registers'] = array(
	'label' => tra('A user registers') ,
	'type' => 'users',
	'url' => 'tiki-adminusers.php',
	'object' => '*'
);
$watches['article_submitted'] = array(
	'label' => tra('A user submits an article') ,
	'type' => 'cms',
	'url' => 'tiki-list_submissions.php',
	'object' => '*'
);
$watches['article_edited'] = array(
	'label' => tra('A user edits an article') ,
	'type' => 'cms',
	'url' => 'tiki-list_articles.php',
	'object' => '*'
);
$watches['article_deleted'] = array(
	'label' => tra('A user deletes an article') ,
	'type' => 'cms',
	'url' => 'tiki-list_submissions.php',
	'object' => '*'
);
$watches['wiki_page_changes'] = array(
	'label' => tra('Any wiki page is changed') ,
	'type' => 'wiki page',
	'url' => 'tiki-lastchanges.php',
	'object' => '*'
);
$watches['wiki_page_changes_incl_minor'] = array(
	'label' => tra('Any wiki page is changed, even minor changes') ,
	'type' => 'wiki page',
	'url' => 'tiki-lastchanges.php',
	'object' => '*'
);
$watches['wiki_comment_changes'] = array(
	'label' => tra('A comment in a wiki page is posted or edited') ,
	'type' => 'wiki page',
	'url' => '',
	'object' => '*'
);
$watches['php_error'] = array(
	'label' => tra('PHP error') ,
	'type' => 'system',
	'url' => '',
	'object' => '*'
);
$watches['fgal_quota_exceeded'] = array(
	'label' => tra('File gallery quota exceeded') ,
	'type' => 'file gallery',
	'url' => '',
	'object' => '*'
);
$save = true;
$login = $email = '';
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
		$tikilib->add_user_watch($login, $_REQUEST["event"], $watches[$_REQUEST['event']]['object'], $watches[$_REQUEST['event']]['type'], $watches[$_REQUEST['event']]['label'], $watches[$_REQUEST['event']]['url'], $email);
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
	foreach($_REQUEST['checked'] as $id) {
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
	global $trklib;
	include_once ('lib/trackers/trackerlib.php');
	$trackers = $trklib->get_trackers_options(0, 'outboundemail', $find, 'empty');
	$smarty->assign_by_ref('trackers', $trackers);
}
if ($prefs['feature_forums'] == 'y') {
	include_once ('lib/comments/commentslib.php');
	$commentslib = new Comments($dbTiki);
	$forums = $commentslib->get_outbound_emails();
	$smarty->assign_by_ref('forums', $forums);
}
ask_ticket('admin-notif');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$admin_mail = $userlib->get_user_email('admin');
$smarty->assign('admin_mail', $admin_mail);
// Display the template
$smarty->assign('mid', 'tiki-admin_notifications.tpl');
$smarty->display("tiki.tpl");
