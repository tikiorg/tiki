<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Action;
use Tiki\MailIn\Account;
use Tiki\MailIn\Source\Message;
use Tiki\MailIn\Exception\MailInException;

class RecipientPlaceholderFactory implements FactoryInterface
{
	private $actionMap;

	function __construct(array $actionMap)
	{
		global $prefs;

		$pattern = $prefs['monitor_reply_email_pattern'];
		$pattern = preg_quote($pattern, '/');
		$pattern = str_replace('PLACEHOLDER', '(?P<DATA>.+)', $pattern);
		$pattern = "/$pattern/";

		$this->pattern = $pattern;
		$this->actionMap = $actionMap;
	}

	function createAction(Account $account, Message $message)
	{
		if (preg_match($this->pattern, $message->getRecipient(), $parts) && isset($parts['DATA'])) {
			$info = \Tiki_Security::get()->decode($parts['DATA']);

			// Not a signed value
			if (! $info) {
				return null;
			}

			$action = $info['a'];

			// Real user part of the signature, no need to rely on the email
			// address.
			if (isset($info['u'])) {
				$message->setAssociatedUser($info['u']);
			}

			if (isset($this->actionMap[$action])) {
				$class = $this->actionMap[$action];
				return new $class([
					'type' => $info['t'],
					'object' => $info['o'],
				]);
			}
		}
	}
}

