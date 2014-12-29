<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


tiki_setup_events();

register_shutdown_function(function () {
	TikiLib::events()->trigger('tiki.process.shutdown', []);
});

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
						$events->trigger(
							'tiki.user.update',
							array(
								'type' => 'user',
								'object' => $user,
							)
						);
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
			$events->bind('tiki.trackeritem.rating', array('Tracker_Field_Computed', 'computeFields'));
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
		$events->bind('tiki.trackeritem.rating', $defer('trk', 'invalidate_item_cache'));

		if ($prefs['tracker_refresh_itemlink_detail'] == 'y') {
			$events->bind('tiki.trackeritem.update', $defer('trk', 'refresh_index_on_master_update'));
		}

		if ($prefs['tracker_wikirelation_synctitle'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'rename_linked_page'));
		}
	}

	if ($prefs['feature_search'] == 'y' && $prefs['unified_incremental_update'] == 'y') {
		$events->bindPriority(100, 'tiki.save', 'tiki_save_refresh_index');
		$events->bindPriority(100, 'tiki.user.save', 'tiki_save_refresh_index');
		$events->bindPriority(100, 'tiki.social.save', 'tiki_save_refresh_index');
		$events->bindPriority(100, 'tiki.rating', 'tiki_save_refresh_index');
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

	if ($prefs['feature_futurelinkprotocol'] == 'y') {
		if ($prefs['feature_wikilingo'] == 'y') {
			$events->bind("tiki.wiki.view", $defer('wlte', 'wikilingo_flp_view'));
			$events->bind("tiki.wiki.save", $defer('wlte', 'wikilingo_flp_save'));
		} else {
			$events->bind("tiki.wiki.view", $defer('wlte', 'tiki_wiki_view_pastlink'));
			$events->bind("tiki.wiki.save", $defer('wlte', 'tiki_wiki_save_pastlink'));
		}
	}

	if ($prefs['goal_enabled'] == 'y') {
		TikiLib::lib('goalevent')->bindEvents($events);
	}

	$events->bind('tiki.save', $defer('tiki', 'object_post_save'));

	if ($prefs['activity_basic_events'] == 'y') {
		TikiLib::lib('activity')->bindBasicEvents($events);
	}

	if ($prefs['activity_custom_events'] == 'y') {
		try {
			TikiLib::lib('activity')->bindCustomEvents($events);
		} catch (Exception $e) {
			TikiLib::lib('errorreport')->report($e->getMessage());
		}
	}

	if ($prefs['storedsearch_enabled'] == 'y' && $prefs['monitor_enabled'] == 'y') {
		$events->bind('tiki.query.hit', $defer('storedsearch', 'handleQueryNotification'));
	}

	if ($prefs['monitor_enabled'] == 'y') {
		TikiLib::lib('monitor')->bindEvents($events);
	}

	if ($prefs['mustread_enabled'] == 'y') {
		$events->bind('tiki.trackeritem.create', ['Services_MustRead_Controller', 'handleItemCreation']);
		$events->bind('tiki.user.create', ['Services_MustRead_Controller', 'handleUserCreation']);
	}

	// If the parameter is supplied by the web server, Tiki will expose the username as a response header
	if (! empty($_SERVER['TIKI_HEADER_REPORT_USER'])) {
		$events->bind('tiki.process.render', function () {
			global $user;
			if ($user) {
				header('X-Remote-User: ' . $user);
			}
		});
	}

	// If the parameter is supplied by the web server, Tiki will expose the object type and id as a response header
	if (! empty($_SERVER['TIKI_HEADER_REPORT_OBJECT'])) {
		$events->bind('tiki.process.render', function () {
			if (function_exists('current_object') && $object = current_object()) {
				header("X-Current-Object: {$object['type']}:{$object['object']}");
			}
		});
	}

	// Chain events
	$events->bind('tiki.wiki.update', 'tiki.wiki.save');
	$events->bind('tiki.wiki.create', 'tiki.wiki.save');
	$events->bind('tiki.wiki.save', 'tiki.save');
	$events->bind('tiki.wiki.view', 'tiki.view');

	$events->bind('tiki.trackeritem.update', 'tiki.trackeritem.save');
	$events->bind('tiki.trackeritem.create', 'tiki.trackeritem.save');
	$events->bind('tiki.trackeritem.save', 'tiki.save');
	$events->bind('tiki.trackeritem.delete', 'tiki.save');
	$events->bind('tiki.trackeritem.rating', 'tiki.rating');

	$events->bind('tiki.trackerfield.update', 'tiki.trackerfield.save');
	$events->bind('tiki.trackerfield.create', 'tiki.trackerfield.save');
	$events->bind('tiki.trackerfield.delete', 'tiki.save');
	$events->bind('tiki.trackerfield.save', 'tiki.save');

	$events->bind('tiki.tracker.update', 'tiki.tracker.save');
	$events->bind('tiki.tracker.create', 'tiki.tracker.save');
	$events->bind('tiki.tracker.delete', 'tiki.save');
	$events->bind('tiki.tracker.save', 'tiki.save');

	$events->bind('tiki.category.update', 'tiki.category.save');
	$events->bind('tiki.category.create', 'tiki.category.save');
	$events->bind('tiki.category.delete', 'tiki.category.save');
	$events->bind('tiki.category.save', 'tiki.save');

	$events->bind('tiki.file.update', 'tiki.file.save');
	$events->bind('tiki.file.create', 'tiki.file.save');
	$events->bind('tiki.file.delete', 'tiki.file.save');
	$events->bind('tiki.file.save', 'tiki.save');

	$events->bind('tiki.filegallery.update', 'tiki.filegallery.save');
	$events->bind('tiki.filegallery.create', 'tiki.filegallery.save');
	$events->bind('tiki.filegallery.delete', 'tiki.filegallery.save');
	$events->bind('tiki.filegallery.save', 'tiki.save');

	$events->bind('tiki.forum.update', 'tiki.forum.save');
	$events->bind('tiki.forum.create', 'tiki.forum.save');
	$events->bind('tiki.forum.delete', 'tiki.forum.save');
	$events->bind('tiki.forum.save', 'tiki.save');

	$events->bind('tiki.forumpost.create', 'tiki.forumpost.save');
	$events->bind('tiki.forumpost.reply', 'tiki.forumpost.save');
	$events->bind('tiki.forumpost.update', 'tiki.forumpost.save');
	$events->bind('tiki.forumpost.save', 'tiki.save');

	$events->bind('tiki.group.update', 'tiki.group.save');
	$events->bind('tiki.group.create', 'tiki.group.save');
	$events->bind('tiki.group.delete', 'tiki.save');
	$events->bind('tiki.group.save', 'tiki.save');

	$events->bind('tiki.comment.post', 'tiki.comment.save');
	$events->bind('tiki.comment.reply', 'tiki.comment.save');
	$events->bind('tiki.comment.update', 'tiki.comment.save');
	$events->bind('tiki.comment.save', 'tiki.save');

	$events->bind('tiki.user.groupjoin', 'tiki.user.update');
	$events->bind('tiki.user.groupleave', 'tiki.user.update');
	$events->bind('tiki.user.update', 'tiki.user.save');
	$events->bind('tiki.user.create', 'tiki.user.save');

	$events->bind('tiki.user.follow.add', 'tiki.user.network');
	$events->bind('tiki.user.follow.incoming', 'tiki.user.network');
	$events->bind('tiki.user.friend.add', 'tiki.user.network');

	$events->bind('tiki.social.like.add', 'tiki.social.save');
	$events->bind('tiki.social.like.remove', 'tiki.social.save');
	$events->bind('tiki.social.favorite.add', 'tiki.social.save');
	$events->bind('tiki.social.favorite.remove', 'tiki.social.save');
	$events->bind('tiki.social.relation.add', 'tiki.social.save');
	$events->bind('tiki.social.relation.remove', 'tiki.social.save');

	$events->bind('tiki.query.critical', 'tiki.query.hit');
	$events->bind('tiki.query.high', 'tiki.query.hit');
	$events->bind('tiki.query.low', 'tiki.query.hit');

	$events->bind('tiki.mustread.addgroup', 'tiki.save');
	$events->bind('tiki.mustread.adduser', 'tiki.save');
	$events->bind('tiki.mustread.complete', 'tiki.save');

	$events->bind('tiki.mustread.completed', 'tiki.save');
	$events->bind('tiki.mustread.required', 'tiki.save');

	if (function_exists('fastcgi_finish_request')) {
		// If available, try to send everything to the user at this point
		$events->bindPriority(-10, 'tiki.process.shutdown', 'fastcgi_finish_request');
	}
}

function tiki_save_refresh_index($args)
{
	if (! isset($args['index_handled'])) {
		require_once('lib/search/refresh-functions.php');
		$isBulk = isset($args['bulk_import']) && $args['bulk_import'];
		refresh_index($args['type'], $args['object'], ! $isBulk);
	}
}
