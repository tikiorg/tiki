<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Source;

class Pop3 implements SourceInterface
{
	private $host;
	private $port;
	private $username;
	private $password;

	function __construct($host, $port, $username, $password)
	{
		$this->host = $host;
		$this->port = (int) $port;
		$this->username = $username;
		$this->password = $password;
	}

	function getMessages()
	{
		$pop = $this->connect();

		$messageCount = $pop->numMsg();

		$mimelib = new mime();

		for ($i = 1; $i <= $messageCount; ++$i) {
			$headers = $this->getParsedHeaders($i);
			$body = $pop3->getMsg($i);

			if (! $headers) {
				continue; // Headers not parsable, skip message
			}

			$info = $mimelib->decode($message);

			$message = new Message($i, function () use ($pop, $i) {
				$pop->deleteMsg($i);
			});
			$message->setMessageId(preg_replace(['<', ']'], '', $aux['Message-ID']));
			$message->setRawFrom(isset($aux['From']) ? $aux['From'] : $aux['Return-path']);
			$message->setSubject($info['header']['subject']);
			$message->setBody($this->getBody($body));
		}

		$pop->disconnect();
	}

	private function connect()
	{
		$pop = new Net_Pop3;

		if (! $pop->connect($this->host, $this->port)) {
			throw new TransportException(tr("Failed to connect to POP3 account on %0:%1", $this->host, $this->port));
		}

		if (false === $pop->login($this->username, $this->password, "USER")) {
			throw new TransportException(tr("Login failed for POP3 account on %0:%1 for user %2", $this->host, $this->password, $this->username));
		}

		return $pop;
	}

	private function getBody($output)
	{
		if (isset($output['text'][0])) {
			$body = $output["text"][0];
		} elseif (isset($output['parts'][0])) {
			$body = $this->getBody($output['parts'][0]);
		} else {
			$body = '';
		}

		return $body;
	}
}
