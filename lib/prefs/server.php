<?php

function prefs_server_list() {
	// Skipping the getTimeZoneList() from tikidate which just emulates the pear date format
	// Generating it is extremely costly in terms of memory.
	if( class_exists( 'DateTimeZone' ) ) {
		$timezones = DateTimeZone::listIdentifiers();
	} elseif ( class_exists('DateTime')) {
		$timezones = array_keys( DateTime::getTimeZoneList() );
	} else {
		$timezones = TikiDate::getTimeZoneList();
		$timezones = array_keys($timezones);
	}

	sort( $timezones );

	return array(
		'server_timezone' => array(
			'name' => tra('Timezone'),
			'description' => tra('Indicates the default time zone to use for the server.'),
			'type' => 'list',
			'options' => array_combine( $timezones, $timezones ),
		),
	);
}

