<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

tiki_setup_events();

function tiki_setup_events()
{
	global $prefs;

	$events = TikiLib::events();
	$events->reset();

	$defer = function ($lib, $function ) {
		return Tiki_Event_Lib::defer($lib, $function);
	};

	if ($prefs['feature_wiki'] == 'y') {
		if ( $prefs['quantify_changes'] == 'y' && $prefs['feature_multilingual'] == 'y' ) {
			$events->bind('tiki.wiki.save', $defer('quantify', 'wiki_update'));
		}

		$prefix = $prefs['feature_wiki_userpage_prefix'];
		if ($prefs['feature_wiki_userpage'] && ! empty($prefix)) {
			$events->bind(
				'tiki.wiki.save',
				function ($args) use ($events, $prefix) {
					global $prefs;
					if ($prefix == substr($args['object'], 0, strlen($prefix))) {
						$user = substr($args['object'], strlen($prefix));
						$events->trigger('tiki.user.update', array('type' => 'user', 'object' => $user));
					}
				}
			);
		}
	}

	if ($prefs['feature_trackers'] == 'y') {
		$events->bind('tiki.trackeritem.save', $defer('trk', 'send_replace_item_notifications'));
		$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_item_geo'));

		if ($prefs['feature_categories'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_categories'));
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_item_auto_categories'));
		}

		if (! empty($prefs['user_trackersync_realname'])) {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_user_realname'));
		}

		if ($prefs['user_trackersync_groups'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_user_groups'));
		}

		if ($prefs['user_trackersync_geo'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_user_geo'));
		}

		if ($prefs['groupTracker'] == 'y') {
			$events->bind('tiki.trackeritem.create', $defer('trk', 'group_tracker_create'));
		}

		if ($prefs['userTracker'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'update_user_account'));
		}

		if ($prefs['feature_freetags'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_freetags'));
		}

		$events->bind('tiki.trackeritem.save', $defer('trk', 'update_create_missing_pages'));

		if ($prefs['trackerfield_computed'] == 'y') {
			$events->bind('tiki.trackeritem.save', array('Tracker_Field_Computed', 'computeFields'));
		}

		if ($prefs['feature_multilingual'] == 'y') {
			$events->bind('tiki.trackeritem.save', array('Tracker_Field_Language', 'update_language'));
		}

		if ($prefs['trackerfield_icon'] == 'y') {
			$events->bind('tiki.trackeritem.save', array('Tracker_Field_Icon', 'updateIcon'));
		}

		$events->bind('tiki.trackeritem.save', $defer('trk', 'update_tracker_summary'));
		$events->bind('tiki.trackeritem.save', $defer('trk', 'invalidate_item_cache'));

		if ($prefs['tracker_refresh_itemlink_detail'] == 'y') {
			$events->bind('tiki.trackeritem.update', $defer('trk', 'refresh_index_on_master_update'));
		}
	}

	if ($prefs['feature_search'] == 'y' && $prefs['unified_incremental_update'] == 'y') {
		$events->bindPriority(100, 'tiki.save', 'tiki_save_refresh_index');
		$events->bindPriority(100, 'tiki.user.save', 'tiki_save_refresh_index');
	}

	if ($prefs['feature_file_galleries'] == 'y') {
		$events->bind('tiki.save', $defer('filegal', 'save_sync_file_backlinks'));
	}

	if ($prefs['dailyreports_enabled_for_new_users'] == 'y') {
		$events->bind('tiki.user.create', array(Reports_Factory::build('Reports_Users'), 'addUserToDailyReports'));
	}

	if ($prefs['scorm_enabled'] == 'y') {
		$events->bind('tiki.file.create', $defer('scorm', 'handle_file_creation'));
		$events->bind('tiki.file.update', $defer('scorm', 'handle_file_update'));
	}

	if ($prefs['feature_forwardlinkprotocol'] == 'y') {
		$events->bind("tiki.wiki.view", 'tiki_wiki_view_forwardlink');
		$events->bind("tiki.wiki.save", 'tiki_wiki_save_forwardlink');
	}

	$events->bind('tiki.save', $defer('tiki', 'plugin_post_save_actions'));

	if ($prefs['activity_custom_events'] == 'y') {
		TikiLib::lib('activity')->bindEvents($events);
	}

	// Chain events
	$events->bind('tiki.wiki.update', 'tiki.wiki.save');
	$events->bind('tiki.wiki.create', 'tiki.wiki.save');
	$events->bind('tiki.wiki.save', 'tiki.save');
	$events->bind('tiki.wiki.view', 'tiki.view');

	$events->bind('tiki.trackeritem.update', 'tiki.trackeritem.save');
	$events->bind('tiki.trackeritem.create', 'tiki.trackeritem.save');
	$events->bind('tiki.trackeritem.save', 'tiki.save');

	$events->bind('tiki.file.update', 'tiki.file.save');
	$events->bind('tiki.file.create', 'tiki.file.save');
	$events->bind('tiki.file.save', 'tiki.save');

	$events->bind('tiki.user.update', 'tiki.user.save');
	$events->bind('tiki.user.create', 'tiki.user.save');
}

function tiki_save_refresh_index($args)
{
	require_once('lib/search/refresh-functions.php');
	$isBulk = isset($args['bulk_import']) && $args['bulk_import'];
	refresh_index($args['type'], $args['object'], ! $isBulk);
}


function tiki_wiki_view_forwardlink($args)
{
	Feed_ForwardLink_Receive::wikiView($args);
	Feed_ForwardLink_PageLookup::wikiView($args);
	Feed_ForwardLink::wikiView($args);
	Feed_TextLink::wikiView($args);
}

function tiki_wiki_save_forwardlink($args)
{
	Feed_ForwardLink::wikiSave($args);
	Feed_TextLink::wikiSave($args);
}
