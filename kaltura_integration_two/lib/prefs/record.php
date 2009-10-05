<?php

function prefs_record_list() {
	return array(
		'record_untranslated' => array(
			'name' => tra('Record untranslated'),
			'description' => tra('Keep track of the untranslated strings.'),
			'type' => 'flag',
		),
	);
}
