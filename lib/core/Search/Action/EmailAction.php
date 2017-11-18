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
		return [
			'replyto' => false,
			'to+' => true,
			'cc+' => false,
			'bcc+' => false,
			'from' => false,
			'subject' => true,
			'content' => true,
			'is_html' => false,
			'pdf_page_attachment' => false,
		];
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
				$mail->setReplyTo($replyto[0]);
			}

			foreach ($data->to->text() as $to) {
				if ($to = $this->dereference($to)) {
					foreach ($to as $email) {
						$mail->addTo($email);
					}
				}
			}

			foreach ($data->cc->text() as $cc) {
				if ($cc = $this->dereference($cc)) {
					foreach ($cc as $email) {
						$mail->addCc($email);
					}
				}
			}

			foreach ($data->bcc->text() as $bcc) {
				if ($bcc = $this->dereference($bcc)) {
					foreach ($bcc as $email) {
						$mail->addBcc($email);
					}
				}
			}

			if ($from = $data->from->text()) {
				$fromEmail = $this->dereference($from);
				$fromName = $this->dereferenceName($from);
				if (! empty($fromEmail[0])) {
					$mail->setFrom($fromEmail[0], $fromName);
					$mail->setSender($fromEmail[0], $fromName);
				}
			}

			$content = $this->parse($data->content->none(), $data->is_html->boolean());
			$subject = $this->parse($data->subject->text());

			$mail->setSubject(strip_tags($subject));

			$bodyPart = new \Zend\Mime\Message();
			$bodyMessage = new \Zend\Mime\Part($content);
			$bodyMessage->type = \Zend\Mime\Mime::TYPE_HTML;
			if ($prefs['default_mail_charset']) {
				$bodyMessage->setCharset($prefs['default_mail_charset']);
			}

			$messageParts = [
				$bodyMessage
			];

			if (! empty($data->pdf_page_attachment->text())) {
				$pageName = $data->pdf_page_attachment->text();
				$fileName = $pageName . ".pdf";
				$pdfContent = $this->getPDFAttachment($pageName);

				if ($pdfContent) {
					$attachment = new \Zend\Mime\Part($pdfContent);
					$attachment->type = 'application/pdf';
					$attachment->filename = $fileName;
					$attachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
					$attachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;

					$messageParts[] = $attachment;
				} else {
					return false;
				}
			}

			$bodyPart->setParts($messageParts);
			$mail->setBody($bodyPart);

			tiki_send_email($mail);

			if ($prefs['log_mail'] == 'y') {
				$logslib = TikiLib::lib('logs');
				foreach ($data->to->text() as $email) {
					$logslib->add_log('mail', tr('EmailAction - send to %0, subject - %1', $email, $subject));
				}
			}

			return true;
		} catch (Exception $e) {
			if ($prefs['log_mail'] == 'y') {
				$logslib = TikiLib::lib('logs');
				foreach ($data->to->text() as $email) {
					$logslib->add_log('mail error', tr("EmailAction - can't send new message"));
				}
			}

			throw new Search_Action_Exception(tr('Error sending email: %0', $e->getMessage()));
		}
	}

	function requiresInput(JitFilter $data)
	{
		return false;
	}

	private function parse($content, $is_html = null)
	{
		$content = "~np~$content~/np~";

		$parserlib = TikiLib::lib('parser');

		$options = [
			'protect_email' => false,
		];

		if ($is_html) {
			$options['is_html'] = true;
		}

		return trim($parserlib->parse_data($content, $options));
	}

	private function stripNp($content)
	{
		return str_replace(['~np~', '~/np~'], '', $content);
	}

	private function dereference($email_or_username)
	{
		if (empty($email_or_username)) {
			return null;
		}
		$email_or_username = $this->stripNp($email_or_username);
		if (strstr($email_or_username, '@')) {
			return [$email_or_username];
		} else {
			$users = TikiLib::lib('trk')->parse_user_field($email_or_username);
			return array_map(function ($username) {
				return TikiLib::lib('user')->get_user_email($username);
			}, $users);
		}
	}

	private function dereferenceName($email_or_username)
	{
		if (empty($email_or_username)) {
			return null;
		}
		$email_or_username = $this->stripNp($email_or_username);
		if (strstr($email_or_username, '@')) {
			return null;
		} else {
			$users = TikiLib::lib('trk')->parse_user_field($email_or_username);
			if ($users) {
				return TikiLib::lib('user')->clean_user($users[0]);
			} else {
				return null;
			}
		}
	}

	private function getPDFAttachment($pageName)
	{

		if (! Perms::get('wiki page', $pageName)->view) {
			return [];
		}

		require_once('tiki-setup.php');
		require_once 'lib/pdflib.php';
		$generator = new PdfGenerator;
		if (! empty($generator->error)) {
			Feedback::error($generator->error);
			return false;
		} else {
			$params = [ 'page' => $pageName ];

			// If the page doesn't exist then display an error
			if (! ($info = TikiLib::lib('tiki')->get_page_info($pageName))) {
				Feedback::error(sprintf(tra('Page %s cannot be found'), $pageName));
				return false;
			}

			$pdata = TikiLib::lib('parser')->parse_data($info["data"], [
				'page' => $pageName,
				'is_html' => $info["is_html"],
				'print' => 'y',
				'namespace' => $info["namespace"]
			]);
			//replacing bootstrap classes for print version.
			$pdata = str_replace(['col-sm','col-md','col-lg'], 'col-xs', $pdata);

			return $generator->getPdf('tiki-print.php', $params, $pdata);
		}
	}
}
