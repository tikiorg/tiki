<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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

function encode_headers($in_str, $charset) {
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

function tiki_mail_setup() {
	static $done = false;
	if( $done ) {
		return;
	}

	require_once 'lib/core/Zend/Mail.php';

	global $prefs;
	if( $prefs['zend_mail_handler'] == 'smtp' ) {
		require_once 'lib/core/Zend/Mail/Transport/Smtp.php';
		$options = array();

		if( $prefs['zend_mail_smtp_auth'] ) {
			$options['auth'] = $prefs['zend_mail_smtp_auth'];
			$options['username'] = $prefs['zend_mail_smtp_user'];
			$options['password'] = $prefs['zend_mail_smtp_pass'];
		}

		if( $prefs['zend_mail_smtp_port'] ) {
			$options['port'] = $prefs['zend_mail_smtp_port'];
		}

		if( $prefs['zend_mail_smtp_security'] ) {
			$options['ssl'] = $prefs['zend_mail_smtp_security'];
		}

		$transport = new Zend_Mail_Transport_Smtp( $prefs['zend_mail_smtp_server'], $options );
		Zend_Mail::setDefaultTransport( $transport );
	}

	$done = true;
}

function tiki_get_basic_mail() {
	tiki_mail_setup();

	return new Zend_Mail('UTF-8');
}

function tiki_get_admin_mail() {
	global $prefs;

	$mail = tiki_get_basic_mail();
	$mail->setFrom( $prefs['sender_email'], $prefs['browsertitle'] );

	return $mail;
}

function tiki_send_admin_mail( $email, $recipientName, $subject, $textBody ) {
	$mail = tiki_get_admin_mail();

	$mail->addTo( $email, $recipientName );

	$mail->setSubject( $subject );
	$mail->setBodyText( $textBody );

	$mail->send();
}
