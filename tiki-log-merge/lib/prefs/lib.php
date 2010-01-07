<?php

function prefs_lib_list() {
	return array(
		'lib_spellcheck' => array(
			'name' => tra('Spellchecker'),
			'description' => tra('Check spelling'),
			'type' => 'flag',
			'help' => 'Spellcheck',
			'hint' => 'Requires a separate download',
		),
	);
}
