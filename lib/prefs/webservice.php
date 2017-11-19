<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_webservice_list()
{
	return [
		'webservice_consume_defaultcache' => [
			'name' => tra('Default cache'),
			'description' => tra('Cache time in seconds to use if the webservice does not supply "max-age" or "no-cache" in the response.'),
			'dependencies' => ['feature_webservices'],
			'type' => 'text',
			'filter' => 'digits',
			'units' => tra('seconds'),
			'default' => 300,		// 5 min
		],
	];
}
