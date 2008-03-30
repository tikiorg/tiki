<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_notifications.php,v 1.21.2.3 2008-03-20 16:03:30 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/notifications/notificationlib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$watches['user_registers'] = array(
	'label'=>tra('A user registers'),
	'type'=>'users',
	'url'=>'tiki-adminusers.php',
	'object'=>'*'
);
$watches['article_submitted'] = array(
	'label'=>tra('A user submits an article'),
	'type'=>'cms',
	'url'=>'tiki-list_submissions.php',
	'object'=>'*'
);
$watches['wiki_page_changes'] = array(
	'label'=>tra('Any wiki page is changed'),
	'type'=>'wiki',
	'url'=>'tiki-lastchanges.php',
	'object'=>'*'
);
$watches['wiki_page_changes_incl_minor'] = array(
	'label'=>tra('Any wiki page is changed, even minor changes'),
	'type'=>'wiki',
	'url'=>'tiki-lastchanges.php',
	'object'=>'*'
);
$watches['wiki_comment_changes'] = array(
	'label'=>tra('A comment in a wiki page is posted or edited'),
	'type'=>'wiki',
	'url'=>'',
	'object'=>'*'
);
$watches['php_error'] = array(
	'label'=>tra('PHP error'),
	'type'=>'system',
	'url'=>'',
	'object'=>'*'
);

$save = true;
$login = $email = '';
if (isset($_REQUEST["add"])) {
	check_ticket('admin-notif');
	if (!empty($_REQUEST['login'])) {
		if ($userlib->user_exists($_REQUEST['login'])) {
			$login = $_REQUEST['login'];
		} else {
			$tikifeedback[] = array('num'=>0,'mes'=>tra("Invalid username"));
			$save = false;
		}
	} elseif (!empty($_REQUEST['email'])) {
		if (validate_email($_REQUEST['email'],$prefs['validateEmail'])) {
			$email = $_REQUEST['email'];
		} else {
			$tikifeedback[] = array('num'=>0,'mes'=>tra("Invalid email"));
			$save = false;
		}
	} else {
		$tikifeedback[] = array('num'=>0,'mes'=>tra("You need to provide a username or an email"));
		$save = false;
	}
	if ($save and isset($_REQUEST['event']) and isset($watches[$_REQUEST['event']])) {
		$tikilib->add_user_watch($login, 
			$_REQUEST["event"], 
			$watches[$_REQUEST['event']]['object'], 
			$watches[$_REQUEST['event']]['type'], 
			$watches[$_REQUEST['event']]['label'],
			$watches[$_REQUEST['event']]['url'],
			$email);
	}
}

if (!empty($tikifeedback)) {
	$smarty->assign_by_ref('tikifeedback', $tikifeedback);
}
if (isset($_REQUEST["removeevent"])) {
  $area = 'delnotif';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$tikilib->remove_user_watch_by_id($_REQUEST["removeevent"]);
  } else {
    key_get($area);
  }
}
if (isset($_REQUEST['delsel_x']) && isset($_REQUEST['checked'])) {
	check_ticket('admin-notif');
	foreach($_REQUEST['checked'] as $id) {
		$tikilib->remove_user_watch_by_id($id);
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
if (isset($_REQUEST['numrows']) && $maxRecords != $_REQUEST['numrows']) {
	$maxRecords = $_REQUEST['numrows'];
	$smarty->assign('numrows', $maxRecords);
 }

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $tikilib->list_watches($offset, $maxRecords, $sort_mode, $find);

$smarty->assign_by_ref('cant', $channels['cant']);
$smarty->assign_by_ref('channels', $channels["data"]);

ask_ticket('admin-notif');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_notifications.tpl');
$smarty->display("tiki.tpl");

?>
