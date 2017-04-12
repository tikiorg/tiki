<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		if ($prefs['user_trackersync_geo'] == 'y') {
			$events->bind('tiki.trackeritem.save', $defer('trk', 'sync_user_geo'));
		}

		if ($prefs['groupTracker'] == 'y') {
			$events->bind('tiki.trackeritem.create', $defer('trk', 'group_tracker_create'));
		}

		$events->bind('tiki.trackeritem.create', $defer('trk', 'setup_wiki_fields'));
		$events->bind('tiki.trackeritem.update', $defer('trk', 'update_wiki_fields'));
		$events->bind('tiki.trackeritem.delete', $defer('trk', 'delete_wiki_fields'));

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

		// Certain non-read only fields that can be edited outside of using the tracker field do store a value in the
		// tiki_tracker_item_fields database, and therefore need updates of the tracker field value to be in sync, when
		// edited elsewhere. Completely read-only fields don't have this problem as they don't save anything anyway.
		//
		// A possible solution could have been to avoid storing the value in the database altogether and get the value
		// from the canonical source, but there is code that currently could dependd on it and also it might actually
		// be argued in favor of for performance reasons to have the value in the tiki_tracker_item_fields db as well.
		//
		// TODO: freetags field. There is already handling for the Freetags field in wikiplugin_addfreetag.php which
		// is the most likely place it would be edited outside of tracker field but an event would be cleaner.
		//
		if ($prefs['trackerfield_relation'] == 'y') {
			$events->bind('tiki.social.relation.add', array('Tracker_Field_Relation', 'syncRelationAdded'));
			$events->bind('tiki.social.relation.remove', array('Tracker_Field_Relation', 'syncRelationRemoved'));
		}
		if ($prefs['trackerfield_category'] == 'y') {
			$events->bind('tiki.object.categorized', array('Tracker_Field_Category', 'syncCategoryFields'));
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

	if ($prefs['feature_score'] == 'y') {
		TikiLib::lib('score')->bindEvents($events);
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
	$events->bind('tiki.object.categorized', 'tiki.save');

	$events->bind('tiki.user.login', 'tiki.view');
	$events->bind('tiki.user.view', 'tiki.view');
	$events->bind('tiki.user.avatar', 'tiki.save');

	$events->bind('tiki.wiki.update', 'tiki.wiki.save');
	$events->bind('tiki.wiki.create', 'tiki.wiki.save');
	$events->bind('tiki.wiki.save', 'tiki.save');
	$events->bind('tiki.wiki.view', 'tiki.view');
	$events->bind('tiki.wiki.attachfile', 'tiki.save');

	$events->bind('tiki.article.create', 'tiki.article.save');
	$events->bind('tiki.article.save', 'tiki.save');
	$events->bind('tiki.article.delete', 'tiki.save');
	$events->bind('tiki.article.view', 'tiki.view');

	$events->bind('tiki.blog.create', 'tiki.blog.save');
	$events->bind('tiki.blog.save', 'tiki.save');
	$events->bind('tiki.blog.delete', 'tiki.save');
	$events->bind('tiki.blog.view', 'tiki.view');

	$events->bind('tiki.blogpost.create', 'tiki.blogpost.save');
	$events->bind('tiki.blogpost.save', 'tiki.save');
	$events->bind('tiki.blogpost.delete', 'tiki.save');

	$events->bind('tiki.trackeritem.update', 'tiki.trackeritem.save');
	$events->bind('tiki.trackeritem.create', 'tiki.trackeritem.save');
	$events->bind('tiki.trackeritem.save', 'tiki.save');
	$events->bind('tiki.trackeritem.delete', 'tiki.save');
	$events->bind('tiki.trackeritem.rating', 'tiki.rating');
	$events->bind('tiki.trackeritem.view', 'tiki.view');

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
	$events->bind('tiki.file.download', 'tiki.view');

	$events->bind('tiki.filegallery.update', 'tiki.filegallery.save');
	$events->bind('tiki.filegallery.create', 'tiki.filegallery.save');
	$events->bind('tiki.filegallery.delete', 'tiki.filegallery.save');
	$events->bind('tiki.filegallery.save', 'tiki.save');

	$events->bind('tiki.image.create', 'tiki.image.save');
	$events->bind('tiki.image.delete', 'tiki.image.save');
	$events->bind('tiki.image.save', 'tiki.save');
	$events->bind('tiki.image.view', 'tiki.view');

	$events->bind('tiki.imagegallery.create', 'tiki.imagegallery.save');
	$events->bind('tiki.imagegallery.delete', 'tiki.imagegallery.save');
	$events->bind('tiki.imagegallery.save', 'tiki.save');
	$events->bind('tiki.imagegallery.view', 'tiki.view');

	$events->bind('tiki.forum.update', 'tiki.forum.save');
	$events->bind('tiki.forum.create', 'tiki.forum.save');
	$events->bind('tiki.forum.delete', 'tiki.forum.save');
	$events->bind('tiki.forum.save', 'tiki.save');

	$events->bind('tiki.forumpost.create', 'tiki.forumpost.save');
	$events->bind('tiki.forumpost.reply', 'tiki.forumpost.save');
	$events->bind('tiki.forumpost.update', 'tiki.forumpost.save');
	$events->bind('tiki.forumpost.save', 'tiki.save');
	$events->bind('tiki.forumpost.view', 'tiki.view');

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
	$events->bind('tiki.user.message', 'tiki.user.network');

	$events->bind('tiki.social.like.add', 'tiki.social.save');
	$events->bind('tiki.social.like.remove', 'tiki.social.save');
	$events->bind('tiki.social.favorite.add', 'tiki.social.save');
	$events->bind('tiki.social.favorite.remove', 'tiki.social.save');
	$events->bind('tiki.social.relation.add', 'tiki.social.save');
	$events->bind('tiki.social.relation.remove', 'tiki.social.save');
	$events->bind('tiki.social.rating.add', 'tiki.social.save');
	$events->bind('tiki.social.rating.remove', 'tiki.social.save');

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

	// if article indexing is on as part of the rss article generator bind the categorization of objects to ensure
	// that the trackeritem and article are always in sync category-wise
	if (isset($prefs['tracker_article_indexing']) && $prefs['tracker_article_indexing'] == 'y') {
		$events->bind('tiki.object.categorized', $defer('trk','sync_tracker_article_categories'));
	}

	//Check the Addons to see if there are any events to bind
	$api = new TikiAddons_Api_Events();
	$api->bindEvents($events);
}

function tiki_save_refresh_index($args)
{
	if (! isset($args['index_handled'])) {
		require_once('lib/search/refresh-functions.php');
		$isBulk = isset($args['bulk_import']) && $args['bulk_import'];
		refresh_index($args['type'], $args['object'], ! $isBulk);
	}
}
