<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn;

class Account
{
	private $source;
	private $actionFactory;

	private $accountAddress;
	private $anonymousAllowed;
	private $adminAllowed;
	private $sendResponses;
	private $discardAfter;

	public static function fromDb(array $acc)
	{
		$account = new self;
		$account->source = new Source\Pop3($acc['pop'], $acc['port'], $acc['username'], $acc['pass']);

		switch ($acc['type']) {
		case 'article-put':
			$this->factory = new Action\DirectFactory('Tiki\MailIn\Action\ArticlePut', array(
				'topic' => $acc['article_topicId'],
				'type' => $acc['article_type'],
			));
			break;
		case 'wiki-put':
			$this->factory = new Action\DirectFactory('Tiki\MailIn\Action\WikiPut');
			break;
		case 'wiki-get':
			$this->factory = new Action\DirectFactory('Tiki\MailIn\Action\WikiGet');
			break;
		case 'wiki-append':
			$this->factory = new Action\DirectFactory('Tiki\MailIn\Action\WikiAppend');
			break;
		case 'wiki-prepend':
			$this->factory = new Action\DirectFactory('Tiki\MailIn\Action\WikiPrepend');
			break;
		case 'wiki':
			$this->factory = new Action\SubjectPrefixFactory(array(
				'GET:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiGet'),
				'APPEND:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiAppend'),
				'PREPEND:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPrepend'),
				'PUT:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPut'),
				'' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPut'),
			));
			break;
		default:
			throw new Exception\MailInException("Action factory not found.");
		}

		$account->accountAddress = $acc['account'];
		$account->anonymousAllowed = $acc['anonymous'] == 'y';
		$account->adminAllowed = $acc['admin'] == 'y';
		$account->sendResponses = $acc['respond_email'] == 'y';
		$account->discardAfter = $acc['discard_after'];

		return $account;
	}

	private function __construct()
	{
	}

	function getMessages()
	{
		return $this->source->getMessage();
	}

	function isAnyoneAllowed()
	{
		return $this->anonymous;
	}

	function canReceive(Source\Message $message)
	{
		$user = $message->getAssociatedUser();
		$perms = TikiLib::lib('tiki')->get_user_permission_accessor($user, null, null);

		if (! $user) {
			return $this->anonymousAllowed;
		} elseif ($perms->admin) {
			return $this->adminAllowed;
		} else {
			$userlib = TikiLib::lib('user');
			return $perms->send_mailin;
		}
	}

	function getAction(Source\Message $message)
	{
		return $this->actionFactory->createAction($this, $message);
	}

	function prepareMessage(Source\Message $message)
	{
		// TODO : This is rather primitive and implies we control the message source, need to make smarter

		if ($this->discardAfter) {
			$body = $message->getBody();
			$pos = strpos($body, $this->discardAfter);
			if ($pos !== false) {
				$body = substr($body, 0, $pos);
				$message->setBody($body);
			}
		}
	}

	function sendFailureResponse(Source\Message $message)
	{
		if (! $this->sendResponses) {
			return;
		}

		global $prefs;
		$l = $prefs['language'];

		$mail = new TikiMail();
		$mail->setFrom($this->accountAddress);
		$mail->setSubject(tra('Tiki mail-in auto-reply', $l));
		$mail->setText(tra("Sorry, you can't use this feature.", $l));
		$mail->send(array($message->getFromAddress()), 'mail');
	}
}

