<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

function tiki_mail_setup()
{
	static $done = false;
	if ( $done ) {
		return;
	}

	global $tiki_maillib__zend_mail_default_transport;
	global $prefs;
	if ( $prefs['zend_mail_handler'] == 'smtp' ) {
		$options = array(
			'host' => $prefs['zend_mail_smtp_server']
		);

		if ( $prefs['zend_mail_smtp_auth'] ) {
			$options['connection_class'] = $prefs['zend_mail_smtp_auth'];
			$options['connection_config'] = array(
				'username' => $prefs['zend_mail_smtp_user'],
				'password' => $prefs['zend_mail_smtp_pass']
			);
		}

		if ( $prefs['zend_mail_smtp_port'] ) {
			$options['port'] = $prefs['zend_mail_smtp_port'];
		}

		if ( $prefs['zend_mail_smtp_security'] ) {
			$options['connection_config']['ssl'] = $prefs['zend_mail_smtp_security'];
		}

		if ( $prefs['zend_mail_smtp_helo'] ) {
			$options['name'] = $prefs['zend_mail_smtp_helo'];
		}

		if ($prefs['openpgp_gpg_pgpmimemail'] == 'y') {
			$transport = new OpenPGP_Zend_Mail_Transport_Smtp();
		} else {
			$transport = new Zend\Mail\Transport\Smtp();
		}
		$transportOptions = new Zend\Mail\Transport\SmtpOptions($options);
		$transport->setOptions($transportOptions);
	} elseif ($prefs['zend_mail_handler'] == 'file') {
		$transport = new Zend\Mail\Transport\File();
		$transportOptions = new Zend\Mail\Transport\FileOptions(
			array(
				'path' => TIKI_PATH . '/temp',
				'callback' => function ($transport) {
					return 'Mail_' . date('YmdHis') . '_' . mt_rand() . '.tmp';
				},
			)
		);
		$transport->setOptions($transportOptions);
	} elseif ($prefs['zend_mail_handler'] == 'sendmail' && ! empty($prefs['sender_email'])) {
		// from http://framework.zend.com/manual/1.12/en/zend.mail.introduction.html#zend.mail.introduction.sendmail
		$transport = new Zend\Mail\Transport\Sendmail('-f' . $prefs['sender_email']);
	} else {
		$transport = new Zend\Mail\Transport\Sendmail();
	}

	$tiki_maillib__zend_mail_default_transport = $transport;

	$done = true;
}

/**
 * @return Zend\Mail\Message
 */
function tiki_get_basic_mail()
{
	tiki_mail_setup();
	$mail = new Zend\Mail\Message();
	$mail->setEncoding('UTF-8');
	$mail->getHeaders()->addHeaderLine('X-Tiki', 'yes');
	return $mail;
}

/**
 * @return Zend\Mail\Message
 */
function tiki_get_admin_mail()
{
	global $prefs;

	$mail = tiki_get_basic_mail();

	if (! empty($prefs['sender_email'])) {
		// [BUG FIX] hollmeer 2012-11-04:
		// Added returnpath for Sendmail; does not send without;
		// catch/ignore error, if already set
		try {
			$mail->setFrom($prefs['sender_email']);
			$mail->setSender($prefs['sender_email']);
		} catch (Exception $e) {
			// was already set, then do nothing
		}
	}

	return $mail;
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

	tiki_send_email($mail);
}

function tiki_send_email($email)
{
	tiki_mail_setup();

	/* @var $tiki_maillib__zend_mail_default_transport Zend\Mail\Transport\TransportInterface */
	global $tiki_maillib__zend_mail_default_transport;

	$tiki_maillib__zend_mail_default_transport->send($email);
}
