<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn;
use TikiLib, TikiMail;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

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

	private static function getSource(array $acc)
	{
		if ($acc['protocol'] == 'imap') {
			return new Source\Imap($acc['host'], $acc['port'], $acc['username'], $acc['pass']);
		} else {
			return new Source\Pop3($acc['host'], $acc['port'], $acc['username'], $acc['pass']);
		}
	}

	public static function test(array $acc)
	{
		$source = self::getSource($acc);
		return $source->test();
	}

	public static function fromDb(array $acc)
	{
		$account = new self;
		$account->source = self::getSource($acc);

		$wikiParams = [
			'namespace' => $acc['namespace'],
			'structure_routing' => $acc['routing'] == 'y',
		];

		try {
			$container = \TikiInit::getContainer();
			$type = str_replace('-', '', $acc['type']);
			$provider = $container->get("tiki.mailin.provider.{$type}");

			$account->actionFactory = $provider->getActionFactory($acc);
		} catch (ServiceNotFoundException $e) {
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

	private function completeSuccess($message)
	{
		$message->delete();
	}

	private function completeFailure($message)
	{
		if ($this->deleteOnError) {
			$message->delete();
		}
	}

	function isAnyoneAllowed()
	{
		return $this->anonymousAllowed;
	}

	private function canReceive(Source\Message $message)
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

	private function getAction(Source\Message $message)
	{
		return $this->actionFactory->createAction($this, $message);
	}

	private function prepareMessage(Source\Message $message)
	{
		// TODO : This is rather primitive and implies we control the message source, need to make smarter

		if ($this->discardAfter) {
			$this->discard($message, $this->discardAfter);
		}

		$this->discard($message, '<div class="gmail_quote">');
		$this->discard($message, '<div class="gmail_extra">');
	}

	private function discard($message, $delimitor)
	{
		$body = $message->getBody();
		$pos = strpos($body, $delimitor);
		if ($pos !== false) {
			$body = substr($body, 0, $pos);
			$message->setBody($body);
		}

		$body = $message->getHtmlBody(false);
		$pos = strpos($body, $delimitor);
		if ($pos !== false) {
			$body = substr($body, 0, $pos);
			$message->setHtmlBody($body);
		}
	}

	function sendFailureResponse(Source\Message $message, $condition)
	{
		global $prefs;
		$l = $prefs['language'];

		$mail = $this->getReplyMail($message);
		$pre = tra('Mail-in auto-reply', $l) . "\n\n";

		if ($condition == 'cant_use') {
			$mail->setText($pre . tra("Sorry, you can't use this feature.", $l));
		} elseif ($condition == 'disabled') {
			$mail->setText($pre . tra("The functionality you are trying to access is currently disabled.", $l));
		} elseif ($condition == 'permission_denied') {
			$mail->setText($pre . tra("Permission denied.", $l));
		} elseif ($condition == 'nothing_to_do') {
			$mail->setText($pre . tra("No required action found.", $l));
		}

		$this->sendFailureReply($message, $mail);
	}

	function getReplyMail(Source\Message $message)
	{
		require_once 'lib/webmail/tikimaillib.php';
		$mail = new TikiMail();
		$mail->setFrom($this->accountAddress);
		$mail->setHeader('In-Reply-To', "<{$message->getMessageId()}>");
		$mail->setSubject("RE: {$message->getSubject()}");

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

	function parseBody($body, $canAllowHtml = true)
	{
		global $prefs;

		$is_html = false;
		$wysiwyg = NULL;
		if ($this->containsStringHTML($body)) {
			$is_html = true;
			$wysiwyg = 'y';
		}

		if ($is_html && $this->saveHtml && $canAllowHtml) {
			// Keep HTML setting. Always save as HTML
		} elseif ($prefs['feature_wysiwyg'] === 'y' && $prefs['wysiwyg_default'] === 'y' && $prefs['wysiwyg_htmltowiki'] !== 'y'  && $canAllowHtml) {
			// WYSIWYG HTML editor is active
			$is_html = true;
			$wysiwyg = 'y';
		} elseif ($is_html) {
			$editlib = TikiLib::lib('edit');
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

	function check()
	{
		global $prefs;

		$logs = TikiLib::lib('logs');
		$messages = $this->source->getMessages();

		foreach ($messages as $message) {
			$success = false;

			if (! $this->canReceive($message)) {
				$this->sendFailureResponse($message, 'cant_use');
				$this->log($message, tr("Rejected message, user globally denied"));
			} elseif ($action = $this->getAction($message)) {
				$context = new \Perms_Context($message->getAssociatedUser());
				if (! $action->isEnabled()) {
					// Action configured, but not enabled
					$this->log($message, tr("Rejected message, associated action disabled (%0)", $action->getName()));
					$this->sendFailureResponse($message, 'disabled');
				} elseif ($this->isAnyoneAllowed() || $action->isAllowed($this, $message)) {
					$this->prepareMessage($message);
					$success = $action->execute($this, $message);
					$this->log($message, tr("Performing action (%0)", $action->getName()));
				} else {
					$this->sendFailureResponse($message, 'permission_denied');
					$this->log($message, tr("Rejected message, user locally denied (%0)", $action->getName()));
				}

				unset($context);
			} else {
				
				$success = false;

				$this->sendFailureResponse($message, 'nothing_to_do');
				$this->log($message, tr("Rejected message, no associated action."));
			}

			if ($success) {
				$this->completeSuccess($message);
			} else {
				$this->completeFailure($message);
			}
		}
	}

	private function log(Source\Message $message, $detail)
	{
		$lib = TikiLib::lib('logs');
		$lib->add_log('mailin', $detail . ' - ' . $message->getSubject(), $message->getAssociatedUser());
	}
}

