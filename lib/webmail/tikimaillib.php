<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * set some default params (mainly utf8 as tiki is utf8) + use the mailCharset pref from a user
 */
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

class TikiMail
{
	private $mail;

	/**
	 * @param null $user	to username
	 * @param null $from	from email
	 */
	function __construct($user = null, $from=null)
	{
		require_once 'lib/mail/maillib.php';

		$userlib = TikiLib::lib('user');

		$to = '';
		if (!empty($user)) {
			if ($userlib->user_exists($user)) {
				$to = $userlib->get_user_email($user);
			} else {
				trigger_error('User not found');
				return;
			}
		}

		if (! empty($from)) {
			$this->mail = tiki_get_basic_mail();
			try {
				$this->mail->setFrom($from);
				$this->mail->setReturnPath($from);
			} catch (Exception $e) {
				// was already set, then do nothing
			}
		} else {
			$this->mail = tiki_get_admin_mail();
		}
		if (! empty($to)) {
			$this->mail->addTo($to);
		}
	}

	function setUser($user)
	{
	}

	function setFrom($email)
	{
		$this->mail->setFrom($email);
	}

	function setReplyTo($email)
	{
		$this->mail->setReplyTo($email);
	}

	function setSubject($subject)
	{
		$this->mail->setSubject($subject);
	}

	function setHtml($html, $text = null, $images_dir = null)
	{
		$this->mail->setBodyHtml($html);
		if ($text) {
			$this->mail->setBodyText($text);
		}
	}

	function setText($text = '')
	{
		$this->mail->setBodyText($text);
	}

	function setCc($address)
	{
		foreach ((array) $address as $cc) {
			$this->mail->addCc($cc);
		}
	}

	function setBcc($address)
	{
		foreach ((array) $address as $bcc) {
			$this->mail->addBcc($bcc);
		}
	}

	function setHeader($name, $value)
	{
		$this->mail->addHeader($name, $value);
	}

	function send($recipients, $type = 'mail')
	{
		global $prefs;
		$logslib = TikiLib::lib('logs');

		$this->mail->clearHeader('To');
		foreach ((array) $recipients as $to) {
			$this->mail->addTo($to);
		}

		try {
			$this->mail->send();

			$title = 'mail';
		} catch (Zend_Mail_Exception $e) {
			$title = 'mail error';
		}

		if ($title == 'mail error' || $prefs['log_mail'] == 'y') {
			foreach ($recipients as $u) {
				$logslib->add_log($title, $u . '/' . $this->mail->getSubject());
			}
		}
		return $title == 'mail';
	}

	function addAttachment($data, $filename, $mimetype)
	{
		$this->mail->createAttachment($data, $mimetype, Zend_Mime::DISPOSITION_INLINE, Zend_Mime::ENCODING_BASE64, $filename);
	}
}

/**
 * Format text, sender and date for a plain text email reply
 * - Split into 75 char long lines prepended with >
 *
 * @param $text		email text to be quoted
 * @param $from		email from name/address to be quoted
 * @param $date		date of mail to be quoted
 * @return string	text ready for replying in a plain text email
 */
function format_email_reply(&$text, $from, $date)
{
	$lines = preg_split('/[\n\r]+/', wordwrap($text));

	for ($i = 0, $icount_lines = count($lines); $i < $icount_lines; $i++) {
		$lines[$i] = '> ' . $lines[$i] . "\n";
	}
	$str = !empty($from) ? $from . ' wrote' : '';
	$str .= !empty($date) ? ' on ' . $date : '';
	$str = "\n\n\n" . $str . "\n" . implode($lines);

	return $str;
}

/**
 * Attempt to close any unclosed HTML tags
 * Needs to work with what's inside the BODY
 * originally from http://snipplr.com/view/3618/close-tags-in-a-htmlsnippet/
 *
 * @param $html			html input
 * @return string		corrected html out
 */
function closetags ( $html )
{
	#put all opened tags into an array
	preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $result);
	$openedtags = $result[1];

	#put all closed tags into an array
	preg_match_all("#</([a-z]+)>#iU", $html, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);

	# all tags are closed
	if ( count($closedtags) == $len_opened ) {
		return $html;
	}
	$openedtags = array_reverse($openedtags);

	# close tags
	for ( $i = 0; $i < $len_opened; $i++ ) {
		if ( !in_array($openedtags[$i], $closedtags)) {
			$html .= "</" . $openedtags[$i] . ">";
		} else {
			unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
	}
	return $html;
}

