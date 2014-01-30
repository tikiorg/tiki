<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

		foreach ($recipients as $recipient) {
			$key = "{$args['EVENT_ID']}-{$recipient['language']}";

			if (! isset($this->mailQueue[$key])) {
				$this->mailQueue[$key] = ['language' => $recipient['language'], 'event' => $event, 'args' => $args, 'emails' => []];
			}

			$this->mailQueue[$key]['emails'][] = $recipient['email'];
		}
	}

	function sendQueue()
	{
		foreach ($this->mailQueue as $mail) {
			$title = $this->renderTitle($mail);
			$content = $this->renderContent($mail);
			foreach ($mail['emails'] as $email) {
				$this->sendMail($email, $title, $content);
			}
		}

		$this->mailQueue = [];
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
			SELECT email, IFNULL(p.value, ?) language
			FROM users_users u
				LEFT JOIN tiki_user_preferences p ON u.login = p.user AND p.prefName = 'language'
			WHERE $condition
		", $bindvars);

		return $result;
	}

	private function renderTitle($mail)
	{
		// FIXME : Needs a better title
		return tra('Notification', $mail['language']);
	}

	/**
	 * Renders the body of the email and inline any applicable CSS.
	 */
	private function renderContent($mail)
	{
		$smarty = TikiLib::lib('smarty');
		$activity = $mail['args'];
		$activity['event_type'] = $mail['event'];
		$smarty->assign('monitor', $activity);
		TikiLib::setExternalContext(true);
		$html = $smarty->fetchLang($mail['language'], 'monitor/notification_email_body.tpl');
		TikiLib::setExternalContext(false);
		$css = $this->collectCss();

		$processor = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles($html, $css);

		$html = $processor->convert();
		return $html;
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

	private function sendMail($email, $title, $html)
	{
		require_once 'lib/webmail/tikimaillib.php';
		$mail = new TikiMail;
		$mail->setSubject($title);
		$mail->setHtml($html);
		$mail->send($email);
	}
}

