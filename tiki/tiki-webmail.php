<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-webmail.php,v 1.24 2004-05-27 21:58:17 rlpowell Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/webmail/webmaillib.php');

if ($feature_webmail != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_use_webmail != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

require_once ("lib/webmail/pop3.php");
require_once ("lib/webmail/mimeDecode.php");
include_once ("lib/webmail/class.rc4crypt.php");
include_once ("lib/webmail/htmlMimeMail.php");

function parse_output(&$obj, &$parts, $i) {
	if (!empty($obj->parts)) {
		for ($i = 0; $i < count($obj->parts); $i++)
			parse_output($obj->parts[$i], $parts, $i);
	} else {
		$ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

		switch ($ctype) {
		case 'text/plain':
			if (isset($obj->disposition)AND $obj->disposition == 'attachment') {
				$names = split(';', $obj->headers["content-disposition"]);

				$names = split('=', $names[1]);
				$aux['name'] = $names[1];
				$aux['content-type'] = $obj->headers["content-type"];
				$aux['part'] = $i;
				$parts['attachments'][] = $aux;
			} else {
				if(isset($obj->ctype_parameters) && strtolower($obj->ctype_parameters['charset']) == "iso-8859-1")
					$parts['text'][] = utf8_encode($obj->body);
				else if (isset($obj->ctype_parameters) && (strtolower($obj->ctype_parameters['charset']) != "utf-8" && function_exists('mb_convert_encoding')))
					$parts['text'][] = mb_convert_encoding($obj->body, "utf-8", $obj->ctype_parameters['charset']);
				else
					$parts['text'][] = $obj->body;
			}

			break;

		case 'text/html':
			if (isset($obj->disposition)AND $obj->disposition == 'attachment') {
				$names = split(';', $obj->headers["content-disposition"]);

				$names = split('=', $names[1]);
				$aux['name'] = $names[1];
				$aux['content-type'] = $obj->headers["content-type"];
				$aux['part'] = $i;
				$parts['attachments'][] = $aux;
			} else {
				if(isset($obj->ctype_parameters) && strtolower($obj->ctype_parameters['charset']) == "iso-8859-1")
					$parts['html'][] = utf8_encode($obj->body);
				else if (isset($obj->ctype_parameters) && (strtolower($obj->ctype_parameters['charset']) != "utf-8" && function_exists('mb_convert_encoding')))
					$parts['html'][] = mb_convert_encoding($obj->body, "utf-8", $obj->ctype_parameters['charset']);
				else
					$parts['html'][] = $obj->body;
			}

			break;
		default:
			$names = split(';', $obj->headers["content-disposition"]);

			$names = split('=', $names[1]);
			$aux['name'] = $names[1];
			$aux['content-type'] = $obj->headers["content-type"];
			$aux['part'] = $i;
			$parts['attachments'][] = $aux;
		}
	}
}
function decode_subject_utf8($string){
	if (ereg('=\?.*\?.*\?=', $string) === false)
		return $string;
	$string = explode('?', $string);
	$str = strtolower($string[2]) == 'q' ?quoted_printable_decode($string[3]):base64_decode($string[3]);
 	if (strtolower($string[1]) == "iso-8859-1")
		return utf8_encode($str);
	else if (strtolower($string[1]) == "utf-8")
		return $str;
	else if (function_exists('mb_convert_encoding'))
		return mb_convert_encoding($str, "utf-8", $string[1]);
	else
		return $str;
} 

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["section"])) {
	$_REQUEST["section"] = 'mailbox';
}

$smarty->assign('section', $_REQUEST["section"]);

// Search if we have to add some contacts
if (isset($_REQUEST["add_contacts"])) {
	if (isset($_REQUEST["add"])) {
		check_ticket('webmail');
		foreach (array_keys($_REQUEST["add"])as $i) {
			$webmaillib->replace_contact(0, $_REQUEST["addFirstName"][$i], $_REQUEST["addLastName"][$i], $_REQUEST["addemail"][$i],
				$_REQUEST["addNickname"][$i], $user);
		}
	}
}

/*************** Read an Email **************************************************************************************/
if ($_REQUEST["section"] == 'read') {
	if (isset($_REQUEST["fullheaders"])) {
		$smarty->assign('fullheaders', 'y');
	} else {
		$smarty->assign('fullheaders', 'n');
	}

	$current = $webmaillib->get_current_webmail_account($user);
	$smarty->assign('current', $current);
	$pop3 = new POP3($current["pop"], $current["username"], $current["pass"]);
	$pop3->Open();

	if (isset($_REQUEST["delete_one"])) {
		check_ticket('webmail');
		$realmsgid = $pop3->GetMessageID($_REQUEST["msgdel"]);

		$webmaillib->remove_webmail_message($current["accountId"], $user, $realmsgid);
		$pop3->DeleteMessage($_REQUEST["msgdel"]);
	}

	$message = $pop3->GetMessage($_REQUEST["msgid"]);
	$realmsgid = $pop3->GetMessageID($_REQUEST["msgid"]);
	$smarty->assign('msgid', $_REQUEST["msgid"]);
	$smarty->assign('realmsgid', $realmsgid);
	$webmaillib->set_mail_flag($current["accountId"], $user, $realmsgid, 'isRead', 'y');
	$s = $pop3->Stats();
	$mailsum = $s["message"];
	$numshow = $current["msgs"];

	if ($_REQUEST["msgid"] == $mailsum) {
		$smarty->assign('next', '');
	} else {
		$smarty->assign('next', $_REQUEST["msgid"] + 1);
	}

	if ($_REQUEST["msgid"] > 1) {
		$smarty->assign('prev', $_REQUEST["msgid"] - 1);
	} else {
		$smarty->assign('prev', '');
	}

	$body = $message["body"];
	$header = $message["header"];
	$full = $message["full"];
	$pop3->Close();
	$params = array(
		'input' => $full,
		'crlf' => "\r\n",
		'include_bodies' => TRUE,
		'decode_headers' => TRUE,
		'decode_bodies' => TRUE
	);

	$output = Mail_mimeDecode::decode($params);
	parse_output($output, $parts, 0);

	if (isset($parts["html"])) {
		$bodies = $parts["html"];
	} else {
		$bodies = $parts["text"];
	}

	for ($i = 0; $i < count($bodies); $i++) {
		$bodies[$i] = strip_tags(
			$bodies[$i], "<a><b><i><table><tr><td><th><ul><li><img><hr><ol><br /><h1><h2><h3><h4><h5><h6><div><span><font><form><input><textarea><checkbox><select>");
	}

	if (isset($parts["attachments"])) {
		$attachs = $parts["attachments"];
	} else {
		$attachs = array();
	}

	$smarty->assign('attachs', $attachs);
	$smarty->assign('bodies', $bodies);
	$allbodies = join("\n", $bodies);
	$allbodies = "\n\n------------------------------------------\n" . $allbodies;
	$smarty->assign('allbodies', htmlspecialchars($allbodies));

	$to_addresses = $output->headers["from"];

	// Get email addresses from the "from" portion
	$to_addresses = split(',', $to_addresses);

	for ($i = 0; $i < count($to_addresses); $i++) {
		preg_match("/<([^>]+)>/", $to_addresses[$i], $add);

		if (isset($add[1])) {
			$to_addresses[$i] = $add[1];
		}
	}

	if (isset($output->headers["cc"]) || ereg(',', $output->headers["to"])) {
		$cc_addresses = "";

		if (isset($output->headers["cc"]))
			$cc_addresses .= $output->headers["cc"];

		//add addresses to cc from "to" field (for 'reply to all')
		if ($cc_addresses != "")
			$cc_addresses .= ",";

		$cc_addresses .= $output->headers["to"];
		$cc_addresses = split(',', $cc_addresses);

		for ($i = 0; $i < count($cc_addresses); $i++) {
			preg_match("/<([^>]+)>/", $cc_addresses[$i], $add);

			if (isset($add[1])) {
				$cc_addresses[$i] = $add[1];
			}
		}
	} else {
		$cc_addresses = array();
	}

	$to_addresses = join(',', $to_addresses);
	$cc_addresses = join(',', $cc_addresses);

	if (isset($output->headers["reply-to"])) {
		$output->headers["replyto"] = $output->headers["reply-to"];

		$output->headers["replycc"] = $cc_addresses;
	} else {
		$output->headers["replycc"] = $cc_addresses;

		$output->headers["replyto"] = $to_addresses;
	}

	$smarty->assign('headers', $output->headers);
//print_r($output->headers);
}

/*************** Mailbox *******************************************************************************************/
if ($_REQUEST["section"] == 'mailbox') {
	$h = opendir("temp/mail_attachs/");

	while ($file = readdir($h)) {
		if (substr($file, 0, strlen($user)) == $user) {
			@unlink ('temp/mail_attachs/' . $file);
		}
	}

	closedir ($h);

	$current = $webmaillib->get_current_webmail_account($user);

	if (!$current) {
		header ("location: tiki-webmail.php?section=settings");

		die;
	}

	$smarty->assign('current', $current);
	// Now get messages from mailbox
	$pop3 = new POP3($current["pop"], $current["username"], $current["pass"]);

	$pop3->exit = false;    //new
	$pop3->Open();

	if ($pop3->has_error) { //new
		echo '<b><br /><center><a href="tiki-webmail.php?section=settings">Click here for settings.</a></center></b>';

		die;
	}

	if (isset($_REQUEST["delete"])) {
		if (isset($_REQUEST["msg"])) {
			check_ticket('webmail');
			// Now we can delete the messages
			foreach (array_keys($_REQUEST["msg"])as $msg) {
				$realmsgid = $pop3->GetMessageID($msg);

				$webmaillib->remove_webmail_message($current["accountId"], $user, $realmsgid);
				$pop3->DeleteMessage($msg);
			}
		}
	}

	if (isset($_REQUEST["delete_one"])) {
		check_ticket('webmail');
		$realmsgid = $pop3->GetMessageID($_REQUEST["msgdel"]);

		$webmaillib->remove_webmail_message($current["accountId"], $user, $realmsgid);
		$pop3->DeleteMessage($_REQUEST["msgdel"]);
	}

	// Now delete the messages and reopen the mailbox to renumber messages
	$pop3->close();

	$pop3->Open();
	$s = $pop3->Stats();
	$mailsum = $s["message"];

	if (isset($_REQUEST["operate"])) {
		if (isset($_REQUEST["msg"])) {
			check_ticket('webmail');
			// Now we can operate the messages
			foreach (array_keys($_REQUEST["msg"])as $msg) {
				$realmsg = $_REQUEST["realmsg"][$msg];

				switch ($_REQUEST["action"]) {
				case "flag":
					$webmaillib->set_mail_flag($current["accountId"], $user, $realmsg, 'isFlagged', 'y');

					break;

				case "unflag":
					$webmaillib->set_mail_flag($current["accountId"], $user, $realmsg, 'isFlagged', 'n');

					break;

				case "read":
					$webmaillib->set_mail_flag($current["accountId"], $user, $realmsg, 'isRead', 'y');

					break;

				case "unread":
					$webmaillib->set_mail_flag($current["accountId"], $user, $realmsg, 'isRead', 'n');

					break;
				}
			}
		}
	}

	$s = $pop3->Stats();
	$mailsum = $s["message"];
	$numshow = $current["msgs"];

	if (isset($_REQUEST["start"]) && $_REQUEST["start"] > $mailsum)
		$_REQUEST["start"] = $mailsum;

	if (!isset($_REQUEST["filter"]))
		$smarty->assign('filter', '');
	else
		$smarty->assign('filter', $_REQUEST["filter"]);

	// If we have a filter then we have to
	if (isset($_REQUEST["filter"])) {
		$tot = 0;

		$aux["msgid"] = 'foo';
		$filtered = array();
		$filtered[] = $aux;

		for ($i = 1; $i <= $mailsum; $i++) {
			$aux = $pop3->ListMessage($i);
			$aux["subject"] = decode_subject_utf8($aux["subject"]);
			$aux["msgid"] = $i;
			$aux["realmsgid"] = $pop3->GetMessageID($i);
			$webmaillib->replace_webmail_message($current["accountId"], $user, $aux["realmsgid"]);
			list($aux["isRead"], $aux["isFlagged"], $aux["isReplied"])
				= $webmaillib->get_mail_flags($current["accountId"], $user, $aux["realmsgid"]);

			if (empty($aux["sender"]["name"]))
				$aux["sender"]["name"] = $aux["sender"]["email"];

			if (!strstr($aux["sender"]["name"], ' '))
				$aux["sender"]["name"] = substr($aux["sender"]["name"], 0, 25);

			$aux["subject"] = htmlspecialchars($aux["subject"]);

			if ($_REQUEST["filter"] == 'unread' && $aux["isRead"] == 'n') {
				$tot++;

				$filtered[] = $aux;
			} elseif ($_REQUEST["filter"] == 'flagged' && $aux["isFlagged"] == 'y') {
				$tot++;

				$filtered[] = $aux;
			}
		}

		$mailsum = count($filtered) - 1;
	}

	if (!isset($_REQUEST["start"]))
		$_REQUEST["start"] = $mailsum;

	$upperlimit = $_REQUEST["start"];
	$smarty->assign('start', $_REQUEST["start"]);
	$list = array();

	for ($i = $upperlimit; $i > 0 && count($list) < $numshow; $i--) {
		if (isset($_REQUEST["filter"])) {
			$aux = $filtered[$i];
		} else {
			$aux = $pop3->ListMessage($i);
			$aux["subject"] = decode_subject_utf8($aux["subject"] );
			
			//print_r($aux);print("<br />");
			$aux["realmsgid"] = $pop3->GetMessageID($i);
			$webmaillib->replace_webmail_message($current["accountId"], $user, $aux["realmsgid"]);
			list($aux["isRead"], $aux["isFlagged"], $aux["isReplied"]) = $webmaillib->get_mail_flags($current["accountId"], $user, $aux["realmsgid"]);

			if (empty($aux["sender"]["name"]))
				$aux["sender"]["name"] = $aux["sender"]["email"];

			if (!strstr($aux["sender"]["name"], ' '))
				$aux["sender"]["name"] = substr($aux["sender"]["name"], 0, 25);

			$aux["sender"]["name"] = htmlspecialchars($aux["sender"]["name"]);

			if (empty($aux["subject"])) {
				$aux["subject"] = '[' . tra('No subject'). ']';
			}

			$aux["subject"] = htmlspecialchars($aux["subject"]);
		}

		$aux["msgid"] = $i;
		$list[] = $aux;
	}

	$lowerlimit = $i;

	if ($lowerlimit < 0)
		$lowerlimit = 0;

	$showstart = $mailsum - $upperlimit + 1;
	$showend = $mailsum - $lowerlimit;
	$smarty->assign('showstart', $showstart);
	$smarty->assign('showend', $showend);
	$smarty->assign('total', $mailsum);

	if ($lowerlimit > 0) {
		$smarty->assign('nextstart', $lowerlimit);
	} else {
		$smarty->assign('nextstart', '');
	}

	if ($upperlimit <> $mailsum) {
		$prevstart = $upperlimit + $numshow;

		if ($prevstart > $mailsum)
			$prevstart = $mailsum;

		$smarty->assign('prevstart', $prevstart);
	} else {
		$smarty->assign('prevstart', '');
	}

	if ($_REQUEST["start"] <> $mailsum) {
		$smarty->assign('first', $mailsum);
	} else {
		$smarty->assign('first', '');
	}

	// Now calculate the last message block
	$last = $mailsum % $numshow;

	if ($_REQUEST["start"] <> $last) {
		$smarty->assign('last', $last);
	} else {
		$smarty->assign('last', '');
	}

	$pop3->Close();
	$smarty->assign('list', $list);
}

/******************** Settings **************************************************************************************/
if ($_REQUEST["section"] == 'settings') {
	// Add a new mail account for the user here
	if (!isset($_REQUEST["accountId"]))
		$_REQUEST["accountId"] = 0;

	$smarty->assign('accountId', $_REQUEST["accountId"]);

	if (isset($_REQUEST["new_acc"])) {
		check_ticket('webmail');
		$webmaillib->replace_webmail_account($_REQUEST["accountId"], $user, $_REQUEST["account"], $_REQUEST["pop"], $_REQUEST["port"], $_REQUEST["username"], $_REQUEST["pass"], $_REQUEST["msgs"], $_REQUEST["smtp"], $_REQUEST["useAuth"], $_REQUEST["smtpPort"]);

		$_REQUEST["accountId"] = 0;
	}

	if (isset($_REQUEST["remove"])) {
		check_ticket('webmail');
		$webmaillib->remove_webmail_account($user, $_REQUEST["remove"]);
	}

	if (isset($_REQUEST["current"])) {
		$webmaillib->current_webmail_account($user, $_REQUEST["current"]);
	}

	if ($_REQUEST["accountId"]) {
		$info = $webmaillib->get_webmail_account($user, $_REQUEST["accountId"]);
	} else {
		$info["account"] = '';

		$info["username"] = '';
		$info["pass"] = '';
		$info["pop"] = '';
		$info["smtp"] = '';
		$info["useAuth"] = 'n';
		$info["port"] = 110;
		$info["smtpPort"] = 25;
		$info["msgs"] = 20;
	}

	$smarty->assign('info', $info);
	// List
	$accounts = $webmaillib->list_webmail_accounts($user, 0, -1, 'account_asc', '');
	$smarty->assign('accounts', $accounts["data"]);
}

/*************** Compose *********************************************************************************************/
if ($_REQUEST["section"] == 'compose') {
	$current = $webmaillib->get_current_webmail_account($user);

	if (!$current) {
		header ("location: tiki-webmail.php?section=settings");

		die;
	}

	// Send a message
	if (isset($_REQUEST["reply"]) || isset($_REQUEST["replyall"])) {
		check_ticket('webmail');
		$webmaillib->set_mail_flag($current["accountId"], $user, $_REQUEST["realmsgid"], 'isReplied', 'y');
	}

	$smarty->assign('sent', 'n');
	$smarty->assign('attaching', 'n');

	if (isset($_REQUEST["send"])) {
		$mail = new htmlMimeMail();

		$mail->setFrom($current["account"]);
		$mail->setCc($_REQUEST["cc"]);
		$mail->setBcc($_REQUEST["bcc"]);
		$mail->setSubject($_REQUEST["subject"]);

		if ($_REQUEST["attach1"]) {
			check_ticket('webmail');
			$a1 = $mail->getFile('temp/mail_attachs/' . $_REQUEST["attach1file"]);

			$mail->addAttachment($a1, $_REQUEST["attach1"], $_REQUEST["attach1type"]);
			@unlink ('temp/mail_attachs/' . $_REQUEST["attach1file"]);
		}

		if ($_REQUEST["attach2"]) {
			check_ticket('webmail');
			$a2 = $mail->getFile('temp/mail_attachs/' . $_REQUEST["attach2file"]);

			$mail->addAttachment($a2, $_REQUEST["attach2"], $_REQUEST["attach2type"]);
			@unlink ('temp/mail_attachs/' . $_REQUEST["attach2file"]);
		}

		if ($_REQUEST["attach3"]) {
			check_ticket('webmail');
			$a3 = $mail->getFile('temp/mail_attachs/' . $_REQUEST["attach3file"]);

			$mail->addAttachment($a3, $_REQUEST["attach3"], $_REQUEST["attach3type"]);
			@unlink ('temp/mail_attachs/' . $_REQUEST["attach3file"]);
		}

		$mail->setSMTPParams($current["smtp"], $current["smtpPort"], '', $current["useAuth"], $current["username"], $current["pass"]);

		if (isset($_REQUEST["useHTML"]) && $_REQUEST["useHTML"] == 'on') {
			$mail->setHTML($_REQUEST["body"], strip_tags($_REQUEST["body"]));
		} else {
			$mail->setText($_REQUEST["body"]);
		}

		$to_array_1 = split('[, ;]', $_REQUEST["to"]);
		$to_array = array();

		foreach ($to_array_1 as $to_1) {
			if (!empty($to_1)) {
				$to_array[] = $to_1;
			}
		}

		$to_array = $webmaillib->parse_nicknames($to_array);

		// Get email addresses not in the address book
		$not_contacts = $webmaillib->are_contacts($to_array, $user);

		if (count($not_contacts) > 0) {
			$smarty->assign('notcon', 'y');
		} else {
			$smarty->assign('notcon', 'n');
		}

		//print_r($not_contacts);
		$smarty->assign('not_contacts', $not_contacts);

		if ($mail->send($to_array,'smtp')) {
			$msg=tra('Your email was sent');
		} else {
			if (is_array($mail->errors)) {
				$msg = "";
				for ($i = 0; $i < count($mail->errors); $i ++) {
					$msg .= $mail->errors[$i]."<br />";
				}
			} else {
				$msg=$mail->errors;
			}
		}

		$smarty->assign('sent', 'y');
		$smarty->assign('msg', $msg);
	}

	if (isset($_REQUEST["attach"])) {
		$smarty->assign('attaching', 'y');
	}

	if (isset($_REQUEST["remove_attach1"])) {
		check_ticket('webmail');
		@unlink ($_REQUEST["attach1file"]);

		$_REQUEST["attach1"] = '';
		$_REQUEST["attach1file"] = '';
		$_REQUEST["attach1type"] = '';
	}

	if (isset($_REQUEST["remove_attach2"])) {
		check_ticket('webmail');
		@unlink ($_REQUEST["attach2file"]);

		$_REQUEST["attach2"] = '';
		$_REQUEST["attach2file"] = '';
		$_REQUEST["attach2type"] = '';
	}

	if (isset($_REQUEST["remove_attach3"])) {
		check_ticket('webmail');
		@unlink ($_REQUEST["attach3file"]);

		$_REQUEST["attach3"] = '';
		$_REQUEST["attach3file"] = '';
		$_REQUEST["attach3type"] = '';
	}

	if (isset($_REQUEST["attached"])) {
		// Now process the uploads
		if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			check_ticket('webmail');
			$size = $_FILES['userfile1']['size'];

			if ($size < 1500000) {
				$name = $_FILES['userfile1']['name'];

				$type = $_FILES['userfile1']['type'];
				$_REQUEST["attach1file"] = $user . md5($webmaillib->genPass());
				$_REQUEST["attach1type"] = $type;
				$_REQUEST["attach1"] = $name;
				move_uploaded_file($_FILES['userfile1']['tmp_name'], 'temp/mail_attachs/' . $_REQUEST["attach1file"]);
			}
		}

		if (isset($_FILES['userfile2']) && is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
			check_ticket('webmail');
			$size = $_FILES['userfile2']['size'];

			if ($size < 1500000) {
				$name = $_FILES['userfile2']['name'];

				$type = $_FILES['userfile2']['type'];
				$_REQUEST["attach2file"] = $user . md5($webmaillib->genPass());
				$_REQUEST["attach2type"] = $type;
				$_REQUEST["attach2"] = $name;
				move_uploaded_file($_FILES['userfile2']['tmp_name'], 'temp/mail_attachs/' . $_REQUEST["attach2file"]);
			}
		}

		if (isset($_FILES['userfile3']) && is_uploaded_file($_FILES['userfile3']['tmp_name'])) {
			check_ticket('webmail');
			$size = $_FILES['userfile3']['size'];

			if ($size < 1500000) {
				$name = $_FILES['userfile3']['name'];

				$type = $_FILES['userfile3']['type'];
				$_REQUEST["attach3file"] = $user . md5($webmaillib->genPass());
				$_REQUEST["attach3type"] = $type;
				$_REQUEST["attach3"] = $name;
				move_uploaded_file($_FILES['userfile3']['tmp_name'], 'temp/mail_attachs/' . $_REQUEST["attach3file"]);
			}
		}
	}

	// Build the to array
	if (!isset($_REQUEST["attach1"]))
		$_REQUEST["attach1"] = '';

	if (!isset($_REQUEST["attach2"]))
		$_REQUEST["attach2"] = '';

	if (!isset($_REQUEST["attach3"]))
		$_REQUEST["attach3"] = '';

	if (!isset($_REQUEST["attach1file"]))
		$_REQUEST["attach1file"] = '';

	if (!isset($_REQUEST["attach2file"]))
		$_REQUEST["attach2file"] = '';

	if (!isset($_REQUEST["attach3file"]))
		$_REQUEST["attach3file"] = '';

	if (!isset($_REQUEST["attach1type"]))
		$_REQUEST["attach1type"] = '';

	if (!isset($_REQUEST["attach2type"]))
		$_REQUEST["attach2type"] = '';

	if (!isset($_REQUEST["attach3type"]))
		$_REQUEST["attach3type"] = '';

	if (!isset($_REQUEST["to"]))
		$_REQUEST["to"] = '';

	if (!isset($_REQUEST["cc"]))
		$_REQUEST["cc"] = '';

	if (!isset($_REQUEST["bcc"]))
		$_REQUEST["bcc"] = '';

	if (!isset($_REQUEST["body"]))
		$_REQUEST["body"] = '';

	if (!isset($_REQUEST["subject"]))
		$_REQUEST["subject"] = '';

	$smarty->assign('cc', $_REQUEST["cc"]);
	$smarty->assign('to', $_REQUEST["to"]);
	$smarty->assign('bcc', $_REQUEST["bcc"]);
	$smarty->assign('body', $_REQUEST["body"]);
	$smarty->assign('subject', $_REQUEST["subject"]);
	$smarty->assign('attach1', $_REQUEST["attach1"]);
	$smarty->assign('attach2', $_REQUEST["attach2"]);
	$smarty->assign('attach3', $_REQUEST["attach3"]);
	$smarty->assign('attach1file', $_REQUEST["attach1file"]);
	$smarty->assign('attach2file', $_REQUEST["attach2file"]);
	$smarty->assign('attach3file', $_REQUEST["attach3file"]);
	$smarty->assign('attach1type', $_REQUEST["attach1type"]);
	$smarty->assign('attach2type', $_REQUEST["attach2type"]);
	$smarty->assign('attach3type', $_REQUEST["attach3type"]);
}

/******************** Contacts **************************************************************************************/
if ($_REQUEST["section"] == 'contacts') {
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
		check_ticket('webmail');
		$webmaillib->remove_contact($_REQUEST["remove"], $user);
	}

	if (isset($_REQUEST["save"])) {
		check_ticket('webmail');
		$webmaillib->replace_contact($_REQUEST["contactId"], $_REQUEST["firstName"], $_REQUEST["lastName"], $_REQUEST["email"], $_REQUEST["nickname"], $user);

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
}

include_once ('tiki-mytiki_shared.php');

$section = 'webmail';
include_once ('tiki-section_options.php');

ask_ticket('webmail');

$smarty->assign('mid', 'tiki-webmail.tpl');
$smarty->display("tiki.tpl");

?>
