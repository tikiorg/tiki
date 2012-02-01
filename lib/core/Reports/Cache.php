<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @package Tiki
 * @subpackage Reports 
 * 
 * Manage the cache of changes to send to users
 * in a period report.
 */
class Reports_Cache
{
	/**
	 * @var TikiDb
	 */
	protected $db;
	
	protected $table;
	
	/**
	 * @param TikiDb $db
	 * @return null
	 */
	public function __construct(TikiDb $db)
	{
		$this->db = $db;
		$this->table = $db->table('tiki_user_reports_cache');
	}

	/**
	 * Return cache entries for a given user.
	 * @param string $user
	 * @return array
	 */
	public function get($user)
	{
		$entries = $this->table->fetchAll(array('user', 'event', 'data', 'time'), array('user' => $user), -1, -1, 'time ASC');
		
		$ret = array();
		
		foreach ($entries as $entry) {
			$entry['data'] = unserialize($entry['data']);
			$ret[] = $entry;
		}
		
		return $ret;
	}
	
	/**
	 * Delete all cache entries for a given user.
	 * 
	 * @param string $user
	 * @return null
	 */
	public function delete($user)
	{
		$this->db->table('tiki_user_reports_cache')->deleteMultiple(array('user' => $user));
	}
}