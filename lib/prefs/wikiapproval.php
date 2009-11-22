<?php

function prefs_wikiapproval_list() {
	return array(
		'wikiapproval_block_editapproved' => array(
			'name' => tra('Force bounce of editing of approved pages to staging'),
			'type' => 'flag',
		),
		'wikiapproval_delete_staging' => array(
			'name' => tra('Delete staging pages at approval'),
			'type' => 'flag',
		),
		'wikiapproval_prefix' => array(
			'name' => tra('Unique page name prefix to indicate staging copy'),
			'type' => 'text',
			'size' => '15',
		),
		'wikiapproval_hideprefix' => array(
			'name' => tra('Hide page name prefix'),
			'type' => 'flag',
		),
		'wikiapproval_sync_categories' => array(
			'name' => tra('Categorize approved pages with categories of staging copy on approval'),
			'type' => 'flag',
		),
		'wikiapproval_update_freetags' => array(
			'name' => tra('Replace freetags with that of staging pages, on approval'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_freetags',
			), 
		),
		'wikiapproval_combine_freetags' => array(
			'name' => tra('Add new freetags of approved copy (into tags field) when editing staging pages'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_freetags',
			), 
		),
	
	
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wikiapproval_master_group' => array(
			'name' => tra('If not in the group, edit is always redirected to the staging page edit'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wikiapproval_staging_category' => array(
			'name' => tra('Staging'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wikiapproval_approved_category' => array(
			'name' => tra('Approved'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-wiki.tpl
	'wikiapproval_outofsync_category' => array(
			'name' => tra('Out-of-sync'),
			'type' => '',
			),
	
	);	
	
}
