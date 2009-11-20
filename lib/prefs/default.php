<?php

function prefs_default_list() {
	return array(
		'default_mail_charset' => array(
			'name' => tra('Default charset for sending mail'),
			'description' => tra('Default charset for sending mail'),
			'type' => 'list',
			'options' => array(
				'utf-8' => tra('utf-8'),
				'iso-8859-1' => tra('iso-8859-1'),
			),
		),
		'default_map' => array(
			'name' => tra('default mapfile'),
			'type' => 'text',
			'size' => '50',
		),
		'default_wiki_diff_style' => array(
			'name' => tra('Default diff style'),
			'type' => 'list',
			'options' => array(
				'old' => tra('Only with last version'),
				'htmldiff' => tra('HTML diff'),
				'sidediff' => tra('Side-by-side diff'),
				'sidediff-char' => tra('Side-by-side diff by characters'),
				'inlinediff' => tra('Inline diff'),
				'inlinediff-char' => tra('Inline diff by characters'),
				'sidediff-full' => tra('Full side-by-side diff'),
				'sidediff-full-char' => tra('Full side-by-side diff by characters'),
				'inlinediff-full' => tra('Full inline diff'),
				'inlinediff-full-char' => tra('Full inline diff by characters'),
				'unidiff' => tra('Unified diff'),
				'sideview' => tra('Side-by-side view'),
			),
		),
	);
}
