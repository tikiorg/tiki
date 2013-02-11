<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Common shared mail functions */
/*
 * function encode_headers()
 *
 * Encode non-ASCII email headers for mail() function to display
 * them properly in email clients.
 * Original code by <gordon at kanazawa-gu dot ac dot jp>.
 * See 'User Contributed Notes' at
 * http://php.benscom.com/manual/en/function.mail.php
 * Rewritten for Tikiwiki by <luci at sh dot ground dot cz>
 *
 * For details on Message Header Extensions see
 * http://www.faqs.org/rfcs/rfc2047.html
 */

$charset = 'utf-8'; // What charset we do use in Tiki
$in_str = '';

class MailLib
{
	private $transport;

	function setUp()
	{
		if ( $this->transport ) {
			return;
		}

		global $prefs;
		if ( $prefs['zend_mail_handler'] == 'smtp' ) {
			$options = array(
				'name' => $prefs['zend_mail_smtp_server'],
				'host' => $prefs['zend_mail_smtp_server'],
			);

			if ( $prefs['zend_mail_smtp_auth'] ) {
				$options['connection_class'] = $prefs['zend_mail_smtp_auth'];
				$options['connection_config'] = array(
					'username' => $prefs['zend_mail_smtp_user'],
					'password' => $prefs['zend_mail_smtp_pass'],
				);
			}

			if ( $prefs['zend_mail_smtp_port'] ) {
				$options['port'] = $prefs['zend_mail_smtp_port'];
			}

			if ( $prefs['zend_mail_smtp_security'] ) {
				$options['connection_config']['ssl'] = $prefs['zend_mail_smtp_security'];
			}

			$options = new Zend\Mail\Transport\SmtpOptions($options);
			$this->transport = new Zend\Mail\Transport\Smtp($options);

			/* Disabled - needs re-implementation with ZF Mail 2
			// hollmeer 2012-11-03: ADDED PGP/MIME ENCRYPTION PREPARATION
			if ($prefs['openpgp_gpg_pgpmimemail'] == 'y') {
				// USE PGP/MIME MAIL VERSION
				$this->transport = new OpenPGP_Zend_Mail_Transport_Smtp($options);
			}
			*/
		} elseif ($prefs['zend_mail_handler'] == 'file') {
			$options = new Zend\Mail\Transport\FileOptions(array(
				'path' => 'temp',
				'callback' => function ($transport) {
					return 'Mail_' . date('YmdHis') . '_' . mt_rand() . '.tmp';
				},
			));
			$this->transport = new Zend\Mail\Transport\File($options);
		} else {
			$this->transport = new Zend\Mail\Transport\Sendmail;
		}
	}

	function createMessage()
	{
		$this->setUp();

		// hollmeer 2012-11-03: ADDED PGP/MIME ENCRYPTION PREPARATION
		// USING lib/openpgp/opepgplib.php
		global $prefs;
		if ($prefs['openpgp_gpg_pgpmimemail'] == 'y') {
			// USE PGP/MIME MAIL VERSION
			$mail = new OpenPGP_Zend_Mail;
		} else {
			// USE ORIGINAL TIKI MAIL VERSION
			$mail = new Zend\Mail\Message;
		}

		$mail->setEncoding('UTF-8');
		$mail->getHeaders()->addHeaderLine('X-Tiki', 'yes');
		return $mail;
	}

	function createAdminMessage()
	{
		global $prefs;

		$mail = $this->createMessage();

		if (! empty($prefs['sender_email'])) {
			// [BUG FIX] hollmeer 2012-11-04:
			// Added returnpath for Sendmail; does not send without;
			// catch/ignore error, if already set
			try {
				$mail->setFrom($prefs['sender_email']);
				$mail->setReplyTo($prefs['sender_email']);
			} catch (Exception $e) {
				// was already set, then do nothing
			}
		}

		return $mail;
	}

	function send(Zend\Mail\Message $message)
	{
		$this->setUp();

		return $this->transport->send($message);
	}
}

/**
 * @param $in_str
 * @param $charset
 * @return string
 */
function encode_headers($in_str, $charset)
{
   $out_str = $in_str;
   if ($out_str && $charset) {

       // define start delimimter, end delimiter and spacer
       $end = "?=";
       $start = "=?" . $charset . "?b?";
       $spacer = $end . "\r\n" . $start;

       // determine length of encoded text within chunks
       // and ensure length is even
       $length = 71 - strlen($spacer); // no idea why 71 but 75 didn't work
       $length = floor($length/2) * 2;

       // encode the string and split it into chunks
       // with spacers after each chunk
       $out_str = base64_encode($out_str);
       $out_str = chunk_split($out_str, $length, $spacer);

       // remove trailing spacer and
       // add start and end delimiters
       $spacer = preg_quote($spacer);
       $out_str = preg_replace("/" . $spacer . "$/", "", $out_str);
       $out_str = $start . $out_str . $end;
   }
   return $out_str;
}// end function encode_headers

/**
 * @return Zend_Mail
 */
function tiki_get_basic_mail()
{
	return TikiLib::lib('mail')->createMessage();
}

/**
 * @return Zend_Mail
 */
function tiki_get_admin_mail()
{
	return TikiLib::lib('mail')->createAdminMessage();
}

/**
 * @param $email
 * @param $recipientName
 * @param $subject
 * @param $textBody
 */
function tiki_send_admin_mail( $email, $recipientName, $subject, $textBody )
{
	$mail = tiki_get_admin_mail();

	$mail->addTo($email, $recipientName);

	$mail->setSubject($subject);
	$mail->setBody($textBody);

	TikiLib::lib('mail')->send($mail);
}
