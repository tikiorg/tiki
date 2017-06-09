<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		global $prefs;

		require_once 'lib/mail/maillib.php';

		try {
			$mail = tiki_get_admin_mail();

			if ($replyto = $this->dereference($data->replyto->text())) {
				$mail->setReplyTo($replyto);
			}

			foreach ($data->to->text() as $to) {
				if( $to = $this->dereference($to) ) {
					$mail->addTo($to);
				}
			}

			foreach ($data->cc->text() as $cc) {
				if( $cc = $this->dereference($cc) ) {
					$mail->addCc($cc);
				}
			}

			foreach ($data->bcc->text() as $bcc) {
				if( $bcc = $this->dereference($bcc) ) {
					$mail->addBcc($bcc);
				}
			}

			$content = $this->parse($data->content->none());
			$subject = $this->parse($data->subject->text());

			$mail->setSubject(strip_tags($subject));

			$bodyPart = new \Zend\Mime\Message();
			$bodyMessage = new \Zend\Mime\Part($content);
			$bodyMessage->type = \Zend\Mime\Mime::TYPE_HTML;
			if ($prefs['default_mail_charset']) {
				$bodyMessage->setCharset($prefs['default_mail_charset']);
			}

			$bodyPart->setParts(array($bodyMessage));

			$mail->setBody($bodyPart);

			tiki_send_email($mail);

			return true;
		} catch (Exception $e) {
			throw new Search_Action_Exception(tr('Error sending email: %0', $e->getMessage()));
		}
	}

	function requiresInput(JitFilter $data) {
		return false;
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

	private function dereference($email_or_username) {
		if( empty($email_or_username) ) {
			return null;
		}
		$email_or_username = $this->stripNp($email_or_username);
		if( strstr($email_or_username, '@') ) {
			return $email_or_username;
		} else {
			return TikiLib::lib('user')->get_user_email($email_or_username);
		}
	}
}

