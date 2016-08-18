<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_bigbluebutton_list()
{
	return array(
		'bigbluebutton_feature' => array(
			'name' => tra('BigBlueButton Web Conferencing'),
			'description' => tra('Integration with the BigBlueButton collaboration server for web conference and screen sharing.'),
			'type' => 'flag',
			'keywords' => 'big blue button web conferencing audio video chat screensharing whiteboard',
			'help' => 'BigBlueButton',
			'tags' => array('basic'),
			'default' => 'n',
			'extensions' => array(
				'dom',
			),			
		),
		'bigbluebutton_server_location' => array(
			'name' => tra('BigBlueButton server location'),
			'description' => tra('Full URL to the BigBlueButton installation.'),
			'type' => 'text',
			'filter' => 'url',
			'hint' => tra('http://host.example.com/'),
			'keywords' => 'big blue button web conferencing audio video chat screensharing whiteboard',
			'size' => 40,
			'tags' => array('basic'),
			'default' => '',
		),
		'bigbluebutton_server_salt' => array(
			'name' => tra('BigBlueButton server salt'),
			'description' => tra('A salt key used to generate checksums for the BigBlueButton server to assure that requests are authentic.'),
			'keywords' => 'big blue button web conferencing audio video chat screensharing whiteboard',
			'type' => 'text',
			'size' => 40,
			'filter' => 'text',
			'tags' => array('basic'),
			'default' => '',
		),
		'bigbluebutton_recording_max_duration' => array(
			'name' => tr('BigBlueButton recording maximum duration'),
			'description' => tr('A maximum duration for the meetings must be submitted to BigBlueButton to prevent the recordings from being excessively long if a user leaves the conference window open.'),
			'shorthint' => tr('minutes'),
			'keywords' => 'big blue button',
			'type' => 'text',
			'filter' => 'digits',
			'size' => 6,
			'default' => 5*60,
			'tags' => array('basic'),
		),
		'bigbluebutton_dynamic_configuration' => array(
			'name' => tr('BigBlueButton Dynamic Configuration'),
			'description' => tr('Uses the advanced options of BigBlueButton to configure the XML per room.'),
			'keywords' => 'big blue button',
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced', 'experimental'),
		),
	);
}


