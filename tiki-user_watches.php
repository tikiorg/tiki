<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_watches.php,v 1.4 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('tiki-setup.php');

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($feature_user_watches != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST['hash'])) {
	$tikilib->remove_user_watch_by_hash($_REQUEST['hash']);
}

if (isset($_REQUEST["delete"]) && isset($_REQUEST['watch'])) {
	foreach (array_keys($_REQUEST["watch"])as $item) {
		$tikilib->remove_user_watch_by_hash($item);
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

$smarty->assign('mid', 'tiki-user_watches.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>