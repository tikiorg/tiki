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
		'wiki_comments_notitle' => array(
			'name' => tra('Disable titles for wiki comments'),
			'description' => tra('Hide the title field on wiki comments and their replies.'),
			'type' => 'flag',
		),
		'wiki_spellcheck' => array(
			'name' => tra('Spell checking'),
			'type' => 'flag',
			'help' => 'Spellcheck',
			'hint' => tra('Requires a separate download'),
		),
		'wiki_edit_section' => array(
			'name' => tra('Edit section'),
			'type' => 'flag',
		),
		'wiki_edit_section_level' => array(
			'name' => tra('Edit section level'),
			'type' => 'list',
			'options' => array(
				'0' => tra('All'),
				'1' => tra('1'),
				'2' => tra('2'),
				'3' => tra('3'),
				'4' => tra('4'),
				'5' => tra('5'),
				'6' => tra('6'),
			),
		),
		'wiki_edit_icons_toggle' => array(
			'name' => tra('Toggle display of section and plugin edit icons'),
			'type' => 'flag',
		),
		'wiki_edit_minor' => array(
			'name' => tra('Allow minor edits'),
			'type' => 'flag',
		),
		'wiki_comments_displayed_default' => array(
			'name' => tra('Display by default'),
			'type' => 'flag',
		),
		'wiki_comments_per_page' => array(
			'name' => tra('Default number per page'),
			'type' => 'text',
			'size' => '5',
		),
		'wiki_comments_default_ordering' => array(
			'name' => tra('Default ordering'),
			'type' => 'list',
			'options' => array(
				'commentDate_desc' => tra('Newest first'),
				'commentDate_asc' => tra('Oldest first'),
				'points_desc' => tra('Points'),
			),
		),
		'wiki_uses_slides' => array(
			'name' => tra('Slideshows'),
			'type' => 'flag',
			'help' => 'Slideshow',
		),
		'wiki_creator_admin' => array(
			'name' => tra('Page creators are admin of their pages'),
			'type' => 'flag',
		),
		'wiki_watch_author' => array(
			'name' => tra('Create watch for author on page creation'),
			'type' => 'flag',
		),
		'wiki_watch_comments' => array(
			'name' => tra('Enable watches on comments'),
			'type' => 'flag',
		),
		'wiki_watch_editor' => array(
			'name' => tra('Enable watch events when I am the editor'),
			'type' => 'flag',
		),
		'wiki_watch_minor' => array(
			'name' => tra('Watch minor edits'),
			'type' => 'flag',
		),
	
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_forum_id' => array(
			'name' => tra('Forum for discussion:'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_backlinks' => array(
			'name' => tra('Backlinks'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_comment' => array(
			'name' => tra('Edit comments'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_comment_len' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_description' => array(
			'name' => tra('Description'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_description_len' => array(
			'name' => tra('Description length:'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_creator' => array(
			'name' => tra('Creator'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_hits' => array(
			'name' => tra('Hits'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_lastmodif' => array(
			'name' => tra('Last modification date'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_lastver' => array(
			'name' => tra('Version'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_links' => array(
			'name' => tra('Links'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_name' => array(
			'name' => tra('Name'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_name_len' => array(
			'name' => tra('Name length:'),
			'type' => 'text',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_size' => array(
			'name' => tra('Size'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_status' => array(
			'name' => tra('Status'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_user' => array(
			'name' => tra('Last modified by'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_versions' => array(
			'name' => tra('Versions'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_language' => array(
			'name' => tra('Language'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_categories' => array(
			'name' => tra('Categories'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_categories_path' => array(
			'name' => tra('Categories path'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_id' => array(
			'name' => tra('Page ID'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_sortorder' => array(
			'name' => tra('Default sort order:'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_list_sortdirection' => array(
			'name' => tra('Descending'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_feature_3d' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_width' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_height' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_navigation_depth' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_feed_animation_interval' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_existing_page_color' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_missing_page_color' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_camera_distance' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_elastic_constant' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_eletrostatic_constant' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_fov' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_friction_constant' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_node_charge' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_node_mass' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_node_size' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_spring_size' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_text_size' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_adjust_camera' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wiki_3d_autoload' => array(
			'name' => '',
			'type' => '',
			),
	
	);
}
