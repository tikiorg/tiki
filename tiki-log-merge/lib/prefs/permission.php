<?php

function prefs_permission_list() {
	return array(
		'permission_denied_url' => array(
			'name' => tra('Send to URL'),
			'type' => 'text',
			'size' => '50',
		),
	);
}
