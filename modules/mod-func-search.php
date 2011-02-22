<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_search_info() {
	return array(
		'name' => tra('Search'),
		'description' => tra('Multi-purpose search module (go or edit page by name and/or search site)'),
		'prefs' => array(), // feature_search_fulltext does not depend on feature_search (apparently?)
		'params' => array(
			'legacy_mode' => array(
				'name' => tra('Legacy Mode'),
				'description' => tra('Setting to emulate previous behaviour.') . ' ' . tra('Default:') . ' ""' . tra(' ("search"=search_box, "page"=search_wiki_page, "quick"=quick_edit)')
			),
			'tiki_search' => array(
				'name' => tra('Tiki'),
				'description' => tra('If set to "y" the search performed is a "Tiki search".') . ' ' . tra('Default:') . ' "n"' . tra(' (full text search)')
			),
			'show_object_filter' => array(
				'name' => tra('Show Search Filter'),
				'description' => tra('If set to "y" shows a dropdown of sections to search.') . ' ' . tra('Default:') . ' "n"' . tra(' (no object filter)')
			),
			'use_autocomplete' => array(
				'name' => tra('Use autocomplete'),
				'description' => tra('If set to "y" input uses autocomplete for pagenames if applicable.') . ' ' . tra('Default:') . ' "y"' . tra(' (use autocomplete)')
			),
			'advanced_search' => array(
				'name' => tra('Advanced search'),
				'description' => tra('Use advanced (boolean) search (full text search only).') . ' ' . tra('Default:') . ' "y"' . tra(' (use advanced search)'),
			),
			'advanced_search_option' => array(
				'name' => tra('Advanced search checkbox'),
				'description' => tra('Show advanced search checkbox (full text search only).') . ' ' . tra('Default:') . ' "n"' . tra(' (advanced search checkbox off)'),
			),
			'advanced_search_help' => array(
				'name' => tra('Advanced search help'),
				'description' => tra('Show advanced search help icon (full text search only).') . ' ' . tra('Default:') . ' "n"' . tra(' (advanced search help off)'),
			),
			'show_search_button' => array(
				'name' => tra('Show Search Button'),
				'description' => tra('Show search button.') . ' ' . tra('Default:') . ' "y"' . tra(' (do show search button)'),
			),
			'show_go_button' => array(
				'name' => tra('Show Go Button'),
				'description' => tra('Show go to page button.') . ' ' . tra('Default:') . ' "y"' . tra(' (do show go button)'),
			),
			'show_edit_button' => array(
				'name' => tra('Show Edit Button'),
				'description' => tra('Show edit button.') . ' ' . tra('Default:') . ' "y"' . tra(' (do show edit button)'),
			),
			'default_button' => array(
				'name' => tra('Default Button'),
				'description' => tra('Action to perform on entering <return>.') . ' ' . tra('Default:') . ' "search"' . tra(' (search|go|edit)'),
			),
			// initially from quick-edit
			'search_action' => array(
				'name' => 'Search Form Action',
				'description' => tra("If set, send the form to the given location (relative to Tiki's root) for processing.") . " " . tra('Default:') . ' tiki-searchresults.php or tiki-searchindex.php (for Tiki search)'
			),
			'search_submit' => array(
				'name' => tra('Edit Submit Label'),
				'description' => tra('The label on the button to submit the form.') . " " . tra('Default:') . ' ' . tra('Search')
			),
			'go_action' => array(
				'name' => 'Go Form Action',
				'description' => tra("If set, send the form to the given location (relative to Tiki's root) for processing.") . " " . tra('Default:') . ' tiki-editpage.php'
			),
			'go_submit' => array(
				'name' => tra('Edit Submit Label'),
				'description' => tra('The label on the button to submit the form.') . " " . tra('Default:') . ' ' . tra('Go')
			),
			'edit_action' => array(
				'name' => 'Edit Form Action',
				'description' => tra("If set, send the form to the given location (relative to Tiki's root) for processing.") . " " . tra('Default:') . ' tiki-editpage.php'
			),
			'edit_submit' => array(
				'name' => tra('Edit Submit Label'),
				'description' => tra('The label on the button to submit the form.') . " " . tra('Default:') . ' ' . tra('Edit')
			),
			'input_size' => array(
				'name' => 'Input size',
				'description' => tra('Size attribute (horizontal, in characters) of the text input field.') . ' ' . tra('Default:') . ' 14'
			),
			'select_size' => array(
				'name' => 'Select size',
				'description' => tra('Size of the Search Filter dropdown list.') . " " . tra('Default:') . " 10"
			),
			'search_heading' => array(
				'name' => tra('Heading'),
				'description' => tra("Optional heading to display at the top of the module's content.")
			),
			'templateId' => array(
				'name' => tra('Edit Template identifier'),
				'description' => tra('If set to a template identifier, the specified template is used for creating new Wiki pages.') . " " . tra('Not set by default.')
			),
			'categId' => array(
				'name' => tra('Category identifier'),
				'description' => tra('If set to a category identifier, pages created through the module are automatically categorized in the specified category.') . " " . tra('Not set by default.')
			),
			
			
		)
	);
}

function module_search( $mod_reference, $smod_params ) {	// modifies $smod_params so uses & reference
	global $smarty, $prefs;
	static $search_mod_usage_counter = 0;
	$smarty->assign('search_mod_usage_counter', ++$search_mod_usage_counter);

	$smarty->assign('module_error', '');
	$smarty->assign_by_ref('smod_params', $smod_params);
	
	// Deal with the two search types (sigh). If the requested search type is disabled but the other one is enabled, use it as a fallback.
	$smod_params['tiki_search'] = isset($smod_params['tiki_search']) && $smod_params['tiki_search'] == 'y';
	
	if ($prefs['feature_search'] == 'n' && $prefs['feature_search_fulltext'] == 'n') {
		$smod_params['tiki_search'] = 'none';
		$smarty->assign('module_error', tra('Search is disabled.'));
		return;
	} else if ($prefs['feature_search'] == 'n' && $smod_params['tiki_search'] == 'y') {
		$smod_params['tiki_search'] = 'n';
	} else if ($prefs['feature_search_fulltext'] == 'n' && $smod_params['tiki_search'] != 'y') {
		$smod_params['tiki_search'] = 'y';
	}
	
	if (isset($smod_params['go_action']) && $smod_params['go_action'] == 'ti') { unset($smod_params['go_action']); }	// temporary fix for 5.0 in case params were truncated in the db
	
	// set up other param defaults
	$defaults = array(
		'legacy_mode' => '',
		'show_object_filter' => 'n',
		'use_autocomplete' => 'y',
		'advanced_search' => 'y',
		'advanced_search_option' => 'n',
		'advanced_search_help' => 'n',
		'show_search_button' => 'y',
		'show_go_button' => 'y',
		'show_edit_button' => 'y',
		'default_button' => 'search',
		'input_size' => 0,
		'select_size' => 10,
		'search_action' => $smod_params['tiki_search'] ? 'tiki-searchindex.php' : 'tiki-searchresults.php',
		'search_submit' => tra("Search"),
		'go_action' => 'tiki-listpages.php',
		'go_submit' => tra("Titles"),
		'edit_action' => 'tiki-editpage.php',
		'edit_submit' => tra("Edit"),
		'default_button' => 'search',
		'search_heading' => '',
		'templateId' => '',
		'categId' => '',
	);
	
	$smod_params = array_merge($defaults, $smod_params);
	
	if ($smod_params['tiki_search'] == 'y') {
		$smod_params['advanced_search'] = 'n';
		$smod_params['advanced_search_option'] = 'n';
		$smod_params['advanced_search_help']   = 'n';
	}
	
	switch ($smod_params['legacy_mode']) {
		case 'quick':		// params from old quick_edit module
			$smod_params['show_search_button']   = 'n';
			$smod_params['show_go_button']   = 'n';
			$smod_params['show_edit_button']   = 'y';
			$smod_params['edit_submit'] = isset($smod_params['submit']) ? $smod_params['submit'] : tra("Create/Edit");
			$smod_params['default_button'] = 'edit';
			$smod_params['edit_action'] = isset($smod_params['action']) ? $smod_params['action'] : 'tiki-editpage.php';
			$smod_params['input_size'] = isset($smod_params['size']) ? $smod_params['size'] : 15;
			$smod_params['search_heading'] = isset($smod_params['mod_quickedit_heading']) ? $smod_params['mod_quickedit_heading'] : $smod_params['search_heading'];
			$smod_params['title']   = tra('Quick Edit a Wiki Page');
			break;
		case 'search':		// params from old search_box module
			$smod_params['tiki_search'] = isset($smod_params['tiki']) ? $smod_params['tiki'] : 'n';
			$smod_params['show_search_button']   = 'y';
			$smod_params['show_go_button']   = 'n';
			$smod_params['show_edit_button']   = 'n';
			$smod_params['advanced_search']   = 'y';
			$smod_params['advanced_search_option']   = 'y';
			$smod_params['advanced_search_help']   = 'y';
			$smod_params['search_submit'] = tra("Go");
			$smod_params['default_button'] = 'search';
			$smod_params['show_object_filter'] = $prefs['feature_search_show_object_filter'];
			break;
		case 'page':		// params from old search_wiki_page module
			$smod_params['show_search_button']   = 'n';
			$smod_params['show_go_button']   = 'y';
			$smod_params['show_edit_button']   = 'n';
			$smod_params['go_submit'] = tra("Go");
			$smod_params['default_button'] = 'go';
			$smod_params['title']   = tra('Search for Wiki Page');
			break;
			
		case '':
		default:
			break;
	}
	
	switch ($smod_params['default_button']) {
		case 'edit':
			$smod_params['default_action'] = $smod_params['edit_action'];
			break;
		case 'go':
			$smod_params['default_action'] = $smod_params['go_action'];
			break;
		case 'search':
		default:
			$smod_params['default_action'] = $smod_params['search_action'];
			break;
	}

	if (($smod_params['show_search_button'] == 'y' || $smod_params['default_action'] == $smod_params['search_action'])
			&& $smod_params['show_edit_button'] == 'n' && $smod_params['show_go_button'] == 'n') {
		$smod_params['use_autocomplete'] = 'n';
	}
	
	if (!empty($_REQUEST['highlight'])) {
		$smod_params['input_value'] = $_REQUEST['highlight'];
	} else if (!empty($_REQUEST['words'])) {
		$smod_params['input_value'] = $_REQUEST['words'];
	} else if (!empty($_REQUEST['find'])) {
		$smod_params['input_value'] = $_REQUEST['find'];
	} else {
		$smod_params['input_value'] = '';
	}
	if (!empty($_REQUEST['where'])) {
		$smod_params['where'] = $_REQUEST['where'];
	} else {
		$smod_params['where'] = '';
	}
	if (!empty($_REQUEST['boolean_last'])) {
		if (!empty($_REQUEST['boolean'])) {
			$smod_params['advanced_search'] = 'y';
		} else {
			$smod_params['advanced_search'] = 'n';
		}
	}
}
