<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Connect_Controller
{

	private $private_prefs = array(
		'browsertitle',
		'connect_server',
		'connect_site_email',
		'connect_site_email',
		'connect_site_location',
		'connect_site_title',
		'connect_site_url',
		'gmap_defaultx',
		'gmap_defaulty',
		'gmap_key',
		'header_custom_js',
		'sitesubtitle',
		'sitetitle',
	);

	function setUp()
	{
		global $prefs;

		if ($prefs['connect_feature'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}
	}

	function action_list($input)
	{
		global $user, $prefs;
		
		if (! $user) {
			return array();
		}

		$info = array( 'version' => $prefs['tiki_release'] );

		if ($prefs['connect_send_anonymous_info'] === 'y') {
			$prefslib = TikiLib::lib('prefs');
			$modified_prefs = $prefslib->getModifiedPreferences();

			// remove the non-anonymous values
			foreach ($this->private_prefs as $p) {
				unset($modified_prefs[$p]);
			}
			foreach ($modified_prefs as &$p) {
				$p = $p['cur'];	// remove the defaults
			}
			$info['prefs'] = $modified_prefs;
			// get all table row counts
			$res = TikiLib::lib('tiki')->fetchAll('SELECT table_name, table_rows FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE();');
			if (!empty($res)) {
				$info['tables'] = array();
				foreach( $res as $r ) {
					$info['tables'][$r['table_name']] = $r['table_rows'];
				}
			}
		}

		if ($prefs['connect_send_info'] === 'y') {
			$site_prefs = array();
			foreach( $this->private_prefs as $p) {
				$site_prefs[$p] = $prefs[$p];
			}
			$info['site'] = $site_prefs;
			$info['server'] = $_SERVER;
		}

		return $info;
	}

}

