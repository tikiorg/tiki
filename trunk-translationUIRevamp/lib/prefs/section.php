<?php

function prefs_section_list() {
	return array(
		'section_comments_parse' => array(
			'name' => tra('Parse wiki syntax in comments in all sections apart from Forums'),
			'type' => 'flag',
			'help' => 'Wiki+Syntax',
			'hint' => tra('Use "Accept wiki syntax" for forums in admin forums page'),
		),
	);
}
