<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-send_newsletters.php,v 1.14 2004-03-31 07:38:41 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/newsletters/nllib.php');
include_once ('lib/webmail/tikimaillib.php');

if ($feature_newsletters != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_newsletters != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["nlId"]))
	$_REQUEST["nlId"] = 0;

$smarty->assign('nlId', $_REQUEST["nlId"]);

$newsletters = $nllib->list_newsletters(0, -1, 'created_desc', '');
$smarty->assign('newsletters', $newsletters["data"]);

$nl_info = $nllib->get_newsletter($_REQUEST["nlId"]);
// $nl_info["name"] = '';
// $nl_info["description"] = '';
// $nl_info["allowUserSub"] = 'y';
// $nl_info["allowAnySub"] = 'n';
// $nl_info["unsubMsg"] = 'y';
// $nl_info["validateAddr"] = 'y';

if (!isset($_REQUEST["editionId"]))
	$_REQUEST["editionId"] = 0;

if ($_REQUEST["editionId"]) {
	$info = $nllib->get_edition($_REQUEST["editionId"]);
} else {
	$info = array();

	$info["data"] = '';
	$info["subject"] = '';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
  $area = 'delnewsletter';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$nllib->remove_edition($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["data"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');
	//if (eregi("\<[ \t]*html[ \t\>]",  $_REQUEST["data"]))  // html newsletter - this will be the text sent with the html part
	//	$smarty->assign('txt', nl2br(strip_tags($_REQUEST["data"])));
	//TODO: the sent text version is not pretty: the text must be a textarea
	$info["data"] = $_REQUEST["data"];
	$info["subject"] = $_REQUEST["subject"];
	$smarty->assign('info', $info);
}

$smarty->assign('presend', 'n');

if (isset($_REQUEST["save"])) {
	check_ticket('send-newsletter');
	// Now send the newsletter to all the email addresses and save it in sent_newsletters
	$smarty->assign('presend', 'y');

	$subscribers = $nllib->get_subscribers($_REQUEST["nlId"]);
	$smarty->assign('nlId', $_REQUEST["nlId"]);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('subject', $_REQUEST["subject"]);
	$cant = count($subscribers);
	$smarty->assign('subscribers', $cant);
}

$smarty->assign('emited', 'n');

if (isset($_REQUEST["send"])) {
	check_ticket('send-newsletter');
	$subscribers = $nllib->get_subscribers($_REQUEST["nlId"]);

	$mail = new TikiMail();
	$txt = strip_tags($_REQUEST["data"]); //TODO: be able to have a different text
	$sent = 0;
	$unsubmsg = '';

	foreach ($subscribers as $email) {
		$userEmail = $userlib->get_user_by_email($email);
		$mail->setUser($userEmail);
		$mail->setSubject($_REQUEST["subject"]); // htmlMimeMail memorised the encoded subject 
		$languageEmail = !$userEmail? $language: $tikilib->get_user_preference($userEmail, "language", $language);
		if ($nl_info["unsubMsg"] = 'y')
			$unsubmsg = $nllib->get_unsub_msg($_REQUEST["nlId"], $email, $languageEmail);
		$mail->setHtml($_REQUEST["data"] . $unsubmsg, $txt.$unsubmsg);
		$mail->buildMessage();
		if ($mail->send(array($email)))
			$sent++;
	}

	$smarty->assign('sent', $sent);
	$smarty->assign('emited', 'y');
	$nllib->replace_edition($_REQUEST["nlId"], $_REQUEST["subject"], $_REQUEST["data"], $sent);
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
$channels = $nllib->list_editions($offset, $maxRecords, $sort_mode, $find);

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
	$templates = $tikilib->list_templates('newsletters', 0, -1, 'name_asc', '');
}
include_once("textareasize.php");

$smarty->assign_by_ref('templates', $templates["data"]);

ask_ticket ('send-newsletter');

// Display the template
$smarty->assign('mid', 'tiki-send_newsletters.tpl');
$smarty->display("tiki.tpl");

?>

