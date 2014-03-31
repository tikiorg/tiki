<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn;
use TikiLib, TikiMail;

class Account
{
	private $source;
	private $actionFactory;

	private $accountAddress;
	private $anonymousAllowed;
	private $adminAllowed;
	private $sendResponses;
	private $discardAfter;
	private $defaultCategory;
	private $saveHtml;
	private $auto_attachments;

	public static function fromDb(array $acc)
	{
		$account = new self;
		$account->source = new Source\Pop3($acc['pop'], $acc['port'], $acc['username'], $acc['pass']);

		$wikiParams = [
			'namespace' => $acc['namespace'],
			'structure_routing' => $acc['routing'] == 'y',
		];

		switch ($acc['type']) {
		case 'article-put':
			$account->actionFactory = new Action\DirectFactory('Tiki\MailIn\Action\ArticlePut', array(
				'topic' => $acc['article_topicId'],
				'type' => $acc['article_type'],
			));
			break;
		case 'wiki-put':
			$account->actionFactory = new Action\DirectFactory('Tiki\MailIn\Action\WikiPut', $wikiParams);
			break;
		case 'wiki-get':
			$account->actionFactory = new Action\DirectFactory('Tiki\MailIn\Action\WikiGet', $wikiParams);
			break;
		case 'wiki-append':
			$account->actionFactory = new Action\DirectFactory('Tiki\MailIn\Action\WikiAppend', $wikiParams);
			break;
		case 'wiki-prepend':
			$account->actionFactory = new Action\DirectFactory('Tiki\MailIn\Action\WikiPrepend', $wikiParams);
			break;
		case 'wiki':
			$account->actionFactory = new Action\SubjectPrefixFactory(array(
				'GET:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiGet', $wikiParams),
				'APPEND:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiAppend', $wikiParams),
				'PREPEND:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPrepend', $wikiParams),
				'PUT:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPut', $wikiParams),
				'' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPut', $wikiParams),
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
		$account->defaultCategory = $acc['categoryId'];
		$account->saveHtml = $acc['save_html'] == 'y';
		$account->deleteOnError = $acc['leave_email'] != 'y';
		$account->auto_attachments = $acc['attachments'] == 'y';
		$account->inline_attachments = $acc['show_inlineImages'] == 'y';

		return $account;
	}

	private function __construct()
	{
	}

	function getMessages()
	{
		return $this->source->getMessages();
	}

	function completeSuccess($message)
	{
		$message->delete();
	}

	function completeFailure($message)
	{
		if ($this->deleteOnError) {
			$message->delete();
		}
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

			$body = $message->getHtmlBody(false);
			$pos = strpos($body, $this->discardAfter);
			if ($pos !== false) {
				$body = substr($body, 0, $pos);
				$message->setHtmlBody($body);
			}
		}
	}

	function sendFailureResponse(Source\Message $message)
	{
		global $prefs;
		$l = $prefs['language'];

		$mail = $this->getReplyMail($message);
		$mail->setSubject(tra('Tiki mail-in auto-reply', $l));
		$mail->setText(tra("Sorry, you can't use this feature.", $l));
		$this->sendFailureReply($message, $mail);
	}

	function getReplyMail(Source\Message $message)
	{
		$mail = new TikiMail();
		$mail->setFrom($this->accountAddress);

		return $mail;
	}

	function getAddress()
	{
		return $this->accountAddress;
	}

	function sendFailureReply(Source\Message $message, TikiMail $mail)
	{
		if ($this->sendResponses) {
			$this->sendReply($message, $mail);
		}
	}


	function sendReply(Source\Message $message, TikiMail $mail)
	{
		$mail->send(array($message->getFromAddress()), 'mail');
	}

	function getDefaultCategory()
	{
		return $this->defaultCategory;
	}

	function parseBody($body)
	{
		global $prefs;

		$is_html = false;
		$wysiwyg = NULL;
		if ($this->containsStringHTML($body)) {
			$is_html = true;
			$wysiwyg = 'y';
		}

		if ($is_html && $this->saveHtml) {
			// Keep HTML setting. Always save as HTML
		} elseif ($prefs['feature_wysiwyg'] === 'y' && $prefs['wysiwyg_default'] === 'y' && $prefs['wysiwyg_htmltowiki'] !== 'y' ) {
			// WYSIWYG HTML editor is active
			$is_html = true;
			$wysiwyg = 'y';
		} elseif ($is_html) {
			include_once "lib/wiki/editlib.php";
			$editlib = new EditLib;
			$body = $editlib->parseToWiki($body);
			$is_html = false;
			$wysiwyg = NULL;
		}

		return array(
			'body' => $body,
			'is_html' => $is_html,
			'wysiwyg' => $wysiwyg,
		);
	}

	private function containsStringHTML($str)
	{
		return preg_match('/<[^>]*>/', $str);
	}

	function hasAutoAttach()
	{
		return $this->auto_attachments;
	}

	function hasInlineAttach()
	{
		return $this->inline_attachments;
	}
}

