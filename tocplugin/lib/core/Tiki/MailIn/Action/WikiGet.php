<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Action;
use Tiki\MailIn\Account;
use Tiki\MailIn\Source\Message;
use TikiLib;

class WikiGet extends WikiPut
{
	function getName()
	{
		return tr('Wiki Get');
	}

	function isAllowed(Account $account, Message $message)
	{
		$user = $message->getAssociatedUser();
		$page = $this->getPage($message);
		$perms = TikiLib::lib('tiki')->get_user_permission_accessor($user, 'wiki page', $page);

		return $perms->view;
	}

	function execute(Account $account, Message $message)
	{
		$tikilib = TikiLib::lib('tiki');
		$page = $this->getPage($message);
		$info = $tikilib->get_page_info($page);

		if ($info) {
			$data = $tikilib->parse_data($info["data"]);

			$mail = $account->getReplyMail($message);
			$mail->setSubject($page);
			$mail->addAttachment($info['data'], 'source.txt', 'plain/txt');
			$mail->setHTML($data, strip_tags($data));

			$account->sendReply($message, $mail);
		} else {
			$l = $prefs['language'];
			$mail_data = $smarty->fetchLang($l, "mail/mailin_reply_subject.tpl");

			$mail = $account->getReplyMail($message);
			$mail->setSubject($mail_data . $page);
			$account->sendReply($message, $mail);
		}

		return true;
	}
}

