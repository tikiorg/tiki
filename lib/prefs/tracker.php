<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tracker_list() {
	return array (
		'tracker_jquery_user_selector_threshold' => array(
			'name' => tra('Use Jquery autocomplete user selector for better performance when number of users exceed'),
			'description' => tra('Use Jquery autocomplete user selector for better performance when number of users exceed'),
			'type' => 'text',
			'size' => '5',
			'dependencies' => array('feature_jquery_autocomplete'),
		)
	);
}
