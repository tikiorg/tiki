<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wiki_list() {

	global $prefs;
	$wiki_forums = array();

	if ($prefs['feature_forums'] == 'y') {
		$all_forums = TikiDb::get()->fetchMap( 'SELECT `forumId`, `name` FROM `tiki_forums` ORDER BY `name` ASC' );

		if ( count( $all_forums ) ) {
			$wiki_forums = $all_forums;
		} else {
			$wiki_forums[''] = tra('None');
		}
	}

	global $prefslib;
	$advanced_columns = $prefslib->getExtraSortColumns();

	$wiki_sort_columns = array_merge( array(
		'pageName' => tra('Name'),
		'lastModif' => tra('LastModif'),
		'created' => tra('Created'),
		'creator' => tra('Creator'),
		'hits' => tra('Hits'),
		'user' => tra('Last editor'),
		'page_size' => tra('Size'),
	), $advanced_columns );

	$comment_sort_orders = array(
		'commentDate_desc' => tra('Newest first'),
		'commentDate_asc' => tra('Oldest first'),
		'points_desc' => tra('Points'),
	);

	foreach( $advanced_columns as $key => $label ) {
		$comment_sort_orders[ $key . '_asc' ] = $label . ' ' . tr('ascending');
		$comment_sort_orders[ $key . '_desc' ] = $label . ' ' . tr('descending');
	}

	return array(
		'wiki_page_regex' => array(
			'name' => tra('Wiki link format'),
			'description' => tra('What level of special characters are accepted as wiki links for page names. Ex.: ((Page &eacute;&agrave;&icirc;))'),
			'hint' => tra('Strict will only be basic characters like a-z and 0-9. If you have accented or special characters in page names, you should not use strict.'),
			'type' => 'list',
			'options' => array(
				'complete' => tra('Complete'),
				'full' => tra('Relaxed'),
				'strict' => tra('Strict'),
			),
		),
		'wiki_show_version' => array(
			'name' => tra('Display page version'),
			'description' => tra('Display the page version information when viewing the page.'),
			'type' => 'flag',
		),
		'wiki_page_name_above' => array(
			'name' => tra('Display page name above page'),
			'description' => tra('Display page name above page instead of inside page.'),
			'type' => 'flag',
		),
		'wiki_pagename_strip' => array(
			'name' => tra('Page name display stripper'),
			'description' => tra('Character to use as a delimiter in the page name. The portion of the name after this character will not be displayed.'),
			'type' => 'text',
			'size' => 5,
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
			'name' => tra('Allow comments per wiki page'),
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
			'hint' => tra('Requires JavaScript'),
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
			'name' => tra('Encourage contribution to wiki pages by anonymous'),
			'description' => tra('When a page is not editable and the user is anonymous, display the edit links anyway. The visitor will be prompted with a login screen and be encouraged to register.'),
			'type' => 'flag',
		),
		'wiki_timeout_warning' => array(
			'name' => tra('Warn before page lock timeout'),
			'description' => tra('Provide a JavaScript alert before the user\'s lock on a page times out.'),
			'type' => 'flag',
		),
		'wiki_dynvar_style' => array(
			'name' => tra('Dynamic variables'),
			'description' => tra('Global snippets of text that can be included in wiki pages and edited in place.'),
			'type' => 'list',
			'help' => 'Dynamic+Variable',
			'options' => array(
				'disable' => tra('Disabled'),
				'single' => tra('Single (%varname%)'),
				'double' => tra('Double (%%varname%%)'),
			),
		),
		'wiki_dynvar_multilingual' => array(
			'name' => tra('Multilingual dynamic variables'),
			'description' => tra('Make dynamic variable content language specific.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_multilingual',
			),
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
			'name' => tra('Default Ordering'),
			'type' => 'list',
			'options' => $comment_sort_orders,
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
		'wiki_list_id' => array(
			'name' => tra('Page ID'),
			'type' => 'flag',
		),
		'wiki_list_name' => array(
			'name' => tra('Name'),
			'type' => 'flag',
		),
		'wiki_list_name_len' => array(
			'name' => tra('Name length'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_list_hits' => array(
			'name' => tra('Hits'),
			'type' => 'flag',
		),
		'wiki_list_lastmodif' => array(
			'name' => tra('Last modification date'),
			'type' => 'flag',
		),
		'wiki_list_creator' => array(
			'name' => tra('Creator'),
			'type' => 'flag',
		),
		'wiki_list_user' => array(
			'name' => tra('Last modified by'),
			'type' => 'flag',
		),
		'wiki_list_lastver' => array(
			'name' => tra('Version'),
			'type' => 'flag',
		),
		'wiki_list_comment' => array(
			'name' => tra('Edit comments'),
			'type' => 'flag',
		),
		'wiki_list_comment_len' => array(
			'name' => tra('Edit Comment length'),
			'type' => 'text',
			'size' => '3',
		),
		'wiki_list_description' => array(
			'name' => tra('Description'),
			'type' => 'flag',
		),
		'wiki_list_description_len' => array(
			'name' => tra('Description length'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
		),
		'wiki_list_status' => array(
			'name' => tra('Status'),
			'type' => 'flag',
		),
		'wiki_list_versions' => array(
			'name' => tra('Versions'),
			'type' => 'flag',
		),
		'wiki_list_links' => array(
			'name' => tra('Links'),
			'type' => 'flag',
		),
		'wiki_list_backlinks' => array(
			'name' => tra('Backlinks'),
			'type' => 'flag',
		),
		'wiki_list_size' => array(
			'name' => tra('Size'),
			'type' => 'flag',
		),
		'wiki_list_language' => array(
			'name' => tra('Language'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_multilingual',
			),
		),
		'wiki_list_categories' => array(
			'name' => tra('Categories'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_categories',
			),
		),
		'wiki_list_categories_path' => array(
			'name' => tra('Categories path'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_categories',
			),
		),
		'wiki_list_sortorder' => array(
			'name' => tra('Default sort order'),
			'type' => 'list',
			'options' => $wiki_sort_columns,
		),
		'wiki_list_sortdirection' => array(
			'name' => tra('Sort Direction'),
			'type' => 'radio',
			'options' => array(
				'desc' => tra('Descending'),
				'asc' => tra('Ascending'),
			),
		),
		'wiki_list_rating' => array(
			'name' => tra('Rating'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_polls',
				'feature_wiki_ratings',
			),
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
			'name' => tra('Browser width'),
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
		'wiki_forum_id' => array(
			'name' => tra('Forum for discussion'),
			'type' => 'list',
			'options' => $wiki_forums,
		),
		'wiki_keywords' => array(
			'name' => tra('Keywords'),
			'description' => tra('Allow to manage keywords on a per-page basis.'),
			'type' => 'flag',
		),
		'wiki_likepages_samelang_only' => array(
			'name' => tra('Similar pages only listed in same language'),
			'description' => tra('When listing similar pages, such as in missing page 404, only display pages in the same language as the request.'),
			'type' => 'flag',
			'dependencies' => array( 'feature_multilingual' ),
		),
		'wiki_mandatory_edit_summary' => array(
			'name' => tra('Mandatory edit summary on wiki pages'),
			'description' => tra('Reject save attempts not providing an edit summary to describe the changes made.'),
			'type' => 'flag',
		),
		'wiki_structure_bar_position' => array(
			'name' => tra('Structure navigation bar location'),
			'description' => tra('Displays Next/Back buttons, breadcrumbs, and form to add a new page.'),
			'type' => 'list',
			'options' => array(
				'top' => tra('Top'),
				'bottom' => tra('Bottom'),
				'both' => tra('Both'),
			),
		),
		'wiki_backlinks_name_len' => array(
			'name' => tra('Name length'),
			'description' => tra('Maximum length (characters) to display/truncate for backlink page names. Use "0" for no truncation.'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
			'dependencies' => array( 'feature_backlinks' ),
		),
		'wiki_simple_ratings' => array(
			'name' => tra('Simple wiki ratings'),
			'description' => tra('Enable users to rate articles based on a simple numeric scale.'),
			'type' => 'flag',
		),
		'wiki_simple_ratings_options' => array(
			'name' => tra('Wiki rating options'),
			'description' => tra('List of options available for the rating of wiki pages.'),
			'type' => 'text',
			'separator' => ',',
			'filter' => 'int',
		),
		'wiki_prefixalias_tokens' => array(
			'name' => tra('Redirect pages using these prefix alias semantic links'),
			'description' => tra('Comma separated list of prefixes of which pages will be redirected to page with semantic link'),
			'type' => 'text',
			'help' => 'Semantic+Alias',
			'size' => '30',
			'dependencies' => array(
				'feature_wiki_1like_redirection',
			),
		),		
	);
}
