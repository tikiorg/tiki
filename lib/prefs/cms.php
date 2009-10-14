<?php

function prefs_cms_list() {
	return array(
		'cms_spellcheck' => array(
			'name' => tra('Spell checking'),
			'type' => 'flag',
			'help' => 'Spellcheck',
		),
	);
}
