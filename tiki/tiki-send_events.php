<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-send_events.php,v 1.2 2005-01-22 22:54:55 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/events/evlib.php');

if ($feature_events != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_events");
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["evId"])) $_REQUEST["evId"] = 0;
$smarty->assign('evId', $_REQUEST["evId"]);

$newsl = $evlib->list_events(0, -1, 'created_desc', '');
$events = array('data'=>array(),'cant'=>0);
for ($i = 0; $i < $newsl["cant"]; $i++) {
	if ($tiki_p_admin == 'y' || $tiki_p_admin_events == 'y' || $userlib->object_has_permission($user, $newsl["data"][$i]["evId"], 'event', 'tiki_p_send_events')) {
		$events["data"][] = $newsl["data"][$i];
		$events["cant"]++;
	}
}

if (!$events["cant"]) {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('events', $events["data"]);

if ($_REQUEST["evId"]) {
	$ev_info = $evlib->get_event($_REQUEST["evId"]);

	if (!isset($_REQUEST["editionId"])) $_REQUEST["editionId"] = 0;

	if ($_REQUEST["editionId"]) {
		$info = $evlib->get_edition($_REQUEST["editionId"]);
	} else {
		$info = array();
		$info["data"] = '';
		$info["subject"] = '';
	}
	$smarty->assign('info', $info);
}

if (isset($_REQUEST["remove"])) {
	$area = 'delevent';
	if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$evlib->remove_edition($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["data"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
	$smarty->assign("templateId", $_REQUEST["templateId"]);
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');
	//if (eregi("\<[ \t]*html[ \t\>]",  $_REQUEST["data"]))  // html event - this will be the text sent with the html part
	//	$smarty->assign('txt', nl2br(strip_tags($_REQUEST["data"])));
	//TODO: the sent text version is not pretty: the text must be a textarea
	if (isset($_REQUEST["data"])) {
		$info["data"] = $_REQUEST["data"];
		$info["dataparsed"] = $tikilib->parse_data($_REQUEST["data"]);
	} else {
		$info["data"] = '';
	}
	if (isset($_REQUEST["subject"])) {
		$info["subject"] = $_REQUEST["subject"];
	} else {
		$info["subject"] = '';
	}
	$smarty->assign('info', $info);
}

$smarty->assign('presend', 'n');

if (isset($_REQUEST["save"])) {
	check_ticket('send-event');
	// Now send the event to all the email addresses and save it in sent_events
	$smarty->assign('presend', 'y');

	$subscribers = $evlib->get_subscribers($_REQUEST["evId"]);
	$smarty->assign('evId', $_REQUEST["evId"]);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('dataparsed', $tikilib->parse_data($_REQUEST["data"]));
	$smarty->assign('subject', $_REQUEST["subject"]);
	$cant = count($subscribers);
	$smarty->assign('subscribers', $cant);
}

$smarty->assign('emited', 'n');

if (isset($_REQUEST["send"])) {
	include_once ('lib/webmail/tikimaillib.php');
	check_ticket('send-event');
	$subscribers = $evlib->get_subscribers($_REQUEST["evId"]);

	$mail = new TikiMail();
	$txt = preg_replace(array("/\s+/","/&nbsp;/"),array(" "," "),strip_tags($tikilib->parse_data($_REQUEST["data"]))); 
	$html = $tikilib->parse_data($_REQUEST["data"]); 
	$sent = 0;
	$unsubmsg = '';

	foreach ($subscribers as $email) {
		$userEmail = $userlib->get_user_by_email($email);
		if ($userEmail)
			$mail->setUser($userEmail);
		$mail->setSubject($_REQUEST["subject"]); // htmlMimeMail memorised the encoded subject 
		$languageEmail = !$userEmail? $language: $tikilib->get_user_preference($userEmail, "language", $language);
		if ($ev_info["unsubMsg"] == 'y')
			$unsubmsg = $evlib->get_unsub_msg($_REQUEST["evId"], $email, $languageEmail);
		$mail->setHtml($html.$unsubmsg, $txt. strip_tags($unsubmsg));
		$mail->buildMessage();
		if ($mail->send(array($email)))
			$sent++;
	}

	$smarty->assign('sent', $sent);
	$smarty->assign('emited', 'y');
	$evlib->replace_edition($_REQUEST["evId"], $_REQUEST["subject"], $_REQUEST["data"], $sent);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'sent_desc';
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
$channels = $evlib->list_editions($_REQUEST["evId"], $offset, $maxRecords, $sort_mode, $find);

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

if ($tiki_p_use_content_templates == 'y') {
	$templates = $tikilib->list_templates('events', 0, -1, 'name_asc', '');
}
include_once("textareasize.php");

$smarty->assign_by_ref('templates', $templates["data"]);

ask_ticket ('send-event');

// Display the template
$smarty->assign('mid', 'tiki-send_events.tpl');
$smarty->display("tiki.tpl");

?>

