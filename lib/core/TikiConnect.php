<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
 
class TikiConnect
{

	// preferences that we should not collect

	private $privatePrefs = array(
		'gmap_key',
		'recaptcha_pubkey',
		'recaptcha_privkey',
	);

	// preferences that we should ask to collect
	
	private $protectedPrefs = array(
		'browsertitle',
		'connect_server',
		'connect_site_email',
		'connect_site_location',
		'connect_site_title',
		'connect_site_url',
		'feature_site_report_email',
		'fgal_use_dir',
		'gmap_defaultx',
		'gmap_defaulty',
		'header_custom_js',
		'sender_email',
		'sitemycode',
		'sitesubtitle',
		'sitetitle',
		't_use_dir',
	);

	private $connectTable = null;
	private $votes = null;

	public function __construct() {
		$this->connectTable = TikiDb::get()->table('tiki_connect');
	}

	/**
	 * Collects and returns Tiki Connect data
	 * 
	 * @return array
	 */

	function buildConnectData() {
		global $prefs;
		$info = array( 'version' => $prefs['tiki_release'] );

		if ($prefs['connect_send_anonymous_info'] === 'y') {
			$tikilib = TikiLib::lib('tiki');
			$tikilib->invalidateModifiedPreferencesCaches();
			$modifiedPrefs = $tikilib->getModifiedPreferences();

			// remove the non-anonymous values
			foreach ( $this->privatePrefs as $p ) {
				unset($modifiedPrefs[$p]);
			}
			// remove the protected values
			foreach ( $this->protectedPrefs as $p ) {
				unset($modifiedPrefs[$p]);
			}
			$info['prefs'] = $modifiedPrefs;
			// get all table row counts
			$res = TikiLib::lib('tiki')->fetchAll('SELECT table_name, table_rows FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE();');
			if (!empty($res)) {
				$info['tables'] = array();
				foreach( $res as $r ) {
					$info['tables'][$r['table_name']] = $r['table_rows'];
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
			foreach( $this->protectedPrefs as $p) {
				$site_prefs[$p] = $prefs[$p];
			}
			$info['site'] = $site_prefs;
			$info['server'] = $_SERVER;
		}
		return $info;
	}

	/**
	 * Records a row in tiki_connect and updates pref connect_last_post if client
	 *
	 * @param string $status	pending|confirmed|sent|received
	 * @param null $guid		client guid
	 * @param array $data		"connect" data to store
	 * @param bool $server		server mode (default client)
	 * @return void
	 */

	function recordConnection($status, $guid, $data = '', $server = false) {

		if (is_array($data) || is_object($data)) {
			$data = serialize( $data );
		}
		$this->connectTable->insert(array(
				'type' => $status,
				'data' => $data,
				'guid' => $guid,
				'server' => $server ? 1 : 0,
		));

		if (!$server) {
			$tikilib = TikiLib::lib('tiki');
			$tikilib->set_preference('connect_last_post', $tikilib->now);
		}
	}

	function getLastDataSent() {

		$res = $this->connectTable->fetchAll(
			array('created', 'data'),
			array(
				'type' => 'sent',
				'server' => 0,
			),
			1,
			-1,
			array( 'created' => 'DESC')
		);

		if (!empty($res[0]) && !empty($res[0]['data'])) {
			return unserialize( $res[0]['data'] );
		} else {
			return array();
		}

	}

	function diffDataWithLastSent( $data ) {
		$lastData = $this->getLastDataSent();

		if (!empty($lastData)) {
			foreach( $data as $key => $val) {
				if (is_array($val)) {
					foreach( $val as $ikey => $ival) {
						if (isset( $lastData[$key][$ikey] ) && $lastData[$key][$ikey] === $ival) {
							unset($data[$key][$ikey]);
						}
					}
				} else if (!in_array( $key, array('version', 'guid'))) {
					if (isset( $lastData[$key] ) && $lastData[$key] === $val) {
						unset($data[$key]);
					}
				}
			}
		}

		return $data;
	}


	function getReceivedDataStats() {
		global $prefs;

		$ret = array();

		if ($prefs['connect_server_mode'] === 'y') {
			$ret['received'] = $this->connectTable->fetchCount(
				array(
					'type' => 'received',
					'server' => 1,
				)
			);
		}

		// select distinct guid from tiki_connect where server=1;
		$res = TikiLib::lib('tiki')->getOne('SELECT COUNT(DISTINCT `guid`) FROM `tiki_connect` WHERE `server` = 1 AND `type` = \'received\';');

		$ret['guids'] = $res;
		
		return $ret;
	}

	/**
	 * gets a guid created within last 1 minute
	 * Connect Client
	 *
	 * @return string guid
	 */

	function getPendingGuid() {

		$res = $this->connectTable->fetchAll(
			array('created', 'guid'),
			array(
				'type' => 'pending',
				'server' => 0,
			),
			1,
			-1,
			array( 'created' => 'DESC')
		);
		
		if (!empty($res[0])) {
			$created = strtotime($res[0]['created']);
			if ($created + 60 > time()) {	// 1 min
				return $res[0]['guid'];
			}
		}
		return '';
	}

	/**
	 * gets a confirmed guid if there
	 * Connect Client
	 *
	 * @return string guid
	 */
	
	function getConfirmedGuid() {
		$res = $this->connectTable->fetchAll(
			array('created', 'guid'),
			array('type' => 'confirmed', 'server' => 0),
			1,
			-1,
			array( 'created' => 'DESC')

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

	function getVote($pref) {
		$votes = $this->getVotes();
		if (isset( $votes[$pref] )) {
			return $votes[$pref];
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

	function getVotes( $reload = false ) {
		global $prefs;

		if (empty($this->votes) || $reload ) {
			$this->votes = $this->getVotesForGuid($prefs['connect_guid']);
		}
		return $this->votes;
	}

	/**
	 * Load vote info from database
	 * Connect Client (default) or Server
	 *
	 * @param string $guid
	 * @param bool $server
	 * @return array
	 */

	function getVotesForGuid( $guid, $server = false ) {
		if (!empty($guid)) {
			$res = $this->connectTable->fetchAll(
				array('data'),
				array(
					 'type' => 'votes',
					 'guid' => $guid,
					 'server' => $server ? 1 : 0
				),
				1,
				-1,
				array( 'created' => 'DESC')

			);
		} else {
			$res = array();
		}

		if (!empty($res[0])) {
			return unserialize($res[0]['data']);
		} else {
			return array();
		}
	}

	/**
	 * Save current votes to database
	 * Connect Client
	 *
	 * @param string $guid
	 * @param $votes
	 * @return void
	 */

	function saveVotesForGuid( $guid, $votes ) {

		if (is_array($votes) || is_object($votes)) {
			$votes = serialize( $votes );
		}
		$count = $this->connectTable->fetchCount(array(
			'server' => 0,
			'guid' => $guid,
			'type' => 'votes',
		));

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

	/**
	 * removes confirm/pending guid if there
	 *
	 * @param string $guid
	 * @param bool $server
	 * @return void
	 */

	function removeGuid( $guid, $server = false ) {
		$this->connectTable->update(
			array(
				'type' => 'deleted_pending'
			),
			array(
				'server' => $server ? 1 : 0,
				'guid' => $guid,
				'type' => 'pending',
			)
		);
		$this->connectTable->update(
			array(
				'type' => 'deleted_confirmed'
			),
			array(
				'server' => $server ? 1 : 0,
				'guid' => $guid,
				'type' => 'confirmed',
			)
		);
	}

	/**
	 * test if a guid is pending
	 * Connect Server
	 *
	 * @param string $guid
	 * @return string
	 */

	function isPendingGuid( $guid ) {
		$res = $this->connectTable->fetchOne(
			'data',
			array(
				'type' => 'pending',
				'server' => 1,
				'guid' => $guid,
			)
		);
		return $res;
	}

	/**
	 * text if a guid is confirmed here
	 * Connect Server
	 *
	 * @param string $guid
	 * @return bool
	 */

	function isConfirmedGuid( $guid ) {
		$res = $this->connectTable->fetchCount(
			array(
				'type' => 'confirmed',
				'server' => 1,
				'guid' => $guid,
			)
		);
		return $res > 0;
	}
}
