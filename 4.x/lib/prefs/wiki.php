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
		'wiki_dft_list_pages_lang_to_current' => array(
			'name' => tra('Display by default only pages in current language'),
			'description' => tra('In Wiki page lists, display by default only pages in current language'),
			'type' => 'flag',
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
		'wiki_comments_allow_per_page' => array(
			'name' => tra('Allow comments per page'),
			'description' => tra('Enable control for comments on wiki pages individually.'),
			'type' => 'list',
			'options' => array(
				'n' => tra('Disable'),
				'y' => tra('Enable (default On)'),
				'o' => tra('Enable (default Off)'),
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
		'wiki_feature_3d' => array(
			'name' => tra('Enable wiki 3D browser'),
			'type' => 'flag',
		),
		'wiki_3d_autoload' => array(
			'name' => tra('Load page on navigation'),
			'type' => 'flag',
		),
		'wiki_3d_width' => array(
			'name' => 'Browser width',
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_height' => array(
			'name' => tra('Browser height'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_navigation_depth' => array(
			'name' => tra('Navigation depth'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_node_size' => array(
			'name' => tra('Node size'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_text_size' => array(
			'name' => tra('Text size'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_spring_size' => array(
			'name' => tra('Spring (connection) size'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_existing_page_color' => array(
			'name' => tra('Existing page node color'),
			'type' => 'text',
			'size' => '8',
			),
		'wiki_3d_missing_page_color' => array(
			'name' => tra('Missing page node color'),
			'type' => 'text',
			'size' => '8',
		),
		'wiki_3d_adjust_camera' => array(
			'name' => tra('Camera distance adjusted relative to nearest node'),
			'type' => 'flag',
		),
		'wiki_3d_camera_distance' => array(
			'name' => tra('Camera distance'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_fov' => array(
			'name' => tra('Field of view'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_feed_animation_interval' => array(
			'name' => tra('Feed animation interval (milisecs)'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_friction_constant' => array(
			'name' => tra('Friction constant'),
			'type' => 'text',
			'size' => '4',
		),
		'wiki_3d_elastic_constant' => array(
			'name' => tra('Elastic constant'),
			'type' => 'text',
			'size' => '4',
		),
		'wiki_3d_eletrostatic_constant' => array(
			'name' => tra('Eletrostatic constant'),
			'type' => 'text',
			'type' => 'text',
			'size' => '5',
		),
		'wiki_3d_node_mass' => array(
			'name' => tra('Node mass'),
			'type' => 'text',
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_3d_node_charge' => array(
			'name' => tra('Node charge'),
			'type' => 'text',
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
	);
}

