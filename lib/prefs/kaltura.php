<?php

function prefs_kaltura_list()
{
	return array(
		'kaltura_partnerId' => array(
			'name' => tra('Partner ID'),
			'description' => tra('Kaltura Partner ID'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 10,
			'default' => '',
		),
		'kaltura_secret' => array(
			'name' => tra('User secret'),
			'description' => tra('Kaltura partner setting user secret.'),
			'type' => 'text',
			'size' => 45,
			'filter' => 'alnum',
			'default' => '',
		),
		'kaltura_adminSecret' => array(
			'name' => tra('Admin secret'),
			'description' => tra('Kaltura partner setting admin secret.'),
			'type' => 'text',
			'size' => 45,
			'filter' => 'alnum',
			'default' => '',		
		),
		'kaltura_kdpUIConf' => array(
			'name' => tra('Kaltura Video Player ID'),
			'description' => tra('Kaltura Dynamic Player (KDP) user interface configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '1913592',
		),
		'kaltura_kdpEditUIConf' => array(
			'name' => tra('Kaltura Video Player ID (Editor)'),
			'description' => tra('Kaltura Dynamic Player (KDP) user interface configuration ID for use when editing'),
			'type' => 'text',
			'size' => 20,
			'default' => '1913592',
		),
		'kaltura_kcwUIConf' => array(
			'name' => tra('KCW UI Configuration ID'),
			'description' => tra('Kaltura Configuration Wizard (KCW) user interface configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '1913682',
		),
		'kaltura_kServiceUrl' => array(
			'name' => tra('Kaltura Service URL'),
			'description' => tra('e.g. http://www.kaltura.com/'),
			'type' => 'text',
			'size' => 40,
			'default' => 'http://www.kaltura.com/',
		),
		'kaltura_legacyremix' => array(
			'name' => tra('Show remixes from old versions of Kaltura'),
			'description' => tra('Show remixes from old versions of Kaltura (remixing is no longer supported)'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
