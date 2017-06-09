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
			'pdf_page_attachment' => false,
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

			$messageParts = array(
				$bodyMessage
			);

			if (!empty($data->pdf_page_attachment->text())) {

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

	private function getPDFAttachment($pageName) {

		if (! Perms::get('wiki page', $pageName)->view) {
			return array();
		}

		require_once ('tiki-setup.php');
		require_once 'lib/pdflib.php';
		$generator = new PdfGenerator;
		if (!empty($generator->error)) {
			Feedback::error($generator->error);
			return false;
		} else {
			$params = array( 'page' => $pageName );

			// If the page doesn't exist then display an error
			if (!($info = TikiLib::lib('tiki')->get_page_info($pageName))) {
				Feedback::error(sprintf(tra('Page %s cannot be found'), $pageName));
				return false;
			}

			$pdata = TikiLib::lib('parser')->parse_data($info["data"], array(
				'page' => $pageName,
				'is_html' => $info["is_html"],
				'print' => 'y',
				'namespace' => $info["namespace"]
			));
			//replacing bootstrap classes for print version.
			$pdata = str_replace(array('col-sm','col-md','col-lg'),'col-xs',$pdata);

			return $generator->getPdf('tiki-print.php', $params, $pdata);
		}
	}
}

