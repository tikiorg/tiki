<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Main class to manage the functionality
 * of the periodic reports.
 * 
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Manager
{
	protected $reportsUsers;
	
	protected $reportsCache;
	
	protected $reportsSend;
	
	protected $userlib;
	
	public function __construct(Reports_Users $reportsUsers, Reports_Cache $reportsCache, Reports_Send $reportsSend, UsersLib $userlib)
	{
		$this->reportsUsers = $reportsUsers;
		$this->reportsCache = $reportsCache;
		$this->reportsSend = $reportsSend;
		$this->userlib = $userlib;
	}
	
	/**
	 * Send report to subscribed users.
	 * @return null
	 */
	public function send()
	{
		$users = $this->reportsUsers->getUsersForReport();
		
		foreach ($users as $user) {
			$userReportPreferences = $this->reportsUsers->get($user);
			$userData = $this->userlib->get_user_info($user);
		
			// if email address isn't set, do nothing but clear the cache
			if (!empty($userData['email'])) {
				$cache = $this->reportsCache->get($user);
				
				if (!empty($cache) || $userReportPreferences['always_email']) {
					$this->reportsSend->sendEmail($userData, $userReportPreferences, $cache);
					$this->reportsUsers->updateLastReport($userData['login']);
				}
			}

			$this->reportsCache->delete($userData['login']);
		}
	}
	
	/**
	 * Remove user preferences for reports and the
	 * changes cache for this user.
	 * 
	 * @param string $user user name
	 * @return null
	 */
	public function delete($user)
	{
		$this->reportsUsers->delete($user);
		$this->reportsCache->delete($user);
	}
	
	/**
	 * @see Reports_Users::save()
	 * @param string $user
	 * @param string $interval
	 * @param string $view
	 * @param string $type
	 * @param int $always_email
	 * @return null
	 */
	public function save($user, $interval, $view, $type, $always_email)
	{
		$this->reportsUsers->save($user, $interval, $view, $type, $always_email);
	}
	
	/**
	 * Add a new event to the periodic reports cache instead
	 * of sending an notification e-mail to the users.
	 * 
	 * @param array $watches a list of users watching the changed object and some information about the object itself
	 * @param array $data information about the changed object
	 * @return null
	 */
	public function addToCache(&$watches, $data)
	{
		$users = $this->reportsUsers->getAllUsers();
		$this->reportsCache->add($watches, $data, $users);
	}
}
