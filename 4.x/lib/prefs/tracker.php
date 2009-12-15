<?php

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
