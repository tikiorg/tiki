<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_bigbluebutton_list()
{
	return array(
		'bigbluebutton_feature' => array(
			'name' => tra('BigBlueButton'),
			'description' => tra('Integration with the BigBlueButton collaboration server for web conference and screen sharing.'),
			'type' => 'flag',
			'keywords' => 'big blue button',
		),
		'bigbluebutton_server_location' => array(
			'name' => tra('BigBlueButton server location'),
			'description' => tra('Full URL to the BigBlueButton installation.'),
			'type' => 'text',
			'filter' => 'url',
			'hint' => tra('http://host.example.com/'),
			'keywords' => 'big blue button',
			'size' => 40,
		),
		'bigbluebutton_server_salt' => array(
			'name' => tra('BigBlueButton server salt'),
			'description' => tra('A salt key used to generate checksums for the BigBlueButton server to know the requests are authentic.'),
			'keywords' => 'big blue button',
			'type' => 'text',
			'size' => 40,
			'filter' => 'text',
		),
	);
}


