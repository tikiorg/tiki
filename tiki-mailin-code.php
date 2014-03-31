<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\MailIn;

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
include_once ('lib/mailin/mailinlib.php');
include_once ("lib/mail/mimelib.php");
include_once ("lib/webmail/tikimaillib.php");
include_once ('lib/wiki/wikilib.php');

$mailinlib = TikiLib::lib('mailin');

// Get a list of ACTIVE emails accounts configured for mailin procedures
$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');

// foreach account
foreach ($accs['data'] as $acc) {
	if (empty($acc['account'])) {
		continue;
	}

	$account = MailIn\Account::fromDb($acc);
	$messages = $account->getMessages();

	foreach ($messages as $message) {
		$success = false;

		if (! $account->canReceive($message)) {
			$account->sendFailureResponse($message);
		} elseif ($action = $account->getAction($message)) {
			if (! $action->isEnabled()) {
				// Action configured, but not enabled
			} elseif ($account->isAnyoneAllowed() || $action->isAllowed($account, $message)) {
				$account->prepareMessage($message);
				$success = $action->execute($account, $message);
			} else {
				// TODO : Send permission denied message
			}
		} else {
			// Send failure response for no suitable action found
			$l = $prefs['language'];
			$subject = $smarty->fetchLang($l, "mail/mailin_help_subject.tpl");
			$smarty->assign('subject', $message->getSubject());
			$mail_data = $smarty->fetchLang($l, "mail/mailin_help.tpl");

			$mail = $account->getReplyMail($message);
			$mail->setSubject($subject);
			$mail->setText($mail_data);
			$account->sendFailureReply($message, $mail);
		}

		if ($success) {
			$account->completeSuccess($message);
		} else {
			$account->completeFailure($message);
		}
	}
}
