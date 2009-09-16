<?php

function prefs_wiki_list() {
	return array(
		'wiki_page_regex' => array(
			'name' => tra('Wiki link format'),
			'description' => tra('Character set used when detecting wiki links within pages.'),
			'type' => 'list',
			'options' => array(
				'complete' => tra('Complete'),
				'full' => tra('Latin'),
				'strict' => tra('English'),
			),
		),
		'wiki_show_version' => array(
			'name' => tra('Display page version'),
			'description' => tra('Display the page version information when viewing the page.'),
			'type' => 'flag',
		),
		'wiki_pagename_strip' => array(
			'name' => tra('Page name display stripper'),
			'description' => tra('Character to use as a delimiter in the page name. The portion of the name after this character will not be displayed.'),
			'type' => 'text',
			'size' => 5,
			'help' => '#',
		),
		'wiki_authors_style' => array(
			'name' => tra('Wiki author list style'),
			'description' => tra('Changes the list format used to display the authors of the page.'),
			'type' => 'list',
			'options' => array(
				'classic' => tra('Creator & Author'),
				'business' => tra('Business style'),
				'collaborative' => tra('Collaborative style'),
				'lastmodif' => tra('Page last modified on'),
				'none' => tra('none (disabled)'),
			),
		),
		'wiki_autors_style_by_page' => array(
			'name' => tra('Specify wiki author list style per page'),
			'description' => tra('Allows to modify the style in which the author list is displayed on a per-page basis.'),
			'type' => 'flag',
		),
		'wiki_actions_bar' => array(
			'name' => tra('Wiki action bar location'),
			'description' => tra('Buttons: Save, Preview, Cancel, ...'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top'),
				'bottom' => tra('Bottom'),
				'both' => tra('Both'),
			),
		),
		'wiki_page_navigation_bar' => array(
			'name' => tra('Wiki navigation bar location'),
			'description' => tra('When using the ...page... page break wiki syntax'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top'),
				'bottom' => tra('Bottom'),
				'both' => tra('Both'),
			),
		),
		'wiki_topline_position' => array(
			'name' => tra('Wiki top line location'),
			'description' => tra('Page description, icons, backlinks, ...'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top'),
				'bottom' => tra('Bottom'),
				'both' => tra('Both'),
				'none' => tra('Neither'),
			),
		),
		'wiki_cache' => array(
			'name' => tra('Cache wiki pages (global)'),
			'description' => tra('Enable page cache globally for wiki pages.'),
			'type' => 'list',
			'options' => array(
				0 => tra('no cache'),
				60 => '1 ' . tra('minute'),
				300 => '5 ' . tra('minutes'),
				600 => '10 ' . tra('minutes'),
				900 => '15 ' . tra('minutes'),
				1800 => '30 ' . tra('minutes'),
				3600 => '1 ' . tra('hour'),
				7200 => '2 ' . tra('hours'),
			),
		),
	);
}

?>
