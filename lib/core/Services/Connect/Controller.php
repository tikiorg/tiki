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

		$prefslib = TikiLib::lib('prefs');
		$modified_prefs = $prefslib->getModifiedPreferences();

		// remove some non-anonymous values
		if ($prefs['connect_send_info'] !== 'y') {
			foreach ($this->private_prefs as $p) {
				unset($modified_prefs[$p]);
			}
		}

		$info['prefs'] = $modified_prefs;

		$pages = TikiDb::get()->table('tiki_pages');
		$info['objects'] = array( 'wiki_pages' => $pages->fetchOne($pages->count()));
		
		return $info;
	}

}

