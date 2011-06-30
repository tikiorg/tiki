<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** Extension htmlMimeMail
  * set some default params (mainly utf8 as titi is utf8) + use the mailCharset pref from a user
  */
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
include_once("lib/webmail/htmlMimeMail.php");

class TikiMail extends HtmlMimeMail
{
		var $charset;

	/* $user = user you send the mail
	   $from = email you send from*/
	function TikiMail($user = null, $from=null) {
		global $prefs, $tikilib;

		parent::htmlMimeMail();
		$this->charset = !$user ? $prefs['default_mail_charset'] : $tikilib->get_user_preference($user, 'mailCharset', 'utf-8');
		$this->setTextCharset($this->charset);
		$this->setHtmlCharset($this->charset);
		$this->setHeadCharset($this->charset);
		if (isset($prefs['mail_crlf'])) {
			$this->setCrlf($prefs['mail_crlf'] == "LF"? "\n": "\r\n");
		}
		if ($prefs['zend_mail_handler'] == 'smtp') {
			if ($prefs['zend_mail_smtp_auth'] == 'login') {
				$this->setSMTPParams($prefs['zend_mail_smtp_server'], $prefs['zend_mail_smtp_port'], $prefs['zend_mail_smtp_helo'], true, $prefs['zend_mail_smtp_user'], $prefs['zend_mail_smtp_pass'], $prefs['zend_mail_smtp_security']);
			} else {
				$this->setSMTPParams($prefs['zend_mail_smtp_server'], $prefs['zend_mail_smtp_port'], $prefs['zend_mail_smtp_helo'], false, null, null, $prefs['zend_mail_smtp_security']);
			}
		}
		if (empty($from)) {
			$from = $prefs['sender_email'];
		}
		$this->setFrom($from);
		if (!@ini_get('safe_mode')) {
			$this->setReturnPath($from); // in safe-mode, return-path must then be configured at the server level
		}
		$this->setHeader("Return-Path", $from); // just in case, mainly will not work as usually the server rewrites the envelop
		$this->setHeader("Reply-To",  $from);
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
		global $prefs;
		if (!empty($prefs['email_footer'])) {
			$text .= CRLF . $prefs['email_footer'];
		}

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
		if ($prefs['zend_mail_handler'] == 'smtp') {
			$type = 'smtp';
		}
		$result = parent::send($recipients, $type);
		$title = $result?'mail': 'mail error';
		if (!$result || $prefs['log_mail'] == 'y')
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

function decode_subject_utf8($string){
	if (preg_match('/=\?.*\?.*\?=/', $string) === false)
		return $string;
	$string = explode('?', $string);
	$str = strtolower($string[2]) == 'q' ?quoted_printable_decode($string[3]):base64_decode($string[3]);
 	if (strtolower($string[1]) == "iso-8859-1")
		return utf8_encode($str);
	else if (strtolower($string[1]) == "utf-8")
		return $str;
	else if (function_exists('mb_convert_encoding'))
		return mb_convert_encoding($str, "utf-8", $string[1]);
	else
		return $str;
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
function format_email_reply(&$text, $from, $date) {
	$lines = preg_split('/[\n\r]+/',wordwrap($text));

	for ($i = 0, $icount_lines = count($lines); $i < $icount_lines; $i++) {
		$lines[$i] = '> '.$lines[$i]."\n";
	}
	$str = !empty($from) ? $from.' wrote' : '';
	$str .= !empty($date) ? ' on '.$date : '';
	$str = "\n\n\n".$str."\n".implode($lines);
	
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
function closetags ( $html ) {
    #put all opened tags into an array
    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
    $openedtags = $result[1];
 
    #put all closed tags into an array
    preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
    $closedtags = $result[1];
    $len_opened = count ( $openedtags );
    # all tags are closed
    if( count ( $closedtags ) == $len_opened ) {
        return $html;
    }
    $openedtags = array_reverse ( $openedtags );
    # close tags
    for( $i = 0; $i < $len_opened; $i++ ) {
        if ( !in_array ( $openedtags[$i], $closedtags )) {
            $html .= "</" . $openedtags[$i] . ">";
        } else {
            unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
        }
    }
    return $html;
}

