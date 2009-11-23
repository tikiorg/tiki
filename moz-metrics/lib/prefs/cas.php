<?php

function prefs_cas_list() {
	return array(
			'cas_create_user_tiki' => array(
				'name' => tra('Create user if not in Tiki'),
				'type' => 'flag',
				),
			'cas_skip_admin' => array(
				'name' => tra('Use Tiki authentication for Admin login'),
				'type' => 'flag',
				),
			'cas_show_alternate_login' => array(
				'name' => tra('Show Alternate Login Method in Header'),
				'type' => 'flag',
				),
			'cas_version' => array(
				'name' => tra('CAS server version'),
				'type' => 'list',
				'options' => array(
					'none' => tra('none'),
					'1.0'  => tra('Version 1.0'),
					'2.0'  => tra('Version 2.0'),
					)
				),
			'cas_hostname' => array(
				'name' => tra('Hostname'),
				'description' => tra('Hostname of the CAS server.'),
				'type' => 'text',
				'size' => 50,
				'filter' => 'striptags',
				),
			'cas_port' => array(
					'name' => tra('Port'),
					'description' => tra('Port of the CAS server.'),
					'type' => 'text',
					'size' => 5,
					'filter' => 'digits',
					),
			'cas_path' => array(
					'name' => tra('Path'),
					'description' => tra('Path for the CAS server.'),
					'type' => 'text',
					'size' => 50,
					'filter' => 'striptags',
					),
			'cas_extra_param' => array(
					'name' => tra('CAS Extra Parameter'),
					'description' => tra('Extra Parameter to pass to the CAS Server.'),
					'type' => 'text',
					'size' => 100,
					'filter' => 'striptags',
					),
			);
}
