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
		'wiki_authors_style_by_page' => array(
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
		'wiki_feature_copyrights' => array(
			'name' => tra('Wiki'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
		),
		'wiki_edit_plugin' => array(
			'name' => tra('Enable edit plugin icons'),
			'description' => tra('Permits editing of a plugin, via a popup form, without needing to edit the whole page.'),
			'type' => 'flag',
			'hint' => 'Requires javascript',
		),
		'wiki_badchar_prevent' => array(
			'name' => tra('Prevent special characters in page names'),
			'description' => tra('Some characters may prevent the pages from being easily accessible from the URL or through wiki links. This option prevents from creating pages with such characters.'),
			'type' => 'flag',
		),
		'wiki_ranking_reload_probability' => array(
			'name' => tra('Page ranking reload probability'),
			'description' => tra('Calculation of page rankings is an expensive task that cannot be performed at every page load. It will be calculated at random page loads based on a dice roll. This option indicates the amount of faces on the dice. Larger numbers lead to less load, but less accurate ranking.'),
			'type' => 'text',
			'size' => 7,
			'filter' => 'digits',
		),
		'wiki_encourage_contribution' => array(
			'name' => tra('Encourage contribution to wiki pages by anonymnous'),
			'description' => tra('When a page is not editable and the user is anonymous, display the edit links anyway. The visitor will be prompted with a login screen and be encouraged to register.'),
			'type' => 'flag',
		),
	);
}

