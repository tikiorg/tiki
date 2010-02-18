<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_rating_list() {
	return array(
		'rating_advanced' => array(
			'name' => tra('Advanced Rating'),
			'description' => tra('Rating system allowing for options and calculation method to be configured.'),
			'type' => 'flag',
		),
	);
}

