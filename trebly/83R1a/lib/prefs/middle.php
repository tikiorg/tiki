<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: middle.php 33845 2011-04-06 21:03:07Z lphuberdeau $

function prefs_middle_list() {
	return array(
		'middle_shadow_start' => array(
			'name' => tra('Middle shadow start'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'middle_shadow_end' => array(
			'name' => tra('Middle shadow end'),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
