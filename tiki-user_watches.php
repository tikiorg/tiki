<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_watches.php,v 1.9 2004-03-31 07:38:41 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('tiki-setup.php');

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($feature_user_watches != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_user_watches");

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['hash'])) {
  $area = 'deluserwatch';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$tikilib->remove_user_watch_by_hash($_REQUEST['hash']);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["delete"]) && isset($_REQUEST['watch'])) {
  $area = 'delwatches';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
	foreach (array_keys($_REQUEST["watch"])as $item) {
		$tikilib->remove_user_watch_by_hash($item);
	}
  } else {
	key_get($area);
  }
}

// Get watch events and put them in watch_events
$events = $tikilib->get_watches_events();
$smarty->assign('events', $events);

// if not set event type then all
if (!isset($_REQUEST['event']))
	$_REQUEST['event'] = '';

// get all the information for the event
$watches = $tikilib->get_user_watches($user, $_REQUEST['event']);
$smarty->assign('watches', $watches);

include_once ('tiki-mytiki_shared.php');

ask_ticket('user-watches');

$smarty->assign('mid', 'tiki-user_watches.tpl');
$smarty->display("tiki.tpl");

?>
