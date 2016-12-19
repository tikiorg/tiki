<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_long_list()
{
	return array(
		'long_date_format' => array(
			'name' => tra('Long date format'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '30',
			'default' => '%A %B %e, %Y',
			'tags' => array('basic'),
			//get_strings tra("%A %d of %B, %Y");
		),
		'long_time_format' => array(
			'name' => tra('Long time format'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '30',
			'default' => '%H:%M:%S %Z',
			'tags' => array('basic'),
			//get_strings tra("%H:%M:%S %Z");
		),
		//get_strings tra("%A %d of %B, %Y %H:%M:%S %Z");
	);	
}
