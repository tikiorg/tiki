<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_wiki.php,v 1.77.2.10 2008-03-10 19:39:55 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST["dump"])) {
	check_ticket('admin-inc-wiki');
	include ("lib/tar.class.php");

	error_reporting (E_ERROR | E_WARNING);
	$adminlib->dump();
}

// Included for the forum dropdown
include_once ("lib/commentslib.php");

if (isset($_REQUEST["createtag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if ($adminlib->tag_exists($_REQUEST["tagname"])) {
		$msg = tra("Tag already exists");
		$access->display_error(basename(__FILE__), $msg);
	}

	$adminlib->create_tag($_REQUEST["tagname"]);
}

if (isset($_REQUEST["restoretag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if (!$adminlib->tag_exists($_REQUEST["tagname"])) {
		$msg = tra("Tag not found");
		$access->display_error(basename(__FILE__), $msg);
	}

	$adminlib->restore_tag($_REQUEST["tagname"]);
}

if (isset($_REQUEST["removetag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	$adminlib->remove_tag($_REQUEST["tagname"]);
}

if (isset($_REQUEST["wikiattprefs"])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('feature_wiki_attachments');
	simple_set_value('w_use_db');
	simple_set_value('w_use_dir');
	simple_set_toggle('w_displayed_default');
}

if (isset($_REQUEST["wikiprefs"])) {
	check_ticket('admin-inc-wiki');
	simple_set_value('wiki_comments_per_page');
	simple_set_value('wiki_comments_default_ordering');
	simple_set_toggle('wiki_comments_displayed_default');
}

if (isset($_REQUEST["rmvunusedpic"])) {
	check_ticket('admin-inc-wiki');
	$adminlib->remove_unused_pictures();
}

if (isset($_REQUEST["setwikihome"])) {
	check_ticket('admin-inc-wiki');
	simple_set_value('wikiHomePage'); 
}

if (isset($_REQUEST["wikidiscussprefs"])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('feature_wiki_discuss');
	simple_set_value('wiki_forum_id'); 
}

if (isset($_REQUEST["setwikiregex"])) {
	check_ticket('admin-inc-wiki');
	simple_set_value('wiki_page_regex');
	simple_set_value('wiki_pagename_strip');
}

if (isset($_REQUEST['wikilistprefs'])) {
	check_ticket('admin-inc-wiki');
	$pref_toggles = array(
	'wiki_list_name',
	'wiki_list_hits',
	'wiki_list_lastmodif',
	'wiki_list_creator',
	'wiki_list_user',
	'wiki_list_lastver',
	'wiki_list_comment',
	'wiki_list_status',
	'wiki_list_versions',
	'wiki_list_links',
	'wiki_list_backlinks',
	'wiki_list_size',
	'wiki_list_language',
	'wiki_list_categories',
	'wiki_list_categories_path',
	'wiki_list_id',
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	simple_set_int('wiki_list_name_len');
	simple_set_int('wiki_list_comment_len');
	simple_set_value('wiki_list_sortorder');
	simple_set_value('wiki_list_sortdirection');
}

if (isset($_REQUEST["wikifeatures"])) {
	check_ticket('admin-inc-wiki');
	$pref_toggles = array(
	'feature_lastChanges',
	'feature_wiki_comments',
	'feature_wiki_pictures',
	'feature_wiki_description',
	'wiki_spellcheck',
	'feature_warn_on_edit',
	'feature_page_title',
	'feature_wiki_pageid',
	'feature_wikiwords',
	'feature_wikiwords_usedash',
	'feature_wiki_plurals',
	'feature_wiki_paragraph_formatting',
	'feature_wiki_paragraph_formatting_add_br',
	'feature_dump',
	'feature_wiki_export',
	'feature_wiki_rankings',
	'feature_wiki_ratings',
	'feature_wiki_footnotes',
	'feature_wiki_monosp',
	'feature_wiki_usrlock',
	'feature_wiki_undo',
	'feature_wiki_icache',
	'feature_wiki_import_html',
	'wiki_creator_admin',
	'feature_wiki_templates',
	'feature_wiki_multiprint',
	// 'feature_wiki_pdf',
	'feature_listPages',
	'feature_history',
	'feature_source',
	'feature_sandbox',
	'feature_wiki_print',
	'feature_wiki_replace',
	'feature_antibot',
	'feature_backlinks',
	'feature_likePages',
	'feature_userVersions',
	'wiki_uses_slides',
	'feature_wiki_open_as_structure',
	'feature_wiki_make_structure',
	'feature_wiki_categorize_structure',
	'feature_create_webhelp',
	'feature_wiki_ext_icon',
	'feature_wiki_userpage',
	'feature_wiki_protect_email',
	'feature_wiki_1like_redirection',
	'feature_wiki_show_hide_before',
	'feature_wiki_allowhtml',
	'wiki_show_version',
	'feature_wiki_import_page',
	'wiki_edit_section',
  'feature_actionlog_bytes',
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	
	$pref_values = array(
	'wiki_cache',
	'warn_on_edit_time',
	'feature_wiki_tables',
	'feature_wiki_userpage_prefix',
	'wiki_authors_style',
	'feature_wiki_mandatory_category',
	'wiki_page_navigation_bar'
	);
	foreach ($pref_values as $value) {
		simple_set_value($value);
	}
}

if (isset($_REQUEST["wikisetprefs"])) {
	check_ticket('admin-inc-wiki');
	simple_set_value('maxVersions');
	simple_set_value('keep_versions');
	simple_set_value('default_wiki_diff_style');
	simple_set_toggle('feature_wiki_history_ip');
	simple_set_toggle('feature_wiki_history_full');
}



if (isset($_REQUEST["wikiset3d"])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('wiki_feature_3d');
	simple_set_value('wiki_3d_width');
	simple_set_value('wiki_3d_height');
	simple_set_value('wiki_3d_navigation_depth');
	simple_set_value('wiki_3d_feed_animation_interval');
	simple_set_value('wiki_3d_feed_animation_interval');
	simple_set_value('wiki_3d_existing_page_color');
	simple_set_value('wiki_3d_missing_page_color');
	simple_set_value('wiki_3d_camera_distance');
	simple_set_value('wiki_3d_fov');
	simple_set_value('wiki_3d_node_size');
	simple_set_value('wiki_3d_text_size');
	simple_set_value('wiki_3d_friction_constant');
	simple_set_value('wiki_3d_elastic_constant');
	simple_set_value('wiki_3d_eletrostatic_constant');
	simple_set_value('wiki_3d_spring_size');
	simple_set_value('wiki_3d_node_mass');
	simple_set_value('wiki_3d_node_charge');

	if (isset($_REQUEST["wiki_3d_autoload"]) && $_REQUEST["wiki_3d_autoload"] == "on") {
	    $tikilib->set_preference("wiki_3d_autoload", 'true');
	} else {
	    $tikilib->set_preference("wiki_3d_autoload", 'false');
	}

	if (isset($_REQUEST["wiki_3d_adjust_camera"]) && $_REQUEST["wiki_3d_adjust_camera"] == "on") {
	    $tikilib->set_preference("wiki_3d_adjust_camera", 'true');
	} else {
	    $tikilib->set_preference("wiki_3d_adjust_camera", 'false');
	}

}

if(isset($_REQUEST["wikisetwatch"])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('wiki_watch_author');
	simple_set_toggle('wiki_watch_comments');
	simple_set_toggle('wiki_watch_editor');
	simple_set_toggle('wiki_watch_minor');
}

if (isset($_REQUEST["wikiapprovalprefs"])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('feature_wikiapproval');
	simple_set_value('wikiapproval_staging_category');
	simple_set_value('wikiapproval_approved_category');
	simple_set_value('wikiapproval_outofsync_category');
	simple_set_value('wikiapproval_prefix');
	simple_set_toggle('wikiapproval_hideprefix');
	simple_set_toggle('wikiapproval_block_editapproved');	
	simple_set_toggle('wikiapproval_sync_categories');
	simple_set_toggle('wikiapproval_update_freetags');
	simple_set_toggle('wikiapproval_combine_freetags');
	simple_set_toggle('wikiapproval_delete_staging');
	simple_set_value('wikiapproval_master_group');
}

$options_sortorder = array(
						   tra('Name')=>'pageName',
						   tra('LastModif')=>'lastModif',
						   tra('Created')=>'created',
						   tra('Creator')=>'creator',
						   tra('Hits')=>'hits',
						   tra('Last editor')=>'user',
						   tra('Size')=>'page_size'
						   );
$smarty->assign_by_ref('options_sortorder', $options_sortorder);

if ($prefs['feature_forums'] == 'y') {
	$commentslib = new Comments($dbTiki);
	$all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');
	$smarty->assign_by_ref("all_forums", $all_forums["data"]);
}
if ($prefs['feature_categories'] == 'y') {
	include_once('lib/categories/categlib.php');
	$catree = $categlib->get_all_categories();
	$smarty->assign('catree', $catree);
}
$all_groups = $userlib->list_all_groups();
$smarty->assign_by_ref('all_groups', $all_groups);

$tags = $adminlib->get_tags();
$smarty->assign_by_ref("tags", $tags);

ask_ticket('admin-inc-wiki');
?>
