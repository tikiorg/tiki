<?php

function prefs_bot_list() {
	return array(
		'bot_logo_code' => array(
			'name' => tra('Content'),
			'hint' => tra('Example:') . ' ' . '<div style="text-align: center"><small>Powered by Tikiwiki</small></div>',
			'type' => 'textarea',
			'size' => '6',
		),
	);	
}
