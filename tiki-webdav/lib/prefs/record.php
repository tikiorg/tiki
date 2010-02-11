<?php

function prefs_record_list() {
	return array(
		'record_untranslated' => array(
			'name' => tra('Record untranslated strings'),
			'description' => tra('Keep track of the unsuccessful attemps to translate strings.'),
			'type' => 'flag',
		),
	);
}
