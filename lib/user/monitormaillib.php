<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class MonitorMailLib
{
	private $mailQueue = [];

	function queue($event, array $args, array $sendTo)
	{
		$recipients = $this->getRecipients($sendTo);

		$this->mailQueue[] = [
			'event' => $event,
			'args' => $args,
			'recipients' => $recipients,
		];
	}

	function sendQueue()
	{
		foreach ($this->mailQueue as $mail) {
			foreach ($mail['recipients'] as $recipient) {
				$this->sendMail($recipient['login'], $recipient['email'], $recipient['language'], $mail);
			}
		}

		$this->mailQueue = [];
	}

	function sendDigest($info, $from, $to)
	{
		global $prefs;
		$out = false;
		$servicelib = TikiLib::lib('service');

		\TikiLib::setExternalContext(true);
		// Override the user until the end of the function
		$context = new Perms_Context($info['login']);
		$prefs['language'] = $info['language'];

		try {
			$html = $servicelib->render('monitor', 'stream', [
				'high' => 1,
				'from' => $from,
				'to' => $to,
			]);

			$html = $this->applyStyle($html);
			$title = tr('Notification Digest');

			$this->send($info['email'], $title, $html);

			$out = true;
		} catch (Services_Exception $e) {
		}

		unset($context);
		\TikiLib::setExternalContext(false);
		return $out;
	}

	/**
	 * Ontain the list of email addresses and preferred language for each
	 * user id to whom the notification email must be sent.
	 */
	private function getRecipients($sendTo)
	{
		global $prefs;
		$db = TikiDb::get();
		$bindvars = [$prefs['site_language']];
		$condition = $db->in('userId', $sendTo, $bindvars);

		$result = $db->fetchAll("
			SELECT login, email, IFNULL(p.value, ?) language
			FROM users_users u
				LEFT JOIN tiki_user_preferences p ON u.login = p.user AND p.prefName = 'language'
			WHERE $condition
		", $bindvars);

		return $result;
	}

	private function renderTitle($language, $mail)
	{
		// FIXME : Needs a better title
		return tra('Notification', $language);
	}

	/**
	 * Renders the body of the email and inline any applicable CSS.
	 */
	private function renderContent($language, $mail)
	{
		$smarty = TikiLib::lib('smarty');
		$activity = $mail['args'];
		$activity['event_type'] = $mail['event'];
		$smarty->assign('monitor', $activity);
		TikiLib::setExternalContext(true);
		$html = $smarty->fetchLang($language, 'monitor/notification_email_body.tpl');
		TikiLib::setExternalContext(false);
		return $this->applyStyle($html);
	}

	private function collectCss()
	{
		static $css;
		if ($css) {
			return $css;
		}

		$cachelib = TikiLib::lib('cache');
		if ($css = $cachelib->getCached('email_css')) {
			return $css;
		}

		$headerlib = TikiLib::lib('header');
		$files = $headerlib->get_css_files();
		$contents = array_map(function ($file) {
			if ($file{0} == '/') {
				return file_get_contents($file);
			} elseif (substr($file, 0, 4) == 'http') {
				return TikiLib::lib('tiki')->httprequest($file);
			} else {
				return file_get_contents(TIKI_PATH . '/' . $file);
			}
		}, $files);

		$css = implode("\n\n", $contents);
		$cachelib->cacheItem('email_css', $css);
		return $css;
	}

	private function sendMail($user, $email, $language, $mail)
	{
		// Override the user until the end of the function
		$context = new Perms_Context($user);

		$title = $this->renderTitle($language, $mail);
		$html = $this->renderContent($language, $mail);

		$this->send($email, $title, $html, $mail['args']);

		unset($context);
	}

	private function send($email, $title, $html, $args = [])
	{
		global $prefs;

		require_once 'lib/webmail/tikimaillib.php';
		$mail = new TikiMail;
		$mail->setSubject($title);
		$mail->setHtml($html);

		if (! empty($prefs['monitor_reply_email_pattern']) && isset($args['reply_action'], $args['type'], $args['object'])) {
			$data = Tiki_Security::get()->encode([
				'u' => $GLOBALS['user'],
				'a' => $args['reply_action'],
				't' => $args['type'],
				'o' => $args['object'],
			]);
			$reply = str_replace('PLACEHOLDER', $data, $prefs['monitor_reply_email_pattern']);
			$name = tr("%0 Reply Handler", $prefs['sitetitle']);
			$mail->setReplyTo($reply, $name);
		}

		$mail->send($email);
	}

	private function applyStyle($html)
	{
		$css = $this->collectCss();

		$processor = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles($html, $css);

		$html = $processor->convert();
		return $html;
	}
}

