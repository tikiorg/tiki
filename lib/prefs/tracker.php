<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tracker_list()
{
	return [
		'tracker_remote_sync' => [
			'name' => tr('Synchronize remote tracker'),
			'description' => tr('Allows a tracker to be cloned on a remote host and synchronize the data locally on demand.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'tracker_refresh_itemlink_detail' => [
			'name' => tr('Refresh item link items when the master is modified'),
			'description' => tr('To be used when item link is used in trackers so that the index remains in good shape when data on the master that is indexed with the detail is modified and used to search on.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'tracker_clone_item' => [
			'name' => tr('Clone tracker items'),
			'description' => tr('Allow copying tracker item information to a new tracker item.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'tracker_insert_allowed' => [
			'name' => tr('Trackers available for insertion from wiki pages'),
			'description' => tr('List of tracker IDs made available when attempting to insert'),
			'type' => 'text',
			'filter' => 'int',
			'separator' => ',',
			'default' => [],
			'profile_reference' => 'tracker',
		],
		'tracker_change_field_type' => [
			'name' => tr('Change Field Types'),
			'description' => tr('Allow field type to be changed after creation.'),
			'type' => 'flag',
			'default' => 'n',
			'warning' => tra('Use with care!'),
		],
		'tracker_show_comments_below' => [
			'name' => tr('Show comments below items'),
			'description' => tr('Show comments for a tracker item below the item itself as in other trackers, instead of enclosed in a tab'),
			'type' => 'flag',
			'default' => 'n',
		],
		'tracker_legacy_insert' => [
			'name' => tr('Use legacy tracker insertion screen'),
			'description' => tr('Use the legacy tracker insertion screen (a tab) instead of a popup dialog.') .
				'<br>' .
				tr('Useful for older trackers using deprecated fields such as Image and File'),
			'type' => 'flag',
			'default' => 'n',
		],
		'tracker_status_in_objectlink' => [
			'name' => tr('Show tracker status in objectlink'),
			'description' => tr('Sets whether we want to show the status when doing an objectlink. This can be used if you want the status to show in tracker screens but not in outputs.'),
			'type' => 'flag',
			'default' => 'y',
		],
		'tracker_wikirelation_synctitle' => [
			'name' => tr('Sync wiki page name to linked tracker field'),
			'description' => tr('When the wiki page is renamed or when the value of the tracker field that is linked to the wiki page is changed, make the corresponding change as well to the other linked via tiki.wiki.linkedfield relation.'),
			'type' => 'flag',
			'tags' => ['advanced'],
			'default' => 'n',
		],
		'tracker_wikirelation_redirectpage' => [
			'name' => tr('Redirect page views to the linked tracker item'),
			'description' => tr('Redirect the user to the tracker item when trying to view a wiki page that has a linked tracker item via tiki.wiki.linkeditem relation. Can be bypassed by adding &admin (?admin in sefurl) to the URL.'),
			'warning' => tra('To get to the wiki page after activation, you can add &admin (?admin in sefurl) to the URL.'),
			'type' => 'flag',
			'tags' => ['advanced'],
			'default' => 'n',
		],
		'tracker_article_tracker' => [
			 'name' => tr('Tracker for articles'),
			 'description' => tr('Have a tracker to supplement article information'),
			 'type' => 'flag',
			'tags' => ['advanced'],
			 'default' => 'n',
			 'dependencies' => ['feature_articles', 'feature_trackers', 'tracker_article_trackerId'],
		 ],
		 'tracker_article_trackerId' => [
			 'name' => tr('Tracker ID of tracker for articles'),
			 'description' => tr('The tracker that is for articles must contain an "Articles" field'),
			 'type' => 'text',
			'tags' => ['advanced'],
			 'size' => '3',
			 'filter' => 'digits',
			 'default' => '',
			 'profile_reference' => 'tracker',
		 ],
		'tracker_article_indexing' => [
			'name' => tr("Index article info in tracker item"),
			'description' => tr('Sets whether the article info should be indexed in the tracker item. This automatically sets the article field to read-only and sets up categorization synchronization between articles and tracker items.'),
			'type' => 'flag',
			'tags' => ['advanced'],
			'default' => 'n',
			'dependencies' => ['feature_articles', 'feature_trackers', 'tracker_article_trackerId', 'tracker_article_tracker'],
		],
		'tracker_tabular_enabled' => [
			'name' => tr('Tracker tabular'),
			'description' => tr('Allows management of import/export tracker profiles and management of custom list formats.'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => ['feature_trackers'],
			'tags' => ['advanced'],
			'help' => 'Tracker+Tabular',
		],
		'tracker_always_notify' => [
			'name' => tr('Always notify watchers'),
			'description' => tr('Send item updated notifications to watchers even if nothing has changed.'),
			'type' => 'flag',
			'default' => 'y',
			'dependencies' => ['feature_trackers'],
		],
		'tracker_force_fill' => [
			'name' => tr('Force users to fill tracker information.'),
			'description' => tr("Forces a user to fill in a tracker form if they haven't already by prompting them with a module  "),
			'help' => 'Force+Fill+Tracker',
			'type' => 'flag',
			'tags' => ['advanced'],
			'default' => 'n',
			'dependencies' => ['feature_trackers'],
		],
		'tracker_force_tracker_id' => [
			'name' => tr('Tracker ID of tracker for force-filling'),
			'description' => tr('The tracker that is for articles must contain an "Articles" field'),
			'type' => 'text',
			'tags' => ['advanced'],
			'size' => '3',
			'filter' => 'digits',
			'default' => '',
			'dependencies' => ['tracker_force_fill'],
		],
		'tracker_force_mandatory_field' => [
			'name' => tr('Mandatory tracker field to check for force-filling'),
			'description' => tr('The permname of field that is checked to see if user has completed the form. If field is empty, user has not completed it.'),
			'type' => 'text',
			'tags' => ['advanced'],
			'default' => '',
			'dependencies' => ['tracker_force_fill'],
		],
		'tracker_force_tracker_fields' => [
			'name' => tr('Fields that are asked for in the modal for force-filling'),
			'description' => tr('Comma-separated permnames of fields that are asked for in the modal for force-filling. If empty, all fields are asked for'),
			'type' => 'text',
			'tags' => ['advanced'],
			'default' => '',
			'dependencies' => ['tracker_force_fill'],
		],
		'tracker_prefixalias_on_links' => [
			'name' => tr('Tracker item links use prefix alias'),
			'description' => tr('Links for tracker items will use the prefix alias automatically everywhere.'),
			'type' => 'flag',
			'default' => 'y',
			'dependencies' => ['feature_trackers','feature_sefurl', 'feature_sefurl_tracker_prefixalias'],
		],
		'tracker_report_resize_button' => [
			'name' => tr('Tracker report resize button'),
			'description' => tr('Adds a button to resize long tracker reports'),
			'type' => 'flag',
			'default' => 'n',
		],
	];
}
