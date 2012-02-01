<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
	
	public function __construct(Reports_Users $reportsUsers, Reports_Cache $reportsCache)
	{
		$this->reportsUsers = $reportsUsers;
		$this->reportsCache = $reportsCache;
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
		$this->reportsCache->add(&$watches, $data, $users);
	}
}