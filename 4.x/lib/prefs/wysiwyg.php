<?php

function prefs_wysiwyg_list() {
	return array(
		'wysiwyg_optional' => array(
			'name' => tra('Wysiwyg Editor is optional'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
		),
		'wysiwyg_default' => array(
			'name' => tra('... and is displayed by default'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_optional',
			),
		),
		'wysiwyg_memo' => array(
			'name' => tra('Reopen with the same editor'),
			'type' => 'flag',
		),
		'wysiwyg_wiki_parsed' => array(
			'name' => tra('Content is parsed like wiki page'),
			'type' => 'flag',
		),
		'wysiwyg_wiki_semi_parsed' => array(
			'name' => tra('Content is partially parsed'),
			'type' => 'flag',
		),
		'wysiwyg_toolbar_skin' => array(
			'name' => tra('Toolbar skin'),
			'type' => 'list',
			'options' => array(
				'default' => tra('Default'),
				'office2003' => tra('Office 2003'),
				'silver' => tra('Silver'),
			),
		),
	);
}
