<?php

function prefs_quantify_list() {
	return array(
		'quantify_changes' => array(
			'name' => tra('Quantify change size'),
			'description' => tra('In addition to tracking the changes, track the change size and display the approximate up-to-date-ness of the page.'),
			'type' => 'flag',
		),
	);
}
