<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * set some default params (mainly utf8 as tiki is utf8) + use the mailCharset pref from a user
 */
$access = TikiLib::lib('access');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

class TikiMail
{
	/**
	 * @var \Zend\Mail\Message
	 */
	private $mail;
	public $errors;

	/**
	 * @param null $user	to username
	 * @param null $from	from email
	 */
	function __construct($user = null, $from=null)
	{
		require_once 'lib/mail/maillib.php';

		$userlib = TikiLib::lib('user');

		$to = '';
		$this->errors = [];
		if (!empty($user)) {
			if ($userlib->user_exists($user)) {
				$to = $userlib->get_user_email($user);
			} else {
				$str = tra('Mail to: User not found');
				trigger_error($str);
				$this->errors = [$str];
				return;
			}
		}

		if (! empty($from)) {
			$this->mail = tiki_get_basic_mail();
			try {
				$this->mail->setFrom($from);
				$this->mail->setSender($from);
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

	function setFrom($email, $name = null)
	{
		$this->mail->setFrom($email, $name);
	}

	function setReplyTo($email, $name = null)
	{
		$this->mail->setReplyTo($email, $name);
	}

	function setSubject($subject)
	{
		$this->mail->setSubject($subject);
	}

	function setHtml($html, $text = null, $images_dir = null)
	{
		$body = $this->mail->getBody();
		if ( !($body instanceof \Zend\Mime\Message) && !empty($body)){
			$this->convertBodyToMime($body);
			$body = $this->mail->getBody();
		}

		if (! $body instanceof Zend\Mime\Message){
			$body = new Zend\Mime\Message();
		}

		$partHtmlFound = false;
		$partTextFound = false;

		$parts = $body->getParts();
		foreach($parts as $part){
			/* @var $part Zend\Mime\Part */
			if ($part->getType() == Zend\Mime\Mime::TYPE_HTML){
				$partHtmlFound = true;
				$part->setContent($html);
			}
			if ($part->getType() == Zend\Mime\Mime::TYPE_TEXT){
				$partTextFound = true;
				if ($text){
					$part->setContent($text);
				}
			}
		}

		if (!$partHtmlFound){
			$htmlPart = new Zend\Mime\Part($html);
			$htmlPart->setType(Zend\Mime\Mime::TYPE_HTML);
			$parts[] = $htmlPart;
		}

		if (!$partTextFound && $text){
			$textPart = new Zend\Mime\Part($text);
			$textPart->setType(Zend\Mime\Mime::TYPE_TEXT);
			$parts[] = $textPart;
		}

		$body->setParts($parts);
		$this->mail->setBody($body);
	}

	function setText($text = '')
	{
		$body = $this->mail->getBody();
		if ( $body instanceof \Zend\Mime\Message ){
			$parts = $body->getParts();
			$textPartFound = false;
			foreach($parts as $part){
				/* @var $part Zend\Mime\Part */
				if ($part->getType() == Zend\Mime\Mime::TYPE_TEXT){
					$part->setContent($text);
					$textPartFound = true;
					break;
				}
			}
			if (!$textPartFound){
				$part = new Zend\Mime\Part($text);
				$part->setType(Zend\Mime\Mime::TYPE_TEXT);
				$parts[] = $part;
			}
			$body->setParts($parts);
		} else {
			$this->mail->setBody($text);
		}
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
		if ($name === 'Message-ID') {
			$this->mail->getHeaders()->addHeader(Zend\Mail\Header\MessageId::fromString('Message-ID: ' . trim($value, "<>")));
		} else {
			$this->mail->getHeaders()->addHeaderLine($name, $value);
		}
	}

	function send($recipients, $type = 'mail')
	{
		global $tikilib, $prefs;
		$logslib = TikiLib::lib('logs');

		$this->mail->getHeaders()->removeHeader('to');
		foreach ((array) $recipients as $to) {
			$this->mail->addTo($to);
		}

        if ($prefs['zend_mail_handler'] == 'smtp' && $prefs['zend_mail_queue'] == 'y') {
            $query = "INSERT INTO `tiki_mail_queue` (message) VALUES (?)";
		    $bindvars = array(serialize($this->mail));
			$tikilib->query($query, $bindvars, -1, 0);
            $title = 'mail';
        } else {

    		try {
				tiki_send_email($this->mail);
    			$title = 'mail';
				$error = '';

    		} catch (Zend\Mail\Exception\ExceptionInterface $e) {
    			$title = 'mail error';
				$error = $e->getMessage();
				$this->errors[] = $error;
				$error = ' [' . $error . ']';
    		}

    		if ($title == 'mail error' || $prefs['log_mail'] == 'y') {
    			foreach ($recipients as $u) {
    				$logslib->add_log($title, $u . '/' . $this->mail->getSubject() . $error);
    			}
    		}
        }
		return $title == 'mail';
	}

	protected function convertBodyToMime($text)
	{
		$textPart = new Zend\Mime\Part($text);
		$textPart->setType(Zend\Mime\Mime::TYPE_TEXT);
		$newBody = new Zend\Mime\Message();
		$newBody->addPart($textPart);
		$this->mail->setBody($newBody);
	}

	function addAttachment($data, $filename, $mimetype)
	{
		$body = $this->mail->getBody();
		if (! ($body instanceof \Zend\Mime\Message) ){
			$this->convertBodyToMime($body);
			$body = $this->mail->getBody();
		}

		$attachment = new Zend\Mime\Part($data);
		$attachment->setFileName($filename);
		$attachment->setType($mimetype);
		$attachment->setEncoding(Zend\Mime\Mime::ENCODING_BASE64);
		$attachment->setDisposition(Zend\Mime\Mime::DISPOSITION_INLINE);
		$body->addPart($attachment);
	}

	/**
	 *	scramble an email with a method
	 *
	 * @param string $email email address to be scrambled
	 * @param string $method unicode or y: each character is replaced with the unicode value
	 *                       strtr: mr@tw.org -> mr AT tw DOT org
	 *                       x: mr@tw.org -> mr@xxxxxx
	 *
	 * @return string scrambled email
	 */
	static function scrambleEmail($email, $method='unicode')
	{
		switch ($method) {
		case 'strtr':
			$trans = array(	"@" => tra("(AT)"),
							"." => tra("(DOT)")
			);
			return strtr($email, $trans);
		case 'x' :
			$encoded = $email;
			for ($i = strpos($email, "@") + 1, $istrlen_email = strlen($email); $i < $istrlen_email; $i++) {
				if ($encoded[$i]  != ".") $encoded[$i] = 'x';
			}
			return $encoded;
		case 'unicode':
		case 'y':// for previous compatibility
			$encoded = '';
			for ($i = 0, $istrlen_email = strlen($email); $i < $istrlen_email; $i++) {
				$encoded .= '&#' . ord($email[$i]). ';';
			}
			return $encoded;
		case 'n':
		default:
			return $email;
		}
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

