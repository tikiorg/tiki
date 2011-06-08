<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$events = TikiLib::events();

if ($prefs['feature_wiki'] == 'y') {
	if( $prefs['quantify_changes'] == 'y' && $prefs['feature_multilingual'] == 'y' ) {
		$events->bind('tiki.wiki.save', Event_Lib::defer('quantify', 'wiki_update'));
	}
}

if ($prefs['feature_trackers'] == 'y') {
	$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'send_replace_item_notifications'));
	$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_item_geo'));

	if ($prefs['feature_categories'] == 'y') {
		$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_categories'));
		$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_item_auto_categories'));
	}
	
	if (! empty($prefs['user_trackersync_realname'])) {
		$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_user_realname'));
	}

	if ($prefs['user_trackersync_groups'] == 'y') {
		$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_user_groups'));
	}

	if ($prefs['user_trackersync_geo'] == 'y') {
		$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_user_geo'));
	}

	if ($prefs['groupTracker'] == 'y') {
		$events->bind('tiki.trackeritem.create', Event_Lib::defer('trk', 'group_tracker_create'));
	}

	if ($prefs['feature_freetags'] == 'y') {
		$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'sync_freetags'));
	}

	$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'update_create_missing_pages'));

	if ($prefs['tracker_field_computed'] == 'y') {
		$events->bind('tiki.trackeritem.save', array('Tracker_Field_Computed', 'computeFields'));
	}

	$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'update_tracker_summary'));
	$events->bind('tiki.trackeritem.save', Event_Lib::defer('trk', 'invalidate_item_cache'));
}

if ($prefs['unified_incremental_update'] == 'y') {
	$events->bind('tiki.save', 'tiki_save_refresh_index');
}

if ($prefs['feature_file_galleries'] == 'y') {
	$events->bind('tiki.save', Event_Lib::defer('filegal', 'save_sync_file_backlinks'));
}

$events->bind('tiki.save', Event_Lib::defer('tiki', 'plugin_post_save_actions'));

// Chain events
$events->bind('tiki.wiki.update', 'tiki.wiki.save');
$events->bind('tiki.wiki.create', 'tiki.wiki.save');
$events->bind('tiki.wiki.save', 'tiki.save');

$events->bind('tiki.trackeritem.update', 'tiki.trackeritem.save');
$events->bind('tiki.trackeritem.create', 'tiki.trackeritem.save');
$events->bind('tiki.trackeritem.save', 'tiki.save');

function tiki_save_refresh_index($args) {
	require_once('lib/search/refresh-functions.php');
	refresh_index($args['type'], $args['object']);
}

