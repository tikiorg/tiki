<?php

function prefs_allowmsg_list() {
	return array(
		'allowmsg_by_default' => array(
			'name' => tra('Users accept internal messages by default'),
			'description' => tra('Users accept internal messages by default'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
		),
		'allowmsg_is_optional' => array(
			'name' => tra('Users can opt-out internal messages'),
			'description' => tra('Users can opt-out internal messages'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
		),
	);
}
