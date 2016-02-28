<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/webmail/webmaillib.php');
$contactlib = TikiLib::lib('contact');

$access->check_feature('feature_webmail');

$smarty->assign('element', $_REQUEST["element"]);

if (!isset($_REQUEST["contactId"])) {
	$_REQUEST["contactId"] = 0;
}

$smarty->assign('contactId', $_REQUEST["contactId"]);

if ($_REQUEST["contactId"]) {
	$info = $contactlib->get_contact($_REQUEST["contactId"], $user);
} else {
	$info = array();

	$info["firstName"] = '';
	$info["lastName"] = '';
	$info["email"] = '';
	$info["nickname"] = '';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$contactlib->remove_contact($_REQUEST["remove"], $user);
}

if (isset($_REQUEST["save"])) {
	check_ticket('webmail-contact');
	$contactlib->replace_contact($_REQUEST["contactId"], $_REQUEST["firstName"], $_REQUEST["lastName"], $_REQUEST["email"], $_REQUEST["nickname"], $user);

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

if (!isset($_REQUEST["initial"])) {
	$contacts = $contactlib->list_contacts($user, $offset, $maxRecords, $sort_mode, $find, true);
} else {
	$contacts = $contactlib->list_contacts_by_letter($user, $offset, $maxRecords, $sort_mode, $_REQUEST["initial"]);
}

$cant_pages = ceil(count($contacts) / $maxRecords);

$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if (count($contacts) > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('channels', $contacts);

ask_ticket('webmail-contact');

//$smarty->display("tiki-webmail_contacts.tpl");
$smarty->display("tiki-webmail_contacts.tpl");
