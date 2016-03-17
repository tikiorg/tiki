<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: multidomain.php 53803 2015-02-06 00:42:50Z jyhem $

function prefs_access_list()
{
	return array(
		'access_control_allow_origin' => array(
			'name' => tra('Access-Control-Allow-Origin'),
			'description' => tra('Domains allowed to make "CORS" (Cross-Origin Resource Sharing or Cross-Domain Ajax) requests from this server.'),
			'type' => 'textarea',
			'hint' => tra('One URI per line, for example, "http://www.example.com" or "*" for any site'),
			'default' => '',
		),
	);
}
