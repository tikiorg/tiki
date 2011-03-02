<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
* Filename.......: class.html.mime.mail.inc
* Project........: HTML Mime mail class
* Last Modified..: $Date: 2007-06-16 16:01:58 $
* CVS Revision...: $Revision: 1.19 $
* Copyright......: 2001, 2002 Richard Heyes
*/

//require_once(dirname(__FILE__) . '/mimePart.php');
class htmlMimeMail
{
	/**
	* The html part of the message
	* @var string
	*/
	var $html;

	/**
	* The text part of the message(only used in TEXT only messages)
	* @var string
	*/
	var $text;

	/**
	* The main body of the message after building
	* @var string
	*/
	var $output;

	/**
	* The alternative text to the HTML part (only used in HTML messages)
	* @var string
	*/
	var $html_text;

	/**
	* An array of embedded images/objects
	* @var array
	*/
	var $html_images;

	/**
	* An array of recognised image types for the findHtmlImages() method
	* @var array
	*/
	var $image_types;

	/**
	* Parameters that affect the build process
	* @var array
	*/
	var $build_params;

	/**
	* Array of attachments
	* @var array
	*/
	var $attachments;

	/**
	* The main message headers
	* @var array
	*/
	var $headers;

	/**
	* Whether the message has been built or not
	* @var boolean
	*/
	var $is_built;

	/**
	* The return path address. If not set the From:
	* address is used instead
	* @var string
	*/
	var $return_path;

	/**
	* Array of information needed for smtp sending
	* @var array
	*/
	var $smtp_params;

	/**
	* Constructor function. Sets the headers
	* if supplied.
	*/
	function htmlMimeMail() {
		/**
		* Initialise some variables.
		*/
		$this->html_images = array();

		$this->headers = array();
		$this->is_built = false;

		/**
		* If you want the auto load functionality
		* to find other image/file types, add the
		* extension and content type here.
		*/
		$this->image_types = array(
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpe' => 'image/jpeg',
			'bmp' => 'image/bmp',
			'png' => 'image/png',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'swf' => 'application/x-shockwave-flash'
		);

		/**
		* Set these up
		*/
		$this->build_params['html_encoding'] = 'quoted-printable';
		$this->build_params['text_encoding'] = '7bit';
		$this->build_params['html_charset'] = 'ISO-8859-1';
		$this->build_params['text_charset'] = 'ISO-8859-1';
		$this->build_params['head_charset'] = 'ISO-8859-1';
		$this->build_params['text_wrap'] = 998;

		/**
		* Defaults for smtp sending
		*/
		if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
			$helo = $_SERVER['HTTP_HOST'];
		} elseif (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) {
			$helo = $_SERVER['SERVER_NAME'];
		} else {
			$helo = 'localhost';
		}

		$this->smtp_params['host'] = 'localhost';
		$this->smtp_params['port'] = 25;
		$this->smtp_params['helo'] = $helo;
		$this->smtp_params['auth'] = false;
		$this->smtp_params['user'] = '';
		$this->smtp_params['pass'] = '';

		/**
		* Make sure the MIME version header is first.
		*/
		$this->headers['MIME-Version'] = '1.0';
	}

	/**
	* This function will read a file in
	* from a supplied filename and return
	* it. This can then be given as the first
	* argument of the the functions
	* add_html_image() or add_attachment().
	*/
	function getFile($filename) {
		$return = '';

		if ($fp = fopen($filename, 'rb')) {
			while (!feof($fp)) {
				$return .= fread($fp, 1024);
			}

			fclose ($fp);
			return $return;
		} else {
			return false;
		}
	}

	/**
	* Accessor to set the CRLF style
	*/
	function setCrlf($crlf = "\n") {
		if (!defined('CRLF')) {
			define('CRLF', $crlf, true);
		}

		if (!defined('MAIL_MIMEPART_CRLF')) {
			define('MAIL_MIMEPART_CRLF', $crlf, true);
		}
	}

	/**
	* Accessor to set the SMTP parameters
	*/
	function setSMTPParams($host = null, $port = null, $helo = null, $auth = null, $user = null, $pass = null, $security = '') {
		if (!is_null($host))
			$this->smtp_params['host'] = $host;

		if (!is_null($port))
			$this->smtp_params['port'] = $port;

		if (!is_null($helo))
			$this->smtp_params['helo'] = $helo;

		$this->smtp_params['auth'] = ("y" == $auth);

		if (!is_null($user))
			$this->smtp_params['user'] = $user;

		if (!is_null($pass))
			$this->smtp_params['pass'] = $pass;
			
		if (!is_null($security))
			$this->smtp_params['security'] = $security;
	}

	/**
	* Accessor function to set the text encoding
	*/
	function setTextEncoding($encoding = '7bit') {
		$this->build_params['text_encoding'] = $encoding;
	}

	/**
	* Accessor function to set the HTML encoding
	*/
	function setHtmlEncoding($encoding = 'quoted-printable') {
		$this->build_params['html_encoding'] = $encoding;
	}

	/**
	* Accessor function to set the text charset
	*/
	function setTextCharset($charset = 'ISO-8859-1') {
		$this->build_params['text_charset'] = $charset;
	}

	/**
	* Accessor function to set the HTML charset
	*/
	function setHtmlCharset($charset = 'ISO-8859-1') {
		$this->build_params['html_charset'] = $charset;
	}

	/**
	* Accessor function to set the header encoding charset
	*/
	function setHeadCharset($charset = 'ISO-8859-1') {
		$this->build_params['head_charset'] = $charset;
	}

	/**
	* Accessor function to set the text wrap count
	*/
	function setTextWrap($count = 998) {
		$this->build_params['text_wrap'] = $count;
	}

	/**
	* Accessor to set a header
	*/
	function setHeader($name, $value) {
		$this->headers[$name] = $value;
	}

	/**
	* Accessor to add a Subject: header
	*/
	function setSubject($subject) {
		$this->headers['Subject'] = $subject;
	}

	/**
	* Accessor to add a From: header
	*/
	function setFrom($from) {
		$this->headers['From'] = $from;
	}

	/**
	* Accessor to set the return path
	*/
	function setReturnPath($return_path) {
		$this->return_path = $return_path;
	}

	/**
	* Accessor to add a Cc: header
	*/
	function setCc($cc) {
		$this->headers['Cc'] = $cc;
	}

	/**
	* Accessor to add a Bcc: header
	*/
	function setBcc($bcc) {
		$this->headers['Bcc'] = $bcc;
	}

	/**
	* Adds plain text. Use this function
	* when NOT sending html email
	*/
	function setText($text = '') {
		$this->text = $text;
	}

	/**
	* Adds a html part to the mail.
	* Also replaces image names with
	* content-id's.
	*/
	function setHtml($html, $text = null, $images_dir = null) {
		$this->html = $html;

		$this->html_text = $text;

		if (isset($images_dir)) {
			$this->_findHtmlImages($images_dir);
		}
	}

	/**
	* Function for extracting images from
	* html source. This function will look
	* through the html code supplied by add_html()
	* and find any file that ends in one of the
	* extensions defined in $obj->image_types.
	* If the file exists it will read it in and
	* embed it, (not an attachment).
	*
	* @author Dan Allen
	*/
	function _findHtmlImages($images_dir) {
		// Build the list of image extensions
		while (list($key) = each($this->image_types)) {
			$extensions[] = $key;
		}

		preg_match_all('/(?:"|\')([^"\']+\.(' . implode('|', $extensions). '))(?:"|\')/Ui', $this->html, $images);

		for ($i = 0; $i < count($images[1]); $i++) {
			if (file_exists($images_dir . $images[1][$i])) {
				$html_images[] = $images[1][$i];

				$this->html = str_replace($images[1][$i], basename($images[1][$i]), $this->html);
			}
		}

		if (!empty($html_images)) {

			// If duplicate images are embedded, they may show up as attachments, so remove them.
			$html_images = array_unique($html_images);

			sort ($html_images);

			for ($i = 0; $i < count($html_images); $i++) {
				if ($image = $this->getFile($images_dir . $html_images[$i])) {
					$ext = substr($html_images[$i], strrpos($html_images[$i], '.') + 1);

					$content_type = $this->image_types[strtolower($ext)];
					$this->addHtmlImage($image, basename($html_images[$i]), $content_type);
				}
			}
		}
	}

	/**
	* Adds an image to the list of embedded
	* images.
	*/
	function addHtmlImage($file, $name = '', $c_type = 'application/octet-stream') {
		$this->html_images[] = array(
			'body' => $file,
			'name' => $name,
			'c_type' => $c_type,
			'cid' => md5(uniqid(time()))
		);
	}

	/**
	* Adds a file to the list of attachments.
	*/
	function addAttachment($file, $name = '', $c_type = 'application/octet-stream', $encoding = 'base64') {
		$this->attachments[] = array(
			'body' => $file,
			'name' => $name,
			'c_type' => $c_type,
			'encoding' => $encoding
		);
	}

	/**
	* Adds a text subpart to a mime_part object
	*/
	function &_addTextPart(&$obj, $text) {
		$params['content_type'] = 'text/plain';

		$params['encoding'] = $this->build_params['text_encoding'];
		$params['charset'] = $this->build_params['text_charset'];

		if (is_object($obj)) {
			return $obj->addSubpart($text, $params);
		} else {
			$result = new Mail_mimePart($text, $params);
			return $result;
		}
	}

	/**
	* Adds a html subpart to a mime_part object
	*/
	function &_addHtmlPart(&$obj) {
		$params['content_type'] = 'text/html';

		$params['encoding'] = $this->build_params['html_encoding'];
		$params['charset'] = $this->build_params['html_charset'];

		if (is_object($obj)) {
			return $obj->addSubpart($this->html, $params);
		} else {
			$n = new Mail_mimePart($this->html, $params);
			return $n;
		}
	}

	/**
	* Starts a message with a mixed part
	*/
	function &_addMixedPart() {
		$params['content_type'] = 'multipart/mixed';
		$n = new Mail_mimePart('', $params);
		return $n;
	}

	/**
	* Adds an alternative part to a mime_part object
	*/
	function &_addAlternativePart(&$obj) {
		$params['content_type'] = 'multipart/alternative';

		if (is_object($obj)) {
			return $obj->addSubpart('', $params);
		} else {
			$m = new Mail_mimePart('', $params);
			return $m;
		}
	}

	/**
	* Adds a html subpart to a mime_part object
	*/
	function &_addRelatedPart(&$obj) {
		$params['content_type'] = 'multipart/related';

		if (is_object($obj)) {
			return $obj->addSubpart('', $params);
		} else {
			$n = new Mail_mimePart('', $params);
			return $n;
		}
	}

	/**
	* Adds an html image subpart to a mime_part object
	*/
	function &_addHtmlImagePart(&$obj, $value) {
		$params['content_type'] = $value['c_type'];

		$params['encoding'] = 'base64';
		$params['disposition'] = 'inline';
		$params['dfilename'] = $value['name'];
		$params['cid'] = $value['cid'];
		$obj->addSubpart($value['body'], $params);
	}

	/**
	* Adds an attachment subpart to a mime_part object
	*/
	function &_addAttachmentPart(&$obj, $value) {
		$params['content_type'] = $value['c_type'];

		$params['encoding'] = $value['encoding'];
		$params['disposition'] = 'attachment';
		$params['dfilename'] = $value['name'];
		$ret = $obj->addSubpart($value['body'], $params);
		return $ret;
	}

	/**
	* Builds the multipart message from the
	* list ($this->_parts). $params is an
	* array of parameters that shape the building
	* of the message. Currently supported are:
	*
	* $params['html_encoding'] - The type of encoding to use on html. Valid options are
	*                            "7bit", "quoted-printable" or "base64" (all without quotes).
	*                            7bit is EXPRESSLY NOT RECOMMENDED. Default is quoted-printable
	* $params['text_encoding'] - The type of encoding to use on plain text Valid options are
	*                            "7bit", "quoted-printable" or "base64" (all without quotes).
	*                            Default is 7bit
	* $params['text_wrap']     - The character count at which to wrap 7bit encoded data.
	*                            Default this is 998.
	* $params['html_charset']  - The character set to use for a html section.
	*                            Default is ISO-8859-1
	* $params['text_charset']  - The character set to use for a text section.
	*                          - Default is ISO-8859-1
	* $params['head_charset']  - The character set to use for header encoding should it be needed.
	*                          - Default is ISO-8859-1
	*/
	function buildMessage($params = array()) {
		if (!empty($params)) {
			while (list($key, $value) = each($params)) {
				$this->build_params[$key] = $value;
			}
		}

		if (!empty($this->html_images)) {
			foreach ($this->html_images as $value) {
				$this->html = str_replace($value['name'], 'cid:' . $value['cid'], $this->html);
			}
		}

		$null = null;
		$attachments = !empty($this->attachments) ? true : false;
		$html_images = !empty($this->html_images) ? true : false;
		$html = !empty($this->html) ? true : false;
		$text = isset($this->text) ? true : false;

		switch (true) {
		case $text AND !$attachments:
			$message = &$this->_addTextPart($null, $this->text);

			break;

		case !$text AND $attachments AND !$html:
			$message = &$this->_addMixedPart();

			for ($i = 0; $i < count($this->attachments); $i++) {
				$this->_addAttachmentPart($message, $this->attachments[$i]);
			}

			break;

		case $text AND $attachments:
			$message = &$this->_addMixedPart();

			$this->_addTextPart($message, $this->text);

			for ($i = 0; $i < count($this->attachments); $i++) {
				$this->_addAttachmentPart($message, $this->attachments[$i]);
			}

			break;

		case $html AND !$attachments AND !$html_images:
			if (!is_null($this->html_text)) {
				$message = &$this->_addAlternativePart($null);

				$this->_addTextPart($message, $this->html_text);
				$this->_addHtmlPart($message);
			} else {
				$message = &$this->_addHtmlPart($null);
			}

			break;

		case $html AND !$attachments AND $html_images:
			if (!is_null($this->html_text)) {
				$message = &$this->_addAlternativePart($null);

				$this->_addTextPart($message, $this->html_text);
				$related = &$this->_addRelatedPart($message);
			} else {
				$message = &$this->_addRelatedPart($null);

				$related = &$message;
			}

			$this->_addHtmlPart($related);

			for ($i = 0; $i < count($this->html_images); $i++) {
				$this->_addHtmlImagePart($related, $this->html_images[$i]);
			}

			break;

		case $html AND $attachments AND !$html_images:
			$message = &$this->_addMixedPart();

			if (!is_null($this->html_text)) {
				$alt = &$this->_addAlternativePart($message);

				$this->_addTextPart($alt, $this->html_text);
				$this->_addHtmlPart($alt);
			} else {
				$this->_addHtmlPart($message);
			}

			for ($i = 0; $i < count($this->attachments); $i++) {
				$this->_addAttachmentPart($message, $this->attachments[$i]);
			}

			break;

		case $html AND $attachments AND $html_images:
			$message = &$this->_addMixedPart();

			if (!is_null($this->html_text)) {
				$alt = &$this->_addAlternativePart($message);

				$this->_addTextPart($alt, $this->html_text);
				$rel = &$this->_addRelatedPart($alt);
			} else {
				$rel = &$this->_addRelatedPart($message);
			}

			$this->_addHtmlPart($rel);

			for ($i = 0; $i < count($this->html_images); $i++) {
				$this->_addHtmlImagePart($rel, $this->html_images[$i]);
			}

			for ($i = 0; $i < count($this->attachments); $i++) {
				$this->_addAttachmentPart($message, $this->attachments[$i]);
			}

			break;
		}

		if (isset($message)) {
			$output = $message->encode();

			$this->output = $output['body'];
			$this->headers = array_merge($this->headers, $output['headers']);

			// Add message ID header
			srand ((double)microtime() * 10000000);
			$message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
			$this->headers['Message-ID'] = $message_id;

			$this->is_built = true;
			return true;
		} else {
			return false;
		}
	}

	/**
* Function to encode a header if necessary
* according to RFC2047
*/
	function _encodeHeader($input, $charset = 'ISO-8859-1') {
		preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $input, $matches);

		foreach ($matches[1] as $value) {
			$replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);

			$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
		}

		return $input;
	}

	/**
* Sends the mail.
*
* @param  array  $recipients
* @param  string $type OPTIONAL
* @return mixed
*/
	function send($recipients, $type = 'mail') {
		if ( ! empty($recipients) && is_string($recipients) ) {
			$recipients = array($recipients);
		}
		if (!defined('CRLF')) {
			$this->setCrlf($type == 'mail' ? "\n" : "\r\n");
		}

		if (!$this->is_built) {
			$this->buildMessage();
		}

		switch ($type) {
		case 'mail':
			$subject = '';

			if (!empty($this->headers['Subject'])) {
				$subject = $this->_encodeHeader($this->headers['Subject'], $this->build_params['head_charset']);

				unset ($this->headers['Subject']);
			}

			// Get flat representation of headers
			foreach ($this->headers as $name => $value) {
				$headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
			}

			$to = $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);

			if (!empty($this->return_path)) {
				// Set the sender for sendmail and use only the email address when the syntax of return_path is like 'Name <email>'
				$additional_parameters = '-f' . preg_replace('/^.*<(.*?)>.*$/', '$1', $this->return_path);
				$result = mail($to, $subject, $this->output, implode(CRLF, $headers), $additional_parameters);
			} else {
				$result = mail($to, $subject, $this->output, implode(CRLF, $headers));
			}

			// Reset the subject in case mail is resent
			if ($subject !== '') {
				$this->headers['Subject'] = $subject;
			}

			// Return
			return $result;
			break;

		case 'smtp':
			//require_once(dirname(__FILE__) . '/smtp.php');
			//require_once(dirname(__FILE__) . '/RFC822.php');
			//:TODO: This may not work (A fix)
			if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
				$helo = $_SERVER['HTTP_HOST'];
			} elseif (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) {
				$helo = $_SERVER['SERVER_NAME'];
			} else {
				$helo = 'localhost';
			}

			$this->smtp_params['helo'] = $helo;
			$smtp = &smtp::connect($this->smtp_params);

			// Parse recipients argument for internet addresses
			foreach ($recipients as $recipient) {
				$addresses = Mail_RFC822::parseAddressList($recipient, $this->smtp_params['helo'], null, false);

				foreach ($addresses as $address) {
					$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
				}
			}

			unset ($addresses); // These are reused
			unset ($address); // These are reused

			// Get flat representation of headers, parsing
			// Cc and Bcc as we go
			foreach ($this->headers as $name => $value) {
				if ($name == 'Cc' OR $name == 'Bcc') {
					$addresses = Mail_RFC822::parseAddressList($value, $this->smtp_params['helo'], null, false);

					foreach ($addresses as $address) {
						$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
					}
				}

				if ($name == 'Bcc') {
					continue;
				}

				$headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
			}

			// Add To header based on $recipients argument
			$headers[] = 'To: ' . $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);

			// Add headers to send_params
			$send_params['headers'] = $headers;
			$send_params['recipients'] = array_values(array_unique($smtp_recipients));
			$send_params['body'] = $this->output;

			// Setup return path
			if (isset($this->return_path)) {
				$send_params['from'] = $this->return_path;
			} elseif (!empty($this->headers['From'])) {
				$from = Mail_RFC822::parseAddressList($this->headers['From']);

				$send_params['from'] = sprintf('%s@%s', $from[0]->mailbox, $from[0]->host);
			} else {
				$send_params['from'] = 'postmaster@' . $this->smtp_params['helo'];
			}

			// Send it
			if (!$smtp->send($send_params)) {
				$this->errors = $smtp->errors;

				return false;
			}

			return true;
			break;
		}
	}

	/**
* Use this method to return the email
* in message/rfc822 format. Useful for
* adding an email to another email as
* an attachment. there's a commented
* out example in example.php.
*/
	function getRFC822($recipients) {
		// Make up the date header as according to RFC822
		// TODO Change to user or system defined timezone
		$this->setHeader('Date', date('D, d M y H:i:s O'));

		if (!defined('CRLF')) {
			$this->setCrlf($type == 'mail' ? "\n" : "\r\n");
		}

		if (!$this->is_built) {
			$this->buildMessage();
		}

		// Return path ?
		if (isset($this->return_path)) {
			$headers[] = 'Return-Path: ' . $this->return_path;
		}

		// Get flat representation of headers
		foreach ($this->headers as $name => $value) {
			$headers[] = $name . ': ' . $value;
		}

		$headers[] = 'To: ' . implode(', ', $recipients);

		return implode(CRLF, $headers). CRLF . CRLF . $this->output;
	}
} // class htmlMimeMail

// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Richard Heyes <richard@phpguru.org>                         |
// +----------------------------------------------------------------------+

/**
*
*  Raw mime encoding class
*
* What is it?
*   This class enables you to manipulate and build
*   a mime email from the ground up.
*
* Why use this instead of mime.php?
*   mime.php is a userfriendly api to this class for
*   people who aren't interested in the internals of
*   mime mail. This class however allows full control
*   over the email.
*
* Eg.
*
* // Since multipart/mixed has no real body, (the body is
* // the subpart), we set the body argument to blank.
*
* $params['content_type'] = 'multipart/mixed';
* $email = new Mail_mimePart('', $params);
*
* // Here we add a text part to the multipart we have
* // already. Assume $body contains plain text.
*
* $params['content_type'] = 'text/plain';
* $params['encoding']     = '7bit';
* $text = $email->addSubPart($body, $params);
*
* // Now add an attachment. Assume $attach is
* the contents of the attachment
*
* $params['content_type'] = 'application/zip';
* $params['encoding']     = 'base64';
* $params['disposition']  = 'attachment';
* $params['dfilename']    = 'example.zip';
* $attach =& $email->addSubPart($body, $params);
*
* // Now build the email. Note that the encode
* // function returns an associative array containing two
* // elements, body and headers. You will need to add extra
* // headers, (eg. Mime-Version) before sending.
*
* $email = $message->encode();
* $email['headers'][] = 'Mime-Version: 1.0';
*
*
* Further examples are available at http://www.phpguru.org
*
* TODO:
*  - Set encode() to return the $obj->encoded if encode()
*    has already been run. Unless a flag is passed to specifically
*    re-build the message.
*
* @author  Richard Heyes <richard@phpguru.org>
* @version $Revision: 1.19 $
* @package Mail
*/
class Mail_mimePart
{

	/**
 * The encoding type of this part
 * @var string
 */
	var $_encoding;

	/**
 * An array of subparts
 * @var array
 */
	var $_subparts;

	/**
 * The output of this part after being built
 * @var string
 */
	var $_encoded;

	/**
 * Headers for this part
 * @var array
 */
	var $_headers;

	/**
 * The body of this part (not encoded)
 * @var string
 */
	var $_body;

	/**
 * Constructor.
 *
 * Sets up the object.
 *
 * @param $body   - The body of the mime part if any.
 * @param $params - An associative array of parameters:
 *                  content_type - The content type for this part eg multipart/mixed
 *                  encoding     - The encoding to use, 7bit, 8bit, base64, or quoted-printable
 *                  cid          - Content ID to apply
 *                  disposition  - Content disposition, inline or attachment
 *                  dfilename    - Optional filename parameter for content disposition
 *                  description  - Content description
 *                  charset      - Character set to use
 * @access public
 */
	function Mail_mimePart($body = '', $params = array()) {
		if (!defined('MAIL_MIMEPART_CRLF')) {
			define('MAIL_MIMEPART_CRLF', defined('MAIL_MIME_CRLF') ? MAIL_MIME_CRLF : "\r\n", TRUE);
		}

		foreach ($params as $key => $value) {
			switch ($key) {
			case 'content_type':
				$headers['Content-Type'] = $value . (isset($charset) ? '; charset="' . $charset . '"' : '');

				break;

			case 'encoding':
				$this->_encoding = $value;

				$headers['Content-Transfer-Encoding'] = $value;
				break;

			case 'cid':
				$headers['Content-ID'] = '<' . $value . '>';

				break;

			case 'disposition':
				$headers['Content-Disposition'] = $value . (isset($dfilename) ? '; filename="' . $dfilename . '"' : '');

				break;

			case 'dfilename':
				if (isset($headers['Content-Disposition'])) {
					$headers['Content-Disposition'] .= '; filename="' . $value . '"';
				} else {
					$dfilename = $value;
				}

				break;

			case 'description':
				$headers['Content-Description'] = $value;

				break;

			case 'charset':
				if (isset($headers['Content-Type'])) {
					$headers['Content-Type'] .= '; charset="' . $value . '"';
				} else {
					$charset = $value;
				}

				break;
			}
		}

		// Default content-type
		if (!isset($headers['Content-Type'])) {
			$headers['Content-Type'] = 'text/plain';
		}

		//Default encoding
		if (!isset($this->_encoding)) {
			$this->_encoding = '7bit';
		}

		// Assign stuff to member variables
		$this->_encoded = array();
		$this->_headers = $headers;
		$this->_body = $body;
	}

	/**
 * encode()
 *
 * Encodes and returns the email. Also stores
 * it in the encoded member variable
 *
 * @return An associative array containing two elements,
 *         body and headers. The headers element is itself
 *         an indexed array.
 * @access public
 */
	function encode() {
		$encoded = &$this->_encoded;

		if (!empty($this->_subparts)) {
			srand ((double)microtime() * 1000000);

			$boundary = '=_' . md5(uniqid(rand()). microtime());
			$this->_headers['Content-Type'] .= ';' . MAIL_MIMEPART_CRLF . "\t" . 'boundary="' . $boundary . '"';

			// Add body parts to $subparts
			for ($i = 0; $i < count($this->_subparts); $i++) {
				$headers = array();

				$tmp = $this->_subparts[$i]->encode();

				foreach ($tmp['headers'] as $key => $value) {
					$headers[] = $key . ': ' . $value;
				}

				$subparts[] = implode(MAIL_MIMEPART_CRLF, $headers). MAIL_MIMEPART_CRLF . MAIL_MIMEPART_CRLF . $tmp['body'];
			}

			$encoded['body'] = '--' . $boundary . MAIL_MIMEPART_CRLF . implode('--' . $boundary . MAIL_MIMEPART_CRLF, $subparts). '--' . $boundary . '--' . MAIL_MIMEPART_CRLF;
		} else {
			$encoded['body'] = $this->_getEncodedData($this->_body, $this->_encoding). MAIL_MIMEPART_CRLF;
		}

		// Add headers to $encoded
		$encoded['headers'] = &$this->_headers;

		return $encoded;
	}

	/**
 * &addSubPart()
 *
 * Adds a subpart to current mime part and returns
 * a reference to it
 *
 * @param $body   The body of the subpart, if any.
 * @param $params The parameters for the subpart, same
 *                as the $params argument for constructor.
 * @return A reference to the part you just added. It is
 *         crucial if using multipart/* in your subparts that
 *         you use =& in your script when calling this function,
 *         otherwise you will not be able to add further subparts.
 * @access public
 */
	function &addSubPart($body, $params) {
		$this->_subparts[] = new Mail_mimePart($body, $params);

		return $this->_subparts[count($this->_subparts) - 1];
	}

	/**
 * _getEncodedData()
 *
 * Returns encoded data based upon encoding passed to it
 *
 * @param $data     The data to encode.
 * @param $encoding The encoding type to use, 7bit, base64,
 *                  or quoted-printable.
 * @access private
 */
	function _getEncodedData($data, $encoding) {
		switch ($encoding) {
		case '8bit':
		case '7bit':
			return $data;

			break;

		case 'quoted-printable':
			return $this->_quotedPrintableEncode($data);

			break;

		case 'base64':
			return rtrim(chunk_split(base64_encode($data), 76, MAIL_MIMEPART_CRLF));

			break;

		default:
			return $data;
		}
	}

	/**
 * quoteadPrintableEncode()
 *
 * Encodes data to quoted-printable standard.
 *
 * @param $input    The data to encode
 * @param $line_max Optional max line length. Should
 *                  not be more than 76 chars
 *
 * @access private
 */
	function _quotedPrintableEncode($input, $line_max = 76) {
		$lines = preg_split("/\r?\n/", $input);

		$eol = MAIL_MIMEPART_CRLF;
		$escape = '=';
		$output = '';

		while (list(, $line) = each($lines)) {
			$linlen = strlen($line);

			$newline = '';

			for ($i = 0; $i < $linlen; $i++) {
				$char = substr($line, $i, 1);

				$dec = ord($char);

				if (($dec == 32) AND ($i == ($linlen - 1))) { // convert space at eol only
					$char = '=20';
				} elseif ($dec == 9) {
					; // Do nothing if a tab.
				} elseif (($dec == 61) OR ($dec < 32) OR ($dec > 126)) {
					$char = $escape . strtoupper(sprintf('%02s', dechex($dec)));
				}

				if ((strlen($newline) + strlen($char)) >= $line_max) { // MAIL_MIMEPART_CRLF is not counted
					$output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay

					$newline = '';
				}

				$newline .= $char;
			} // end of for

			$output .= $newline . $eol;
		}

		$output = substr($output, 0, -1 * strlen($eol)); // Don't want last crlf
		return $output;
	}
} // class Mail_mimePart

/**
* RFC 822 Email address list validation Utility
*
* What is it?
*
* This class will take an address string, and parse it into it's consituent
* parts, be that either addresses, groups, or combinations. Nested groups
* are not supported. The structure it returns is pretty straight forward,
* and is similar to that provided by the imap_rfc822_parse_adrlist(). Use
* print_r() to view the structure.
*
* How do I use it?
*
* $address_string = 'My Group: "Richard Heyes" <richard@localhost> (A comment), ted@example.com (Ted Bloggs), Barney;';
* $structure = Mail_RFC822::parseAddressList($address_string, 'example.com', TRUE)
* print_r($structure);
*
* @author  Richard Heyes <richard@phpguru.org>
* @author  Chuck Hagenbuch <chuck@horde.org>
* @version $Revision: 1.19 $
* @package Mail
*/
class Mail_RFC822
{
	/**
 * The address being parsed by the RFC822 object.
 * @var string $address
 */
	var $address = '';

	/**
 * The default domain to use for unqualified addresses.
 * @var string $default_domain
 */
	var $default_domain = 'localhost';

	/**
 * Should we return a nested array showing groups, or flatten everything?
 * @var boolean $nestGroups
 */
	var $nestGroups = true;

	/**
 * Whether or not to validate atoms for non-ascii characters.
 * @var boolean $validate
 */
	var $validate = true;

	/**
 * The array of raw addresses built up as we parse.
 * @var array $addresses
 */
	var $addresses = array();

	/**
 * The final array of parsed address information that we build up.
 * @var array $structure
 */
	var $structure = array();

	/**
 * The current error message, if any.
 * @var string $error
 */
	var $error = null;

	/**
 * An internal counter/pointer.
 * @var integer $index
 */
	var $index = null;

	/**
 * The number of groups that have been found in the address list.
 * @var integer $num_groups
 * @access public
 */
	var $num_groups = 0;

	/**
 * A variable so that we can tell whether or not we're inside a
 * Mail_RFC822 object.
 * @var boolean $mailRFC822
 */
	var $mailRFC822 = true;

	/**
* A limit after which processing stops
* @var int $limit
*/
	var $limit = null;

	/**
 * Sets up the object. The address must either be set here or when
 * calling parseAddressList(). One or the other.
 *
 * @access public
 * @param string  $address         The address(es) to validate.
 * @param string  $default_domain  Default domain/host etc. If not supplied, will be set to localhost.
 * @param boolean $nest_groups     Whether to return the structure with groups nested for easier viewing.
 * @param boolean $validate        Whether to validate atoms. Turn this off if you need to run addresses through before encoding the personal names, for instance.
 * 
 * @return object Mail_RFC822 A new Mail_RFC822 object.
 */
	function Mail_RFC822($address = null, $default_domain = null, $nest_groups = null, $validate = null, $limit = null) {
		if (isset($address))
			$this->address = $address;

		if (isset($default_domain))
			$this->default_domain = $default_domain;

		if (isset($nest_groups))
			$this->nestGroups = $nest_groups;

		if (isset($validate))
			$this->validate = $validate;

		if (isset($limit))
			$this->limit = $limit;
	}

	/**
 * Starts the whole process. The address must either be set here
 * or when creating the object. One or the other.
 *
 * @access public
 * @param string  $address         The address(es) to validate.
 * @param string  $default_domain  Default domain/host etc.
 * @param boolean $nest_groups     Whether to return the structure with groups nested for easier viewing.
 * @param boolean $validate        Whether to validate atoms. Turn this off if you need to run addresses through before encoding the personal names, for instance.
 * 
 * @return array A structured array of addresses.
 */
	function parseAddressList($address = null, $default_domain = null, $nest_groups = null, $validate = null, $limit = null) {
		if (!isset($this->mailRFC822)) {
			$obj = new Mail_RFC822($address, $default_domain, $nest_groups, $validate, $limit);

			return $obj->parseAddressList();
		}

		if (isset($address))
			$this->address = $address;

		if (isset($default_domain))
			$this->default_domain = $default_domain;

		if (isset($nest_groups))
			$this->nestGroups = $nest_groups;

		if (isset($validate))
			$this->validate = $validate;

		if (isset($limit))
			$this->limit = $limit;

		$this->structure = array();
		$this->addresses = array();
		$this->error = null;
		$this->index = null;

		while ($this->address = $this->_splitAddresses($this->address)) {
			continue;
		}

		if ($this->address === false || isset($this->error)) {
			return false;
		}

		// Reset timer since large amounts of addresses can take a long time to
		// get here
		set_time_limit (30);

		// Loop through all the addresses
		for ($i = 0; $i < count($this->addresses); $i++) {
			if (($return = $this->_validateAddress($this->addresses[$i])) === false || isset($this->error)) {
				return false;
			}

			if (!$this->nestGroups) {
				$this->structure = array_merge($this->structure, $return);
			} else {
				$this->structure[] = $return;
			}
		}

		return $this->structure;
	}

	/**
 * Splits an address into seperate addresses.
 * 
 * @access private
 * @param string $address The addresses to split.
 * @return boolean Success or failure.
 */
	function _splitAddresses($address) {
		if (!empty($this->limit)AND count($this->addresses) == $this->limit) {
			return '';
		}

		if ($this->_isGroup($address) && !isset($this->error)) {
			$split_char = ';';

			$is_group = true;
		} elseif (!isset($this->error)) {
			$split_char = ',';

			$is_group = false;
		} elseif (isset($this->error)) {
			return false;
		}

		// Split the string based on the above ten or so lines.
		$parts = explode($split_char, $address);
		$string = $this->_splitCheck($parts, $split_char);

		// If a group...
		if ($is_group) {
			// If $string does not contain a colon outside of
			// brackets/quotes etc then something's fubar.

			// First check there's a colon at all:
			if (strpos($string, ':') === false) {
				$this->error = 'Invalid address: ' . $string;

				return false;
			}

			// Now check it's outside of brackets/quotes:
			if (!$this->_splitCheck(explode(':', $string), ':'))
				return false;

			// We must have a group at this point, so increase the counter:
			$this->num_groups++;
		}

		// $string now contains the first full address/group.
		// Add to the addresses array.
		$this->addresses[] = array(
			'address' => trim($string),
			'group' => $is_group
		);

		// Remove the now stored address from the initial line, the +1
		// is to account for the explode character.
		$address = trim(substr($address, strlen($string) + 1));

		// If the next char is a comma and this was a group, then
		// there are more addresses, otherwise, if there are any more
		// chars, then there is another address.
		if ($is_group && substr($address, 0, 1) == ',') {
			$address = trim(substr($address, 1));

			return $address;
		} elseif (strlen($address) > 0) {
			return $address;
		} else {
			return '';
		}

		// If you got here then something's off
		return false;
	}

	/**
 * Checks for a group at the start of the string.
 * 
 * @access private
 * @param string $address The address to check.
 * @return boolean Whether or not there is a group at the start of the string.
 */
	function _isGroup($address) {
		// First comma not in quotes, angles or escaped:
		$parts = explode(',', $address);

		$string = $this->_splitCheck($parts, ',');

		// Now we have the first address, we can reliably check for a
		// group by searching for a colon that's not escaped or in
		// quotes or angle brackets.
		if (count($parts = explode(':', $string)) > 1) {
			$string2 = $this->_splitCheck($parts, ':');

			return ($string2 !== $string);
		} else {
			return false;
		}
	}

	/**
 * A common function that will check an exploded string.
 * 
 * @access private
 * @param array $parts The exloded string.
 * @param string $char  The char that was exploded on.
 * @return mixed False if the string contains unclosed quotes/brackets, or the string on success.
 */
	function _splitCheck($parts, $char) {
		$string = $parts[0];

		for ($i = 0; $i < count($parts); $i++) {
			if ($this->_hasUnclosedQuotes($string) || $this->_hasUnclosedBrackets($string, '<>') || $this->_hasUnclosedBrackets($string, '[]') || $this->_hasUnclosedBrackets($string, '()') || substr($string, -1) == '\\') {
				if (isset($parts[$i + 1])) {
					$string = $string . $char . $parts[$i + 1];
				} else {
					$this->error = 'Invalid address spec. Unclosed bracket or quotes';

					return false;
				}
			} else {
				$this->index = $i;

				break;
			}
		}

		return $string;
	}

	/**
 * Checks if a string has an unclosed quotes or not.
 * 
 * @access private
 * @param string $string The string to check.
 * @return boolean True if there are unclosed quotes inside the string, false otherwise.
 */
	function _hasUnclosedQuotes($string) {
		$string = explode('"', $string);

		$string_cnt = count($string);

		for ($i = 0; $i < (count($string) - 1); $i++)
			if (substr($string[$i], -1) == '\\')
				$string_cnt--;

		return ($string_cnt % 2 === 0);
	}

	/**
 * Checks if a string has an unclosed brackets or not. IMPORTANT:
 * This function handles both angle brackets and square brackets;
 * 
 * @access private
 * @param string $string The string to check.
 * @param string $chars  The characters to check for.
 * @return boolean True if there are unclosed brackets inside the string, false otherwise.
 */
	function _hasUnclosedBrackets($string, $chars) {
		$num_angle_start = substr_count($string, $chars[0]);

		$num_angle_end = substr_count($string, $chars[1]);

		$this->_hasUnclosedBracketsSub($string, $num_angle_start, $chars[0]);
		$this->_hasUnclosedBracketsSub($string, $num_angle_end, $chars[1]);

		if ($num_angle_start < $num_angle_end) {
			$this->error = 'Invalid address spec. Unmatched quote or bracket (' . $chars . ')';

			return false;
		} else {
			return ($num_angle_start > $num_angle_end);
		}
	}

	/**
 * Sub function that is used only by hasUnclosedBrackets().
 * 
 * @access private
 * @param string $string The string to check.
 * @param integer &$num    The number of occurences.
 * @param string $char   The character to count.
 * @return integer The number of occurences of $char in $string, adjusted for backslashes.
 */
	function _hasUnclosedBracketsSub($string, &$num, $char) {
		$parts = explode($char, $string);

		for ($i = 0; $i < count($parts); $i++) {
			if (substr($parts[$i], -1) == '\\' || $this->_hasUnclosedQuotes($parts[$i]))
				$num--;

			if (isset($parts[$i + 1]))
				$parts[$i + 1] = $parts[$i] . $char . $parts[$i + 1];
		}

		return $num;
	}

	/**
 * Function to begin checking the address.
 *
 * @access private
 * @param string $address The address to validate.
 * @return mixed False on failure, or a structured array of address information on success.
 */
	function _validateAddress($address) {
		$is_group = false;

		if ($address['group']) {
			$is_group = true;

			// Get the group part of the name
			$parts = explode(':', $address['address']);
			$groupname = $this->_splitCheck($parts, ':');
			$structure = array();

			// And validate the group part of the name.
			if (!$this->_validatePhrase($groupname)) {
				$this->error = 'Group name did not validate.';

				return false;
			} else {
				// Don't include groups if we are not nesting
				// them. This avoids returning invalid addresses.
				if ($this->nestGroups) {
					$structure = new stdClass;

					$structure->groupname = $groupname;
				}
			}

			$address['address'] = ltrim(substr($address['address'], strlen($groupname . ':')));
		}

		// If a group then split on comma and put into an array.
		// Otherwise, Just put the whole address in an array.
		if ($is_group) {
			while (strlen($address['address']) > 0) {
				$parts = explode(',', $address['address']);

				$addresses[] = $this->_splitCheck($parts, ',');
				$address['address'] = trim(substr($address['address'], strlen(end($addresses). ',')));
			}
		} else {
			$addresses[] = $address['address'];
		}

		// Check that $addresses is set, if address like this:
		// Groupname:;
		// Then errors were appearing.
		if (!isset($addresses)) {
			$this->error = 'Empty group.';

			return false;
		}

		for ($i = 0; $i < count($addresses); $i++) {
			$addresses[$i] = trim($addresses[$i]);
		}

		// Validate each mailbox.
		// Format could be one of: name <geezer@domain.com>
		//                         geezer@domain.com
		//                         geezer
		// ... or any other format valid by RFC 822.
		array_walk($addresses, array(
			$this,
			'validateMailbox'
		));

		// Nested format
		if ($this->nestGroups) {
			if ($is_group) {
				$structure->addresses = $addresses;
			} else {
				$structure = $addresses[0];
			}

		// Flat format
		} else {
			if ($is_group) {
				$structure = array_merge($structure, $addresses);
			} else {
				$structure = $addresses;
			}
		}

		return $structure;
	}

	/**
 * Function to validate a phrase.
 *
 * @access private
 * @param string $phrase The phrase to check.
 * @return boolean Success or failure.
 */
	function _validatePhrase($phrase) {
		// Splits on one or more Tab or space.
		$parts = preg_split('/[ \\x09]+/', $phrase, -1, PREG_SPLIT_NO_EMPTY);

		$phrase_parts = array();

		while (count($parts) > 0) {
			$phrase_parts[] = $this->_splitCheck($parts, ' ');

			for ($i = 0; $i < $this->index + 1; $i++)
				array_shift ($parts);
		}

		for ($i = 0; $i < count($phrase_parts); $i++) {
			// If quoted string:
			if (substr($phrase_parts[$i], 0, 1) == '"') {
				if (!$this->_validateQuotedString($phrase_parts[$i]))
					return false;

				continue;
			}

			// Otherwise it's an atom:
			if (!$this->_validateAtom($phrase_parts[$i]))
				return false;
		}

		return true;
	}

	/**
 * Function to validate an atom which from rfc822 is:
 * atom = 1*<any CHAR except specials, SPACE and CTLs>
 * 
 * If validation ($this->validate) has been turned off, then
 * validateAtom() doesn't actually check anything. This is so that you
 * can split a list of addresses up before encoding personal names
 * (umlauts, etc.), for example.
 * 
 * @access private
 * @param string $atom The string to check.
 * @return boolean Success or failure.
 */
	function _validateAtom($atom) {
		if (!$this->validate) {
			// Validation has been turned off; assume the atom is okay.
			return true;
		}

		// Check for any char from ASCII 0 - ASCII 127
		if (!preg_match('/^[\\x00-\\x7E]+$/i', $atom, $matches)) {
			return false;
		}

		// Check for specials:
		if (preg_match('/[][()<>@,;\\:". ]/', $atom)) {
			return false;
		}

		// Check for control characters (ASCII 0-31):
		if (preg_match('/[\\x00-\\x1F]+/', $atom)) {
			return false;
		}

		return true;
	}

	/**
 * Function to validate quoted string, which is:
 * quoted-string = <"> *(qtext/quoted-pair) <">
 * 
 * @access private
 * @param string $qstring The string to check
 * @return boolean Success or failure.
 */
	function _validateQuotedString($qstring) {
		// Leading and trailing "
		$qstring = substr($qstring, 1, -1);

		// Perform check.
		return !(preg_match('/(.)[\x0D\\\\"]/', $qstring, $matches) && $matches[1] != '\\');
	}

	/**
 * Function to validate a mailbox, which is:
 * mailbox =   addr-spec         ; simple address
 *           / phrase route-addr ; name and route-addr
 * 
 * @access public
 * @param string &$mailbox The string to check.
 * @return boolean Success or failure.
 */
	function validateMailbox(&$mailbox) {
		// A couple of defaults.
		$phrase = '';

		$comment = '';

		// Catch any RFC822 comments and store them separately
		$_mailbox = $mailbox;

		while (strlen(trim($_mailbox)) > 0) {
			$parts = explode('(', $_mailbox);

			$before_comment = $this->_splitCheck($parts, '(');

			if ($before_comment != $_mailbox) {
				// First char should be a (
				$comment = substr(str_replace($before_comment, '', $_mailbox), 1);

				$parts = explode(')', $comment);
				$comment = $this->_splitCheck($parts, ')');
				$comments[] = $comment;

				// +1 is for the trailing )
				$_mailbox = substr($_mailbox, strpos($_mailbox, $comment) + strlen($comment) + 1);
			} else {
				break;
			}
		}

		for ($i = 0; $i < count(@$comments); $i++) {
			$mailbox = str_replace('(' . $comments[$i] . ')', '', $mailbox);
		}

		$mailbox = trim($mailbox);

		// Check for name + route-addr
		if (substr($mailbox, -1) == '>' && substr($mailbox, 0, 1) != '<') {
			$parts = explode('<', $mailbox);

			$name = $this->_splitCheck($parts, '<');

			$phrase = trim($name);
			$route_addr = trim(substr($mailbox, strlen($name . '<'), -1));

			if ($this->_validatePhrase($phrase) === false || ($route_addr = $this->_validateRouteAddr($route_addr)) === false)
				return false;

		// Only got addr-spec
		} else {
			// First snip angle brackets if present.
			if (substr($mailbox, 0, 1) == '<' && substr($mailbox, -1) == '>')
				$addr_spec = substr($mailbox, 1, -1);
			else
				$addr_spec = $mailbox;

			if (($addr_spec = $this->_validateAddrSpec($addr_spec)) === false)
				return false;
		}

		// Construct the object that will be returned.
		$mbox = new stdClass();

		// Add the phrase (even if empty) and comments
		$mbox->personal = $phrase;
		$mbox->comment = isset($comments) ? $comments : array();

		if (isset($route_addr)) {
			$mbox->mailbox = $route_addr['local_part'];

			$mbox->host = $route_addr['domain'];
			$route_addr['adl'] !== '' ? $mbox->adl = $route_addr['adl'] : '';
		} else {
			$mbox->mailbox = $addr_spec['local_part'];

			$mbox->host = $addr_spec['domain'];
		}

		$mailbox = $mbox;
		return true;
	}

	/**
 * This function validates a route-addr which is:
 * route-addr = "<" [route] addr-spec ">"
 *
 * Angle brackets have already been removed at the point of
 * getting to this function.
 * 
 * @access private
 * @param string $route_addr The string to check.
 * @return mixed False on failure, or an array containing validated address/route information on success.
 */
	function _validateRouteAddr($route_addr) {
		// Check for colon.
		if (strpos($route_addr, ':') !== false) {
			$parts = explode(':', $route_addr);

			$route = $this->_splitCheck($parts, ':');
		} else {
			$route = $route_addr;
		}

		// If $route is same as $route_addr then the colon was in
		// quotes or brackets or, of course, non existent.
		if ($route === $route_addr) {
			unset ($route);

			$addr_spec = $route_addr;

			if (($addr_spec = $this->_validateAddrSpec($addr_spec)) === false) {
				return false;
			}
		} else {
			// Validate route part.
			if (($route = $this->_validateRoute($route)) === false) {
				return false;
			}

			$addr_spec = substr($route_addr, strlen($route . ':'));

			// Validate addr-spec part.
			if (($addr_spec = $this->_validateAddrSpec($addr_spec)) === false) {
				return false;
			}
		}

		if (isset($route)) {
			$return['adl'] = $route;
		} else {
			$return['adl'] = '';
		}

		$return = array_merge($return, $addr_spec);
		return $return;
	}

	/**
 * Function to validate a route, which is:
 * route = 1#("@" domain) ":"
 * 
 * @access private
 * @param string $route The string to check.
 * @return mixed False on failure, or the validated $route on success.
 */
	function _validateRoute($route) {
		// Split on comma.
		$domains = explode(',', trim($route));

		for ($i = 0; $i < count($domains); $i++) {
			$domains[$i] = str_replace('@', '', trim($domains[$i]));

			if (!$this->_validateDomain($domains[$i]))
				return false;
		}

		return $route;
	}

	/**
 * Function to validate a domain, though this is not quite what
 * you expect of a strict internet domain.
 *
 * domain = sub-domain *("." sub-domain)
 * 
 * @access private
 * @param string $domain The string to check.
 * @return mixed False on failure, or the validated domain on success.
 */
	function _validateDomain($domain) {
		// Note the different use of $subdomains and $sub_domains                        
		$subdomains = explode('.', $domain);

		while (count($subdomains) > 0) {
			$sub_domains[] = $this->_splitCheck($subdomains, '.');

			for ($i = 0; $i < $this->index + 1; $i++)
				array_shift ($subdomains);
		}

		for ($i = 0; $i < count($sub_domains); $i++) {
			if (!$this->_validateSubdomain(trim($sub_domains[$i])))
				return false;
		}

		// Managed to get here, so return input.
		return $domain;
	}

	/**
 * Function to validate a subdomain:
 *   subdomain = domain-ref / domain-literal
 * 
 * @access private
 * @param string $subdomain The string to check.
 * @return boolean Success or failure.
 */
	function _validateSubdomain($subdomain) {
		if (preg_match('|^\[(.*)]$|', $subdomain, $arr)) {
			if (!$this->_validateDliteral($arr[1]))
				return false;
		} else {
			if (!$this->_validateAtom($subdomain))
				return false;
		}

		// Got here, so return successful.
		return true;
	}

	/**
 * Function to validate a domain literal:
 *   domain-literal =  "[" *(dtext / quoted-pair) "]"
 * 
 * @access private
 * @param string $dliteral The string to check.
 * @return boolean Success or failure.
 */
	function _validateDliteral($dliteral) {
		return !preg_match('/(.)[][\x0D\\\\]/', $dliteral, $matches) && $matches[1] != '\\';
	}

	/**
 * Function to validate an addr-spec.
 *
 * addr-spec = local-part "@" domain
 * 
 * @access private
 * @param string $addr_spec The string to check.
 * @return mixed False on failure, or the validated addr-spec on success.
 */
	function _validateAddrSpec($addr_spec) {
		$addr_spec = trim($addr_spec);

		// Split on @ sign if there is one.
		if (strpos($addr_spec, '@') !== false) {
			$parts = explode('@', $addr_spec);

			$local_part = $this->_splitCheck($parts, '@');
			$domain = substr($addr_spec, strlen($local_part . '@'));

		// No @ sign so assume the default domain.
		} else {
			$local_part = $addr_spec;

			$domain = $this->default_domain;
		}

		if (($local_part = $this->_validateLocalPart($local_part)) === false)
			return false;

		if (($domain = $this->_validateDomain($domain)) === false)
			return false;

		// Got here so return successful.
		return array(
			'local_part' => $local_part,
			'domain' => $domain
		);
	}

	/**
 * Function to validate the local part of an address:
 *   local-part = word *("." word)
 * 
 * @access private
 * @param string $local_part
 * @return mixed False on failure, or the validated local part on success.
 */
	function _validateLocalPart($local_part) {
		$parts = explode('.', $local_part);

		// Split the local_part into words.
		while (count($parts) > 0) {
			$words[] = $this->_splitCheck($parts, '.');

			for ($i = 0; $i < $this->index + 1; $i++) {
				array_shift ($parts);
			}
		}

		// Validate each word.
		for ($i = 0; $i < count($words); $i++) {
			if ($this->_validatePhrase(trim($words[$i])) === false)
				return false;
		}

		// Managed to get here, so return the input.
		return $local_part;
	}

	/**
* Returns an approximate count of how many addresses are
* in the given string. This is APPROXIMATE as it only splits
* based on a comma which has no preceding backslash. Could be
* useful as large amounts of addresses will end up producing
* *large* structures when used with parseAddressList().
*
* @param  string $data Addresses to count
* @return int          Approximate count
*/
	function approximateCount($data) {
		return count(preg_split('/(?<!\\\\),/', $data));
	}

	/**
* This is a email validating function seperate to the rest
* of the class. It simply validates whether an email is of
* the common internet form: <user>@<domain>. This can be
* sufficient for most people. Optional stricter mode can
* be utilised which restricts mailbox characters allowed
* to alphanumeric, full stop, hyphen and underscore.
*
* @param  string  $data   Address to check
* @param  boolean $strict Optional stricter mode
* @return mixed           False if it fails, an indexed array
*                         username/domain if it matches
*/
	function isValidInetAddress($data, $strict = false) {
		$regex = $strict ? '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';

		if (preg_match($regex, trim($data), $matches)) {
			return array(
				$matches[1],
				$matches[2]
			);
		} else {
			return false;
		}
	}
} // class Mail_RFC822

/**
* Filename.......: class.smtp.inc
* Project........: SMTP Class
* Version........: 1.0.5
* Last Modified..: 21 December 2001
*/
define('SMTP_STATUS_NOT_CONNECTED', 1, TRUE);
define('SMTP_STATUS_CONNECTED', 2, TRUE);

class smtp
{
	var $authenticated;

	var $connection;
	var $recipients;
	var $headers;
	var $timeout;
	var $errors;
	var $status;
	var $body;
	var $from;
	var $host;
	var $port;
	var $helo;
	var $auth;
	var $user;
	var $pass;

	/**
* Constructor function. Arguments:
* $params - An assoc array of parameters:
*
*   host    - The hostname of the smtp server		Default: localhost
*   port    - The port the smtp server runs on		Default: 25
*   helo    - What to send as the HELO command		Default: localhost
*             (typically the hostname of the
*             machine this script runs on)
*   auth    - Whether to use basic authentication	Default: FALSE
*   user    - Username for authentication			Default: <blank>
*   pass    - Password for authentication			Default: <blank>
*   timeout - The timeout in seconds for the call	Default: 5
*             to fsockopen()
*/
	function smtp($params = array()) {
		if (!defined('CRLF'))
			define('CRLF', "\r\n", TRUE);

		$this->authenticated = FALSE;
		$this->timeout = 5;
		$this->status = SMTP_STATUS_NOT_CONNECTED;
		$this->host = 'localhost';
		$this->port = 25;
		$this->helo = 'localhost';
		$this->auth = FALSE;
		$this->user = '';
		$this->pass = '';
		$this->errors = array();

		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
* Connect function. This will, when called
* statically, create a new smtp object, 
* call the connect function (ie this function)
* and return it. When not called statically,
* it will connect to the server and send
* the HELO command.
*/
	function &connect($params = array()) {
		if (!isset($this->status)) {
			$obj = new smtp($params);

			if ($obj->connect()) {
				$obj->status = SMTP_STATUS_CONNECTED;
			}

			return $obj;
		} else {
			$this->connection = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

			if (function_exists('socket_set_timeout')) {
				@socket_set_timeout($this->connection, 5, 0);
			}

			$greeting = $this->get_data();

			if (is_resource($this->connection)) {
				$m = $this->auth ? $this->ehlo() : $this->helo();
				if ($this->security == 'tls') {
					$this->starttls();
					$m = $this->auth ? $this->ehlo() : $this->helo();
				}
				return $m;
			} else {
				$this->errors[] = 'Failed to connect to server: ' . $errstr;

				return FALSE;
			}
		}
	}

	/**
* Function which handles sending the mail.
* Arguments:
* $params	- Optional assoc array of parameters.
*            Can contain:
*              recipients - Indexed array of recipients
*              from       - The from address. (used in MAIL FROM:),
*                           this will be the return path
*              headers    - Indexed array of headers, one header per array entry
*              body       - The body of the email
*            It can also contain any of the parameters from the connect()
*            function
*/
	function send($params = array()) {
		foreach ($params as $key => $value) {
			$this->set($key, $value);
		}

		if ($this->is_connected()) {

			// Do we auth or not? Note the distinction between the auth variable and auth() function
			if ($this->auth AND !$this->authenticated) {
				if (!$this->auth())
					return FALSE;
			}

			$this->mail($this->from);

			if (is_array($this->recipients))
				foreach ($this->recipients as $value)
					$this->rcpt($value);
			else
				$this->rcpt($this->recipients);

			if (!$this->data())
				return FALSE;

			// Transparency
			$headers = str_replace(CRLF . '.', CRLF . '..', trim(implode(CRLF, $this->headers)));
			$body = str_replace(CRLF . '.', CRLF . '..', $this->body);
			$body = $body[0] == '.' ? '.' . $body : $body;

			$this->send_data($headers);
			$this->send_data('');
			$this->send_data($body);
			$this->send_data('.');

			$result = (substr(trim($this->get_data()), 0, 3) === '250');
			//$this->rset();
			return $result;
		} else {
			$this->errors[] = 'Not connected!';

			return FALSE;
		}
	}

	/**
* Function to implement HELO cmd
*/
	function helo() {
		if (is_resource($this->connection)AND $this->send_data('HELO ' . $this->helo)AND substr(trim($error = $this->get_data()), 0, 3) === '250') {
			return TRUE;
		} else {
			$this->errors[] = 'HELO command failed, output: ' . trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function to implement EHLO cmd
*/
	function ehlo() {
		if (is_resource($this->connection)AND $this->send_data('EHLO ' . $this->helo)AND substr(trim($error = $this->get_data()), 0, 3) === '250') {
			return TRUE;
		} else {
			$this->errors[] = 'EHLO command failed, output: ' . trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function to implement STARTTLS cmd
*/
	function starttls() {
		if (is_resource($this->connection)AND $this->send_data('STARTTLS')AND strpos($this->get_data(), 'Ready to start TLS') !== false) {
			stream_socket_enable_crypto( $this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT );
			
			return TRUE;
		} else {
			$this->errors[] = 'STARTTLS command failed, output: ' . trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function to implement RSET cmd
*/
	function rset() {
		if (is_resource($this->connection)AND $this->send_data('RSET')AND substr(trim($error = $this->get_data()), 0, 3) === '250') {
			return TRUE;
		} else {
			$this->errors[] = 'RSET command failed, output: ' . trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function to implement QUIT cmd
*/
	function quit() {
		if (is_resource($this->connection)AND $this->send_data('QUIT')AND substr(trim($error = $this->get_data()), 0, 3) === '221') {
			fclose ($this->connection);

			$this->status = SMTP_STATUS_NOT_CONNECTED;
			return TRUE;
		} else {
			$this->errors[] = 'QUIT command failed, output: ' . trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function to implement AUTH cmd
*/
	function auth() {
		if (is_resource($this->connection)AND $this->send_data('AUTH LOGIN')AND substr(trim($error = $this->get_data()), 0, 3) === '334' AND $this->send_data(base64_encode($this->user)) // Send username
		AND substr(trim($error = $this->get_data()), 0, 3) === '334' AND $this->send_data(base64_encode($this->pass)) // Send password
		AND substr(trim($error = $this->get_data()), 0, 3) === '235') {
			$this->authenticated = TRUE;

			return TRUE;
		} else {
			$this->errors[] = 'AUTH command failed: ' . trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function that handles the MAIL FROM: cmd
*/
	function mail($from) {
		if ($this->is_connected()AND $this->send_data('MAIL FROM:<' . $from . '>')AND substr(trim($this->get_data()), 0, 2) === '250') {
			return TRUE;
		} else
			return FALSE;
	}

	/**
* Function that handles the RCPT TO: cmd
*/
	function rcpt($to) {
		if ($this->is_connected()AND $this->send_data('RCPT TO:<' . $to . '>')AND substr(trim($error = $this->get_data()), 0, 2) === '25') {
			return TRUE;
		} else {
			$this->errors[] = trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function that sends the DATA cmd
*/
	function data() {
		if ($this->is_connected()AND $this->send_data('DATA')AND substr(trim($error = $this->get_data()), 0, 3) === '354') {
			return TRUE;
		} else {
			$this->errors[] = trim(substr(trim($error), 3));

			return FALSE;
		}
	}

	/**
* Function to determine if this object
* is connected to the server or not.
*/
	function is_connected() {
		return (is_resource($this->connection)AND ($this->status === SMTP_STATUS_CONNECTED));
	}

	/**
* Function to send a bit of data
*/
	function send_data($data) {
		if (is_resource($this->connection)) {
			return fwrite($this->connection, $data . CRLF, strlen($data) + 2);
		} else
			return FALSE;
	}

	/**
* Function to get data.
*/
	function &get_data() {
		$return = '';

		$line = '';
		$loops = 0;

		if (is_resource($this->connection)) {
			while ((strpos($return, CRLF) === FALSE OR substr($line, 3, 1) !== ' ') AND $loops < 100) {
				$line = fgets($this->connection, 512);

				$return .= $line;
				$loops++;
			}

			return $return;
		} else
			return FALSE;
	}

	/**
* Sets a variable
*/
	function set($var, $value) {
		$this->$var = $value;

		return TRUE;
	}
} // class smtp
