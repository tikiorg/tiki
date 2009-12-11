<?php

function prefs_calendar_list() {
	return array(
		'calendar_view_days' => array(
			'name' => tra('Days to display in the Calendar'),
			'type' => 'multicheckbox',
			'options' => array( 
				0 => tra('Sunday'),
				1 => tra('Monday'),
				2 => tra('Tuesday'),
				3 => tra('Wednesday'),
				4 => tra('Thursday'),
				5 => tra('Friday'),
				6 => tra('Saturday'),
			)
		),
	);
}
