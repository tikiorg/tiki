<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		$mail = new TikiMail;
		$to = array();

		if ($replyto = $data->replyto->email()) {
			$mail->setReplyTo($replyto);
		}

		foreach ($data->to->email() as $email) {
			$to[] = $this->stripNp($email);
		}

		foreach ($data->cc->email() as $cc) {
			$mail->setCc($this->stripNp($cc));
		}

		foreach ($data->bcc->email() as $bcc) {
			$mail->setBcc($this->stripNp($bcc));
		}

		$content = $this->parse($data->content->none());
		$subject = $this->parse($data->subject->text());

		$mail->setSubject(strip_tags($subject));
		$mail->setHtml($content);

		return $mail->send($to);
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

