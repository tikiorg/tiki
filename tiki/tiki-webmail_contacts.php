<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-webmail_contacts.php,v 1.7 2004-03-28 07:32:24 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/webmail/webmaillib.php');

$smarty->assign('element', $_REQUEST["element"]);

if (!isset($_REQUEST["contactId"])) {
	$_REQUEST["contactId"] = 0;
}

$smarty->assign('contactId', $_REQUEST["contactId"]);

if ($_REQUEST["contactId"]) {
	$info = $webmaillib->get_contact($_REQUEST["contactId"], $user);
} else {
	$info = array();

	$info["firstName"] = '';
	$info["lastName"] = '';
	$info["email"] = '';
	$info["nickname"] = '';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	check_ticket('webmail-contact');
	$webmaillib->remove_contact($_REQUEST["remove"], $user);
}

if (isset($_REQUEST["save"])) {
	check_ticket('webmail-contact');
	$webmaillib->replace_contact($_REQUEST["contactId"], $_REQUEST["firstName"], $_REQUEST["lastName"], $_REQUEST["email"],
		$_REQUEST["nickname"], $user);

	$info["firstName"] = '';
	$info["lastName"] = '';
	$info["email"] = '';
	$info["nickname"] = '';
	$smarty->assign('info', $info);
	$smarty->assign('contactId', 0);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'email_asc';
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
$maxRecords = 20;
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["letter"])) {
	$channels = $webmaillib->list_contacts($user, $offset, $maxRecords, $sort_mode, $find);
} else {
	$channels = $webmaillib->list_contacts_by_letter($user, $offset, $maxRecords, $sort_mode, $_REQUEST["letter"]);
}

$cant_pages = ceil($channels["cant"] / $maxRecords);

$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

$letters = 'a-b-c-d-e-f-g-h-i-j-k-l-m-n-o-p-q-r-s-t-u-v-w-x-y-z';
$letters = split('-', $letters);
$smarty->assign('letters', $letters);

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

ask_ticket('webmail-contact');

//$smarty->display("tiki-webmail_contacts.tpl");
$smarty->display("tiki-webmail_contacts.tpl");

?>
