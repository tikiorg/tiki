<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
			'to+' => true,
			'cc+' => false,
			'bcc+' => false,
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

			foreach ($data->to->email() as $to) {
				$mail->addTo($this->stripNp($to));
			}

			foreach ($data->cc->email() as $cc) {
				$mail->addCc($this->stripNp($cc));
			}

			foreach ($data->bcc->email() as $bcc) {
				$mail->addBcc($this->stripNp($bcc));
			}

			$content = $this->parse($data->content->none());
			$subject = $this->parse($data->subject->text());

			$mail->setSubject(strip_tags($subject));

			$bodyPart = new \Zend\Mime\Message();
			$bodyMessage = new \Zend\Mime\Part($content);
			$bodyMessage->type = \Zend\Mime\Mime::TYPE_HTML;
			$bodyPart->setParts(array($bodyMessage));

			$mail->setBody($bodyPart);

			tiki_send_email($mail);

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	private function parse($content)
	{
		$content = "~np~$content~/np~";

		$parserlib = TikiLib::lib('parser');

		$options = array(
			'protect_email' => false,
		);

		return trim($parserlib->parse_data($content, $options));
	}

	private function stripNp($content)
	{
		return str_replace(array('~np~', '~/np~'), '', $content);
	}
}

