<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Send e-mail reports to users with changes in Tiki
 * in a given period of time.
 *
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Send
{
	protected $dt;

	protected $mail;

	protected $builder;

	/**
	 * @param DateTime $dt
	 * @param TikiMail $mail
	 * @param Reports_Send_EmailBuilder $builder
	 * @param array $tikiPrefs
	 * @return null
	 */
	public function __construct(DateTime $dt, TikiMail $mail, Reports_Send_EmailBuilder $builder, array $tikiPrefs)
	{
		$this->dt = $dt;
		$this->mail = $mail;
		$this->builder = $builder;
		$this->tikiPrefs = $tikiPrefs;
	}

	public function sendEmail($userData, $reportPreferences, $reportCache)
	{
		global $prefs;

		$tikilib = TikiLib::lib('tiki');

		$lgSave  = $prefs['language'];
		$prefs['language'] = $tikilib->get_user_preference($userData['login'], "language"); //Change language according to user's prefs.

		$mailData = $this->builder->emailBody($userData, $reportPreferences, $reportCache);

		$this->mail->setUser($userData['login']);

		$this->setSubject($reportCache);

		if ($reportPreferences['type'] == 'plain') {
			$this->mail->setText($mailData);
		} else {
			$this->mail->setHtml($mailData);
		}

		$this->mail->send(array($userData['email']));

		$prefs['language'] = $lgSave;  //Restore language settings
	}

	protected function setSubject($reportCache)
	{
		$subject = tr(
			'Report on %0 from %1 ',
			$this->tikiPrefs['browsertitle'],
			TikiLib::date_format($this->tikiPrefs['short_date_format'], $this->dt->format('U'))
		);
		if (!is_array($reportCache)) {
			$subject .= tr('(no changes)');
		} elseif (count($reportCache) == 1) {
			$subject .= tr('(1 change)');
		} else {
			$subject .= tr('(%0 changes)', count($reportCache));
		}
		$this->mail->setSubject($subject);
	}
}
