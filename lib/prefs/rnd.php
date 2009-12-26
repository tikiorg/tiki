<?php

function prefs_rnd_list() {
	return array(
		'rnd_num_reg' => array(
			'name' => tra('Use CAPTCHA to prevent automatic/robot registrations'),
			'type' => 'flag',
			'help' => 'Spam+Protection',
		),
	);
}
