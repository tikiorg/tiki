<?php
/** Extension htmlMimeMail
  * set some default params (mainly utf8 as titi is utf8) + use the mailCharset pref from a user
  */
include_once("lib/webmail/htmlMimeMail.php");

class TikiMail extends HtmlMimeMail {
		var $charset;
	
	function TikiMail($user = null) {
		global $sender_email;
		global $tikilib, $mail_crlf;

		parent::htmlMimeMail();
		$this->charset = !$user ? $tikilib->get_preference("default_mail_charset", "utf-8"): $tikilib->get_user_preference($user, "mailCharset", "utf-8");
		$this->setTextCharset($this->charset);
		$this->setHtmlCharset($this->charset);
		$this->setHeadCharset($this->charset);
		if (isset($mail_crlf))
			$this->setCrlf($mail_crlf == "LF"? "\n": "\r\n");
		$this->setFrom($sender_email);
		if (!@ini_get('safe_mode'))
			$this->setReturnPath($sender_email); // in safe-mode, return-path must then be configured at the server level
		$this->setHeader("Return-Path", "<",$sender_email,">"); // just in case, mainly will not work as usually the server rewrites the envelop
		$this->setHeader("Reply-To",  "<".$sender_email.">");
	}

	function setUser($user) {
		global $tikilib;
		$this->charset = $tikilib->get_user_preference($user, "mailCharset", $tikilib->get_preference('default_mail_charset', 'utf-8'));
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
			parent::setSubject(encodeString($subject, $this->charset));
		else
			parent::setSubject($subject);
	}
	function setHtml($html, $text = null, $images_dir = null) {
		if ($this->charset != "utf-8")
			parent::setHtml(encodeString($html, $this->charset), encodeString($text, $this->charset), $images_dir);
		else
			parent::setHtml($html, $text , $images_dir);
	}
	function setText($text = '') {
		if ($this->charset != "utf-8")
			parent::setText(encodeString($text, $this->charset));
		else
			parent::setText($text);
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
?>