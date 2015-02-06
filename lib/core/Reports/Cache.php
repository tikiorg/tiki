<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Manage the cache of changes to send to users
 * in a period report.
 *
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Cache
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
		$this->table = $db->table('tiki_user_reports_cache');
		$this->dt = $dt;
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
		$this->table->deleteMultiple(array('user' => $user));
	}

	/**
	 * Add Tiki object change information to reports cache
	 * and remove it from the $watches array so that it is not
	 * send to the user in a single email.
	 *
	 * @param array $watches a list of users watching the changed object and some information about the object itself
	 * @param array $data information about the changed object
	 * @param array $users a list of users that are using periodic reports
	 * @return null
	 */
	public function add(&$watches, $data, $users)
	{
		$data["base_url"] = TikiLib::tikiURL();  //Store $base_url in the database. Use it to construct links in the email.

		foreach ($watches as $key => $watch) {
			// if user in the watch has enabled periodic reports
			if (in_array($watch['user'], $users)) {
				// add data to report cache
				$this->table->insert(
					array(
						'user' => $watch['user'],
						'event' => $data['event'],
						'data' => serialize($data),
						'time' => $this->dt->format('Y-m-d H:i:s')
					)
				);

				// remove data from $watches array so that the user doesn't receive a email
				// notification for the event
				unset($watches[$key]);
			}
		}
	}
}
