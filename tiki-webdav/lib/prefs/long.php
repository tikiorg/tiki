<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_long_list() {
	return array(
		'long_date_format' => array(
			'name' => tra('Long date format'),
			'type' => 'text',
			'size' => '30',
		),
		'long_time_format' => array(
			'name' => tra('Long time format'),
			'type' => 'text',
			'size' => '30',
		),
	);	
}
