<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Manage users preferences regarding periodic
 * reports of what have changed in Tiki.
 *
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Users
{
	/**
	 * @var TikiDb
	 */
	protected $db;

	protected $table;

	/**
	 * @var DateTime
	 */
	protected $dt;
	/**
	 * @param TikiDb $db
	 * @return null
	 */
	public function __construct(TikiDb $db, DateTime $dt)
	{
		$this->db = $db;
		$this->table = $db->table('tiki_user_reports');
		$this->dt = $dt;
	}

	/**
	 * Return the preferences for receiving the reports
	 * for a given user.
	 *
	 * @param string $user
	 * @return array
	 */
	public function get($user)
	{
		return $this->table->fetchRow(
			array('id', 'interval', 'view', 'type', 'always_email', 'last_report'),
			array('user' => $user)
		);
	}

	/**
	 * Remove user preferences for reports.
	 *
	 * @param string $user
	 * @return null
	 */
	public function delete($user)
	{
		$this->table->deleteMultiple(array('user' => $user));
	}

	/**
	 * Add or update user preferences regarding receiving periodic
	 * reports with changes in Tiki.
	 *
	 * @param string $user
	 * @param string $interval report interval (can be 'daily', 'weekly' and 'monthly')
	 * @param string $view
	 * @param string $type whether the report should be send in plain text or html
	 * @param bool $always_email if true the user will receive an e-mail even if there are no changes
	 * @return null
	 */
	public function save($user, $interval, $view, $type, $always_email = 0)
	{
		if (!$this->get($user)) {
			$this->table->insert(
				array(
					'user' => $user,
					'interval' => $interval,
					'view' => $view,
					'type' => $type,
					'always_email' => $always_email,
					'last_report' => '0000-00-00 00:00:00',
				)
			);
		} else {
			$this->table->update(
				array(
					'interval' => $interval,
					'view' => $view,
					'type' => $type,
					'always_email' => $always_email
				),
				array('user' => $user)
			);
		}
	}

	/**
	 * Called by event tiki.user.create when feature
	 * dailyreports_enabled_for_new_users is enabled.
	 *
	 * @param $context
	 * @return null
	 */
	public function addUserToDailyReports($context)
	{
		$user = isset($context['user']) ? $context['user'] : $context['object'];
		$this->save($user, 'daily', 'detailed', 'html', 0);
	}

	/**
	 * Return a list of users that should receive the report.
	 * @return array
	 */
	public function getUsersForReport()
	{
		$users = $this->db->fetchAll('select `user`, `interval`, UNIX_TIMESTAMP(`last_report`) as last_report from tiki_user_reports');

		$ret = array();

		foreach ($users as $user) {
			if ($user['interval'] == "minute" && ($user['last_report'] + 60) <= $this->dt->format('U')) {
				$ret[] = $user['user'];
			}
			if ($user['interval'] == "hourly" && ($user['last_report'] + 3600) <= $this->dt->format('U')) {
				$ret[] = $user['user'];
			}
			if ($user['interval'] == "daily" && ($user['last_report'] + 86400) <= $this->dt->format('U')) {
				$ret[] = $user['user'];
			}
			if ($user['interval'] == "weekly" && ($user['last_report'] + 604800) <= $this->dt->format('U')) {
				$ret[] = $user['user'];
			}
			if ($user['interval'] == "monthly" && ($user['last_report'] + 2419200) <= $this->dt->format('U')) {
				$ret[] = $user['user'];
			}
		}

		return $ret;
	}

	/**
	 * Return all users that are using periodic reports.
	 * @return array a list of users names
	 */
	public function getAllUsers()
	{
		return $this->table->fetchColumn('user', array());
	}

	/**
	 * Update date and time of last report sent
	 * to the user.
	 * @param strin $user
	 * @return null
	 */
	function updateLastReport($user)
	{
		$this->table->update(
			array('last_report' => $this->dt->format('Y-m-d H:i:s')),
			array('user' => $user)
		);
	}
}
