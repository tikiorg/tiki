<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_notifications.php,v 1.10 2004-06-11 02:55:15 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/notifications/notificationlib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["add"])) {
	check_ticket('admin-notif');
	$notificationlib->add_mail_event($_REQUEST["event"], '*', $_REQUEST["email"]);
}

if (isset($_REQUEST["removeevent"])) {
  $area = 'delnotif';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$notificationlib->remove_mail_event($_REQUEST["removeevent"], $_REQUEST["object"], $_REQUEST["email"]);
  } else {
    key_get($area);
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

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $notificationlib->list_mail_events($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

$admin_mail = $userlib->get_user_email('admin');
$smarty->assign('admin_mail', $admin_mail);
ask_ticket('admin-notif');
// Display the template
$smarty->assign('mid', 'tiki-admin_notifications.tpl');
$smarty->display("tiki.tpl");

?>
