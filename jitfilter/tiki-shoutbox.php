<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-shoutbox.php,v 1.18.2.1 2008-02-14 11:10:14 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/shoutbox/shoutboxlib.php');

if ($prefs['feature_shoutbox'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_shoutbox");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_shoutbox != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["msgId"])) {
	$_REQUEST["msgId"] = 0;
}

$smarty->assign('msgId', $_REQUEST["msgId"]);
if ($_REQUEST["msgId"]) {
	$info = $shoutboxlib->get_shoutbox($_REQUEST["msgId"]);
	$owner=$info["user"];
	if ($tiki_p_admin_shoutbox != 'y' &&  $owner != $user) {
		$smarty->assign('msg', tra("You do not have permission to edit messages $owner"));
		$smarty->display("error.tpl");
		die;
	}
} else {
	$info = array();
	$info["message"] = '';
	$info["user"] = $user;
	$owner=$info["user"];
}

$smarty->assign('message', $info["message"]);
$smarty->assign('user', $info["user"]);

if ($tiki_p_admin_shoutbox == 'y' || $user == $owner ) {
	if (isset($_REQUEST["remove"])) {
		$area = 'delshoutboxitem';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$shoutboxlib->remove_shoutbox($_REQUEST["remove"]);
		} else {
			key_get($area);
		}
	} elseif (isset($_REQUEST["shoutbox_admin"])) {
		$prefs['shoutbox_autolink'] = (isset($_REQUEST["shoutbox_autolink"])) ? 'y' : 'n';
		$tikilib->set_preference('shoutbox_autolink',$prefs['shoutbox_autolink']);
	}
}

if ($tiki_p_post_shoutbox == 'y') {
	if (isset($_REQUEST["save"]) && !empty($_REQUEST['message'])) {
		check_ticket('shoutbox');
		if (($prefs['feature_antibot'] == 'y' && empty($user)) && (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode'])) {
			$smarty->assign('msg',tra("You have mistyped the anti-bot verification code; please try again."));
			if (!empty($_REQUEST['message'])) $smarty->assign_by_ref('message', $_REQUEST['message']);
		} else {
			$shoutboxlib->replace_shoutbox($_REQUEST['msgId'], $user, $_REQUEST['message']);
			$smarty->assign('msgId', '0');
			$smarty->assign('message', '');
		}
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'timestamp_desc';
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
$channels = $shoutboxlib->list_shoutbox($offset, $maxRecords, $sort_mode, $find);

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

ask_ticket('shoutbox');

// Display the template
$smarty->assign('mid', 'tiki-shoutbox.tpl');
$smarty->display("tiki.tpl");

?>
