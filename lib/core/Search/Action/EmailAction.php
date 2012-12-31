<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_EmailAction implements Search_Action_Action
{
	function getValues()
	{
		return array(
			'replyto' => false,
			'to' => true,
			'cc' => false,
			'bcc' => false,
			'subject' => true,
			'content' => true,
		);
	}

	function validate(JitFilter $data)
	{
		return true;
	}

	function execute(JitFilter $data)
	{
		require_once 'lib/mail/maillib.php';

		try {
			$mail = tiki_get_admin_mail();

			if ($replyto = $data->replyto->email()) {
				$mail->setReplyTo($replyto);
			}

			$mail->addTo($data->to->email());

			if ($cc = $data->cc->email()) {
				$mail->addCc($cc);
			}

			if ($bcc = $data->bcc->email()) {
				$mail->addBcc($bcc);
			}

			$content = $this->parse($data->content->none());
			$subject = $this->parse($data->subject->text());

			$mail->setSubject(strip_tags($subject));
			$mail->setBodyHtml($content);

			$mail->send();

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	private function parse($content)
	{
		$content = "~np~$content~/np~";

		$parserlib = TikiLib::lib('parser');

		return trim($parserlib->parse_data($content));
	}
}

