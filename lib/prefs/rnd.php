<?php

function prefs_rnd_list() {
	return array(

		// Used in templates/tiki-admin-include-login.tpl
		'rnd_num_reg' => array(
			'name' => tra('Use CAPTCHA to prevent automatic/robot registrations'),
			'type' => '',
		),
	);
}
