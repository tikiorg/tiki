<?php

function prefs_lowercase_list() {
	return array(
		'lowercase_username' => array(
			'name' => tra('Force lowercase'),
			'type' => 'flag',
			'help' => 'Login+Config#Case_Sensitivity',
		),
	);	
}
