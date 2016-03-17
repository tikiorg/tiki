<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Connect_Client extends Tiki_Connect_Abstract
{

	private $votes = null;

	/**
	 * Collects and returns Tiki Connect data
	 *
	 * @return array		containing: 'prefs', 'tables', 'votes', 'site' and 'server' info arrays
	 * 						depending on connect prefs
	 */

	function buildConnectData()
	{
		global $prefs, $TWV;
		$info = array('version' => $TWV->version);

		if ($prefs['connect_send_anonymous_info'] === 'y') {
			$cachelib = TikiLib::lib('cache');
			$cachelib->invalidate('global_preferences');

			$tikilib = TikiLib::lib('tiki');
			$modifiedPrefs = $tikilib->getModifiedPreferences();

			// remove the non-anonymous values
			foreach ($this->privatePrefs as $p) {
				unset($modifiedPrefs[$p]);
			}
			// remove the protected values
			foreach ($this->protectedPrefs as $p) {
				unset($modifiedPrefs[$p]);
			}
			$info['prefs'] = $modifiedPrefs;
			// get all table row counts
			$tikilib = TikiLib::lib('tiki');
			$res = $tikilib->fetchAll('SHOW TABLES;');
			if (!empty($res)) {
				$info['tables'] = array();
				foreach ($res as $r) {
					foreach ($r as $table) {
						$info['tables'][$table] = $tikilib->getOne('SELECT COUNT(*) FROM `' . $table . '`');
					}
				}
			}

			$votes = $this->getVotes();
			if (!empty($votes)) {
				$info['votes'] = $votes;
			}
		}

		if ($prefs['connect_send_info'] === 'y') {
			// restore the protected values
			$site_prefs = array();
			foreach ($this->protectedPrefs as $p) {
				if (isset($prefs[$p])) {			// some protected prefs are legacy ones from previous versions
					$site_prefs[$p] = $prefs[$p];
				}
			}
			$info['site'] = $site_prefs;
			$info['server'] = $_SERVER;
		}
		return $info;
	}

	function getLastDataSent()
	{
		$res = $this->connectTable->fetchAll(
			array('created', 'data'),
			array(
				'type' => 'sent',
				'server' => 0,
			),
			1,
			-1,
			array('created' => 'DESC')
		);

		if (!empty($res[0]) && !empty($res[0]['data'])) {
			return unserialize($res[0]['data']);
		} else {
			return array();
		}
	}

	function diffDataWithLastSent($data)
	{
		$lastData = $this->getLastDataSent();

		if (!empty($lastData)) {
			foreach ($data as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $ikey => $ival) {
						if (isset($lastData[$key][$ikey]) && $lastData[$key][$ikey] === $ival) {
							unset($data[$key][$ikey]);
						}
					}
				} else if (!in_array($key, array('version', 'guid'))) {
					if (isset( $lastData[$key] ) && $lastData[$key] === $val) {
						unset($data[$key]);
					}
				}
			}
		}

		return $data;
	}


	/**
	 * gets a guid created within last 1 minute
	 * N.B. time caluculation done within database to avoid timezone issues etc
	 *
	 * @return string guid
	 */

	function getPendingGuid()
	{
		$res = TikiDb::get()->getOne(
			"SELECT `guid` FROM `tiki_connect` WHERE `type` = 'pending' AND " .
			"`created` > NOW() - INTERVAL 1 MINUTE ORDER BY `created` DESC LIMIT 1;"
		);
		return empty($res) ? '' : $res;
	}

	/**
	 * gets a confirmed guid if there
	 * Connect Client
	 *
	 * @return string guid
	 */

	function getConfirmedGuid()
	{
		$res = $this->connectTable->fetchAll(
			array('created', 'guid'),
			array('type' => 'confirmed', 'server' => 0),
			1,
			-1,
			array('created' => 'DESC')
		);

		if (!empty($res[0])) {
			return $res[0]['guid'];
		} else {
			return '';
		}
	}

	/**
	 * Gets voting for a single pref
	 * Connect Client
	 *
	 * @param string $pref		preference name
	 * @return array of votes
	 */

	function getVote($pref)
	{
		$votes = $this->getVotes();
		if (isset($votes->$pref)) {
			return (array) $votes->$pref;
		} else {
			return array();
		}
	}

	/**
	 * Gets current votes
	 * Connect Client
	 *
	 * @param bool $reload
	 * @return array
	 */

	function getVotes($reload = false)
	{
		global $prefs;

		if (empty($this->votes) || $reload ) {
			$this->votes = $this->getVotesForGuid($prefs['connect_guid']);
		}
		return $this->votes;
	}

	/**
	 * Save current votes to database
	 * Connect Client
	 *
	 * @param string $guid
	 * @param $votes
	 * @return void
	 */

	function saveVotesForGuid($guid, $votes)
	{

		if (is_array($votes) || is_object($votes)) {
			$votes = json_encode($votes);
		}

		$count = $this->connectTable->fetchCount(
			array(
				'server' => 0,
				'guid' => $guid,
				'type' => 'votes',
			)
		);

		if ($count) {
			$this->connectTable->update(
				array(
					'type' => 'votes',
					'data' => $votes,
				),
				array(
					'server' => 0,
					'guid' => $guid,
					'type' => 'votes',
				)
			);
		} else {
			$this->connectTable->insert(
				array(
					'type' => 'votes',
					'data' => $votes,
					'server' => 0,
					'guid' => $guid,
				)
			);
		}
	}
}
