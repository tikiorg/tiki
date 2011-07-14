<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_short_list() {
	return array(
		'short_date_format' => array(
			'name' => tra('Short date format'),
			'type' => 'text',
			'size' => '30',
			//get_strings tra("%Y-%m-%d");
		),
		'short_time_format' => array(
			'name' => tra('Short time format'),
			'type' => 'text',
			'size' => '30',
			//get_strings tra("%H:%M");
		),
		//get_strings tra("%Y-%m-%d %H:%M");
	);
}
