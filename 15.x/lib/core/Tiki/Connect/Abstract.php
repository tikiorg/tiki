<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class Tiki_Connect_Abstract
{

	// preferences that we should not collect
	// TODO these should be done as a property (or tag) per preference

	protected  $privatePrefs = array(
		'gmap_key',
		'recaptcha_pubkey',
		'recaptcha_privkey',
		'registerPasscode',
		'kaltura_partnerId',
		'kaltura_secret',
		'kaltura_adminSecret',
		'socialnetworks_twitter_consumer_key',
		'socialnetworks_twitter_consumer_secret',
		'zotero_client_key',
		'zotero_client_secret',
		'socialnetworks_facebook_application_secr',
		'socialnetworks_facebook_application_id',
		'watershed_fme_key',
		'payment_paypal_business',
		'shipping_fedex_key',
		'shipping_fedex_password',
		'shipping_fedex_account',
		'shipping_fedex_meter',
		'shipping_ups_license',
		'shipping_ups_username',
		'shipping_ups_password',
		'payment_cclite_gateway',
		'payment_cclite_merchant_user',
		'payment_cclite_merchant_key',
		'zend_mail_smtp_server',
		'zend_mail_smtp_user',
		'zend_mail_smtp_pass',
		'zend_mail_smtp_port',
		'proxy_host',
		'proxy_port',
		'proxy_user',
		'proxy_pass',
		'auth_ldap_host',
		'auth_ldap_port',
		'auth_ldap_adminpass',
		'auth_ldap_adminuser',
		'auth_ldap_basedn',
		'auth_ldap_userdn',
		'auth_ldap_group_host',
		'auth_ldap_group_port',
		'auth_ldap_group_adminuser',
		'auth_ldap_group_adminpass',
		'auth_ldap_group_basedn',
		'auth_ldap_group_userdn',
		'auth_ldap_groupdn',
		'cas_hostname',
		'cas_port',
		'cas_path',
		'cas_extra_param',
		'auth_phpbb_dbhost',
		'auth_phpbb_dbuser',
		'auth_phpbb_dbpasswd',
		'auth_phpbb_dbname',
		'bigbluebutton_server_location',
		'bigbluebutton_server_salt',
		'internal_site_hash',
		'lang_bing_api_client_id',
		'lang_bing_api_client_secret',
		'connect_guid',
		'lang_google_api_key',
		'socialnetworks_twitter_consumer_key',
		'socialnetworks_twitter_consumer_secret',
		'socialnetworks_facebook_application_secr',
		'socialnetworks_facebook_application_id',
		'socialnetworks_bitly_login',
		'socialnetworks_bitly_key',
		'watershed_fme_key',
		'zotero_client_key',
		'zotero_client_secret',
		'zotero_group_id',
		'vimeo_consumer_key',
		'vimeo_consumer_secret',
		'vimeo_access_token',
		'vimeo_access_token_secret',
	);

	// preferences that we should ask to collect

	protected $protectedPrefs = array(
		'browsertitle',
		'connect_server',
		'connect_site_email',
		'connect_site_location',
		'connect_site_title',
		'connect_site_url',
		'connect_site_keywords',
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
		'multidomain_config',
	);

	protected $connectTable = null;

	public function __construct()
	{
		$this->connectTable = TikiDb::get()->table('tiki_connect');
	}

	/**
	 * Records a row in tiki_connect and updates pref connect_last_post if client
	 *
	 * @param string $status	pending|confirmed|sent|received
	 * @param string $guid		client guid
	 * @param mixed $data		"connect" data to store (serialized)
	 * @param bool $server		server mode (default client)
	 * @return datetime $created
	 */

	function recordConnection($status, $guid, $data = '', $server = false)
	{

		if (is_array($data) || is_object($data)) {
			$data = json_encode( $data );
		}
		$insertId = $this->connectTable->insert(
			array(
				'type' => $status,
				'data' => $data,
				'guid' => $guid,
				'server' => $server ? 1 : 0,
			)
		);

		$created = $this->connectTable->fetchOne('created', array( 'id' => $insertId ));
		if (!$server) {
			$tikilib = TikiLib::lib('tiki');
			$tikilib->set_preference('connect_last_post', $tikilib->now);
		}
		return $created;
	}

	/**
	 * Load vote info from database
	 * Connect Client (default) or Server
	 *
	 * @param string $guid
	 * @param bool $server
	 * @return array
	 */

	function getVotesForGuid( $guid, $server = false )
	{
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
				array('created' => 'DESC')
			);
		} else {
			$res = array();
		}

		if (!empty($res[0])) {
			return json_decode($res[0]['data']);
		} else {
			return new stdClass();
		}
	}

	/**
	 * removes confirm/pending guid if there
	 *
	 * @param string $guid
	 * @param bool $server
	 * @return void
	 */

	function removeGuid( $guid, $server = false )
	{
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
