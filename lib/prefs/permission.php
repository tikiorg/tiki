<?php

function prefs_permission_list() {
	return array(
		'permission_denied_url' => array(
			'name' => tra('Send to URL'),
			'type' => 'text',
			'size' => '50',
		),

		// Used in templates/tiki-admin-include-general.tpl
		'permission_denied_login_box' => array(
			'name' => tra('On permission denied, display login module (for Anonymous)'),
			'type' => '',
		),
	);
}
