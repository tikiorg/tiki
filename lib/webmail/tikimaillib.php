<?php
/** Extension htmlMimeMail
  * set some default params (mainly utf8 as titi is utf8) + use the mailCharset pref from a user
  */
include_once("lib/webmail/htmlMimeMail.php");

class TikiMail extends HtmlMimeMail {
		var $charset;
	
	function TikiMail($user = null) {
		global $prefs, $tikilib;

		parent::htmlMimeMail();
		$this->charset = !$user ? $prefs['default_mail_charset'] : $tikilib->get_user_preference($user, 'mailCharset', 'utf-8');
		$this->setTextCharset($this->charset);
		$this->setHtmlCharset($this->charset);
		$this->setHeadCharset($this->charset);
		if (isset($prefs['mail_crlf']))
			$this->setCrlf($prefs['mail_crlf'] == "LF"? "\n": "\r\n");
		$this->setFrom($prefs['sender_email']);
		if (!@ini_get('safe_mode'))
			$this->setReturnPath($prefs['sender_email']); // in safe-mode, return-path must then be configured at the server level
		$this->setHeader("Return-Path", "<".$prefs['sender_email'].">"); // just in case, mainly will not work as usually the server rewrites the envelop
		$this->setHeader("Reply-To",  "<".$prefs['sender_email'].">");
	}

	function setUser($user) {
		global $tikilib, $prefs;
		$this->charset = $tikilib->get_user_preference($user, 'mailCharset', $prefs['default_mail_charset']);
		$this->setTextCharset($this->charset);
		$this->setHtmlCharset($this->charset);
		$this->setHeadCharset($this->charset);
	}
		
	function _encodeHeader($input, $charset = 'ISO-8859-1') {
// todo perhaps chunk_split
		if (preg_match('/[\x80-\xFF]/', $input)) {
			$input = preg_replace('/([\x80-\xFF =])/e', '"=" . strtoupper(dechex(ord("\1")))', $input);
			return '=?'.$charset .'?Q?'.$input.'?=';
		}
		else
			return $input;
	}
	
	function setSubject($subject) {
		if ($this->charset != "utf-8")
			parent::setSubject(encodeString($this->encodeNonInCharset($subject, false), $this->charset));
		else
			parent::setSubject($subject);
	}
	function setHtml($html, $text = null, $images_dir = null) {
		if ($this->charset != "utf-8")
			
			parent::setHtml(encodeString($this->encodeNonInCharset($html, true), $this->charset), encodeString($this->encodeNonInCharset($text, false), $this->charset), $images_dir);
		else
			parent::setHtml($html, $text , $images_dir);
	}
	function setText($text = '') {
		if ($this->charset != "utf-8")
			parent::setText(encodeString($this->encodeNonInCharset($text, false), $this->charset));
		else
			parent::setText($text);
	}
	/** encode non existing charater is final charset
	 */
	function encodeNonInCharset($string, $toHtml=true) {
		if ($this->charset == 'iso-8859-1') {
			$bad = array('€','‚', 'ƒ','„', '…', '†', '‡', 'ˆ', '‰', 'Š',
				'‹', 'Œ', '‘', '’', '“', '”', '•', '–', '—', '˜', '™',
				'š', '›', 'œ', 'ÿ');
			$html = array('&euro;', '&sbquo;', '&fnof;', '&bdquo;', '&hellip;', '&dagger;', '&Dagger;', '&circ;', '&permil;', '&Scaron;', 
				'&lsaquo;', '&OElig;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&bull;', '&ndash;', '&mdash;', '&tilde;', '&trade;',
				'&scaron;', '&rsaquo;', '&oelig;', '&Yuml;');
			$text = array('euros', ',', 'f', ',,', '...', 'T','T', '^', '0/00', 'S',
				'<', 'OE', '\'', '\'', '"', '"', '.', '-', '-', '~', '(TM)',
				's', '>', 'oe', 'y');
	
			return str_replace($bad, $toHtml? $html: $text, $string);
		} else
			return $string;
	}
	function send($recipients, $type = 'mail') {
		global $prefs;
		global $logslib; include_once ('lib/logs/logslib.php');
		$result = parent::send($recipients, $type);
		$title = $result?'mail': 'mail error';
		if (!$result || $prefs['log_mail'])
			foreach ($recipients as $u) {
				$logslib->add_log($title, $u.'/'.$this->headers['Subject']);
			}
		return $result;
	}
}	
/**
  * encode a string
  * @param string $string : the string in utf-8
  * @param $charset: iso8859-1 or utf-8
  */
function encodeString($string, $charset="utf-8") {
	if ($string == null)
		return null;
	else if ($charset == "iso-8859-1")
		return utf8_decode($string);
	/* add other charsets */

	else
		return $string;
}
