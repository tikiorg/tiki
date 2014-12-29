<?php

// Warning: pref names have a limit of 40 characters
// Please prefix with ta_ (short for tikiaddon) and your package vendor and name

function prefs_ta_tikiorg_helloworld_list()
{
	return array(
		'ta_tikiorg_helloworld_on' => array( // This main _on pref is mandatory
			'name' => tra('Activate Tiki Sample Hello World'),
			'description' => tra('Activate Tiki Sample Hello World Addon'),
			'type' => 'flag',
			'admin' => 'ta_tikiorg_helloworld',
			'tags' => array('basic'),
			'default' => 'y',
		),
		'ta_tikiorg_helloworld_boldtext' => array(
			'name' => tra('Bold Text'),
			'description' => tra('Bold text'),
			'type' => 'flag',
			'tags' => array('basic'),
			'default' => 'y',
		),
	);
}