<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-send_newsletters.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/newsletters/nllib.php');
include_once ('lib/webmail/htmlMimeMail.php');

if ($feature_newsletters != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_admin_newsletters != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["nlId"]))
	$_REQUEST["nlId"] = 0;

$smarty->assign('nlId', $_REQUEST["nlId"]);

$newsletters = $nllib->list_newsletters(0, -1, 'created_desc', '');
$smarty->assign('newsletters', $newsletters["data"]);

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
	$nllib->remove_edition($_REQUEST["remove"]);
}

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["data"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');

	//$parsed = $tikilib->parse_data($_REQUEST["content"]);
	$parsed = $_REQUEST["data"];
	$smarty->assign('parsed', $parsed);
	$info["data"] = $_REQUEST["data"];
	$info["subject"] = $_REQUEST["subject"];
	$smarty->assign('info', $info);
}

$smarty->assign('presend', 'n');

if (isset($_REQUEST["save"])) {
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
	$subscribers = $nllib->get_subscribers($_REQUEST["nlId"]);

	$mail = new htmlMimeMail();
	$mail->setFrom('noreply@noreply.com');
	$mail->setSubject($_REQUEST["subject"]);
	$sent = 0;

	foreach ($subscribers as $email) {
		$to_array = array();

		$to_array[] = $email;
		$unsubmsg = $nllib->get_unsub_msg($_REQUEST["nlId"], $email);
		$mail->setHeadCharset("utf-8");
		$mail->setTextCharset("utf-8");
		$mail->setHtmlCharset("utf-8");
		$mail->setFrom($sender_email);
		$mail->setHTML($_REQUEST["data"] . $unsubmsg, strip_tags($_REQUEST["data"]));

		if ($mail->send($to_array, 'mail'))
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

$smarty->assign_by_ref('templates', $templates["data"]);

// Display the template
$smarty->assign('mid', 'tiki-send_newsletters.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>

?>