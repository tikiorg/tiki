<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wikiapproval_list() {
	global $prefs;
	
	$staging_catree = array();

	if ($prefs['feature_categories'] == 'y') {
		global $categlib;

		include_once ('lib/categories/categlib.php');
		$all_categs = $categlib->get_all_categories();

		$staging_catree['0'] = tra('None');

		foreach ($all_categs as $categ)
		{
			$staging_catree[$categ['categId']] = $categ['categpath'];
		}
	}

	global $userlib;
	$all_groups = $userlib->list_all_groups();

	$staging_groups = array();
	$staging_groups['-1'] = tra('None');

	foreach ($all_groups as $group) {
		$staging_groups[] = $group;
	}
	
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
		'wikiapproval_staging_category' => array(
			'name' =>  tra('Staging'),
			'type' => 'list',
			'options' => $staging_catree,
			'dependencies' => array(
				'feature_categories',
			),
		),
		'wikiapproval_approved_category' => array(
			'name' =>  tra('Approved') . ' ' . tra('(mandatory for feature to work)'),
			'type' => 'list',
			'options' => $staging_catree,
			'dependencies' => array(
				'feature_categories',
			),
		),
		'wikiapproval_outofsync_category' => array(
			'name' =>  tra('Out-of-sync'),
			'type' => 'list',
			'options' => $staging_catree,
			'dependencies' => array(
				'feature_categories',
			),
		),
		'wikiapproval_master_group' => array(
			'name' => tra('If not in the group, edit is always redirected to the staging page edit'),
			'type' => 'list',
			'options' => $staging_groups,
			),
	);	
}
