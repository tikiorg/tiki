<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
 
abstract class Connect_Abstract
{

	// preferences that we should not collect

	protected  $privatePrefs = array(
		'gmap_key',
		'recaptcha_pubkey',
		'recaptcha_privkey',
	);

	// preferences that we should ask to collect
	
	protected $protectedPrefs = array(
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

	protected $connectTable = null;

	public function __construct() {
		$this->connectTable = TikiDb::get()->table('tiki_connect');
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

}
