<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Source;
use Tiki\MailIn\Exception\TransportException;
use Zend\Mail\Storage\Pop3 as ZendPop3;
use Zend\Mail\Exception\ExceptionInterface as ZendMailException;

class Pop3 implements SourceInterface
{
	protected $host;
	protected $port;
	protected $username;
	protected $password;

	function __construct($host, $port, $username, $password)
	{
		$this->host = $host;
		$this->port = (int) $port;
		$this->username = $username;
		$this->password = $password;
	}

	function test()
	{
		try {
			$pop = $this->connect();
			$pop->close();

			return true;
		} catch (TransportException $e) {
			return false;
		}
	}

	/**
	 * @return \Generator
	 * @throws TransportException
	 */
	function getMessages()
	{
		$pop = $this->connect();
		$toDelete = [];

		foreach ($pop as $i => $source) {
			/* @var $source \Zend\Mail\Storage\Message */
			$message = new Message($i, function () use ($i, & $toDelete) {
				$toDelete[] = $i;
			});
			$from = $source->from ?: $source->{'return-path'};
			$message->setMessageId(str_replace(['<', '>'], '', $source->{'message-id'}));
			$message->setRawFrom($from);
			$message->setSubject($source->subject);
			$message->setRecipient($source->to);
			$message->setHtmlBody($this->getBody($source, 'text/html'));
			$message->setBody($this->getBody($source, 'text/plain'));

			$this->handleAttachments($message, $source);

			yield $message;
		}

		// Due to an issue in Zend_Mail_Storage, deletion must be done in reverse order
		$toDelete = array_reverse($toDelete);

		foreach ($toDelete as $i) {
			$pop->removeMessage($i);
		}

		$pop->close();
	}

	/**
	 * @return \Zend\Mail\Storage\Pop3
	 * @throws TransportException
	 */
	protected function connect()
	{
		try {
			$pop = new ZendPop3([
				'host' => $this->host,
				'port' => $this->port,
				'user' => $this->username,
				'password' => $this->password,
				'ssl' => $this->port == 995,
			]);

			return $pop;
		} catch (ZendMailException $e) {
			throw new TransportException(tr("Login failed for POP3 account on %0:%1 for user %2", $this->host, $this->password, $this->username));
		}
	}

	/**
	 * @param $part \Zend\Mail\Storage\Message
	 * @param $type string
	 * @return string
	 */
	private function getBody($part, $type)
	{
		if (! $part->isMultipart() && 0 === strpos($part->getHeaders()->get('Content-Type'), $type)) {
			return $this->decode($part);
		}

		if ($part->isMultipart()) {
			for ($i = 1; $i <= $part->countParts(); ++$i) {
				$p = $part->getPart($i);
				if ($ret = $this->getBody($p, $type)) {
					return $ret;
				}
			}
		}
	}

	/**
	 * @param $message \Tiki\MailIn\Source\Message
	 * @param $part \Zend\Mail\Storage\Message
	 */
	private function handleAttachments($message, $part)
	{
		$type = $part->getHeaders()->get('Content-Type');
		if (0 === strpos($type, 'multipart/mixed') || 0 === strpos($type, 'multipart/related')) {
			// Skip initial content
			for ($i = 2; $i <= $part->countParts(); ++$i) {
				$p = $part->getPart($i);
				if ($p->isMultipart()) {
					continue;
				}
				$headers = $p->getHeaders()->toArray();

				if (isset($headers['content-id'])) {
					$contentId = $headers['content-id'];
					$contentId = str_replace("<", "", $contentId);
					$contentId = str_replace(">", "", $contentId);
				} elseif (isset($headers['x-attachment-id'])) {
					$contentId = $headers['x-attachment-id'];
				} else {
					$contentId = uniqid();
				}
				$fileName = '';
				$fileType = '';
				$fileData = $this->decode($p);
				$fileSize = function_exists('mb_strlen') ? mb_strlen($fileData, '8bit') : strlen($fileData);

				if (isset($headers['content-type'])) {
					$type = $headers['content-type'];
					$pos = strpos($type, ';');
					if ($pos === false) {
						$fileType = $type;
					} else {
						$fileType = substr($type, 0, $pos);
					}

					if (preg_match('/name="([^"]+)"/', $type, $parts)) {
						$fileName = $parts[1];
					}
				}

				if (! $fileName && isset($headers['content-disposition'])) {
					$dispo = $headers['content-disposition'];
					if (preg_match('/name="([^"]+)"/', $dispo, $parts)) {
						$fileName = $parts[1];
					}
				}

				$message->addAttachment($contentId, $fileName, $fileType, $fileSize, $fileData);
			}
		}
	}

	/**
	 * @param $part \Zend\Mail\Storage\Message
	 * @return string
	 */
	private function decode($part)
	{
		$content = $part->getContent();
		if (isset($part->{'content-transfer-encoding'})) {
			switch ($part->{'content-transfer-encoding'}) {
			case 'base64':
				$content = base64_decode($content);
				break;
			case 'quoted-printable':
				$content = quoted_printable_decode($content);
				break;
			}
		}

		if (isset($part->{'content-type'})) {
			if (preg_match('/charset="?iso-8859-1"?/i', $part->{'content-type'})) {
				$content = utf8_encode($content); //convert to utf8
			}
		}

		return $content;
	}
}
