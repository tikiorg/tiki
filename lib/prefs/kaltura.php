<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_kaltura_list()
{
	global $prefs;
	$players = array();

	if (isset($prefs['feature_kaltura']) && $prefs['feature_kaltura'] === 'y') {
		$kalturaadminlib = TikiLib::lib('kalturaadmin');
		$playerList = $kalturaadminlib->getPlayersUiConfs();
		foreach ($playerList as $pl) {
			$players[$pl['id']] = tra($pl['name']);
		}
	}

	return array(
		'kaltura_partnerId' => array(
			'name' => tra('Partner ID'),
			'description' => tra('Kaltura Partner ID'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 10,
			'default' => '',
			'tags' => array('basic'),
		),
		'kaltura_secret' => array(
			'name' => tra('User secret'),
			'description' => tra('Kaltura partner-setting user secret.'),
			'type' => 'text',
			'size' => 45,
			'filter' => 'alnum',
			'default' => '',
			'tags' => array('basic'),
		),
		'kaltura_adminSecret' => array(
			'name' => tra('Admin secret'),
			'description' => tra('Kaltura partner-setting admin secret.'),
			'type' => 'text',
			'size' => 45,
			'filter' => 'alnum',
			'default' => '',
			'tags' => array('basic'),
		),
		'kaltura_kdpUIConf' => array(
			'name' => tra('Kaltura Video Player ID'),
			'description' => tra('Kaltura Dynamic Player (KDP) user interface configuration ID'),
			'type' => empty($players) ? 'text' : 'list',
			'options' => $players,
			'size' => 20,
			'default' => '',
			'tags' => array('basic'),
		),
		'kaltura_kdpEditUIConf' => array(
			'name' => tra('Kaltura Video Player ID (in entry edit mode)'),
			'description' => tra('Kaltura Dynamic Player (KDP) user interface configuration ID for use when editing. You can use a player which also has an option to select a frame as video thumbnail'),
			'type' => empty($players) ? 'text' : 'list',
			'options' => $players,
			'size' => 20,
			'default' => '',
			'tags' => array('basic'),
		),
		'kaltura_kcwUIConf' => array(
			'name' => tra('KCW UI Configuration ID'),
			'description' => tra('Kaltura Configuration Wizard (KCW) user interface configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '',
		),
		'kaltura_kServiceUrl' => array(
			'name' => tra('Kaltura Service URL'),
			'description' => tra('for example, https://www.kaltura.com/'),
			'type' => 'text',
			'size' => 40,
			'default' => 'https://www.kaltura.com/',
			'tags' => array('basic'),
		),
		'kaltura_legacyremix' => array(
			'name' => tra('Show remixes from old versions of Kaltura'),
			'description' => tra('Show remixes from old versions of Kaltura (remixing is no longer supported)'),
			'type' => 'flag',
			'default' => 'n',
			'view' => 'tiki-list_kaltura_entries.php?list=mix',
		),
	);
}
