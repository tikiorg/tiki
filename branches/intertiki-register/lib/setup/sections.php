<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$sections = array(
	'wiki page' => array(
		'feature' => 'feature_wiki',
		'key' => 'page',
		'itemkey' => '',
	),
	'blogs' => array(
		'feature' => 'feature_blogs',
		'key' => 'blogId',
		'itemkey' => 'postId',
	),
	'file_galleries' => array(
		'feature' => 'feature_file_galleries',
		'key' => 'page',
		'itemkey' => 'fileId',
	),
	'galleries' => array(
		'feature' => 'feature_galleries',
		'key' => 'galleryId',
		'itemkey' => 'imageId',
	),
	'forums' => array(
		'feature' => 'feature_forums',
		'key' => 'forumId',
		'itemkey' => 'postId',
	),
	'cms' => array(
		'feature' => 'feature_articles',
		'key' => 'topicId',
		'itemkey' => 'articleId',
	),
	'trackers' => array(
		'feature' => 'feature_trackers',
		'key' => 'trackerId',
		'itemkey' => 'itemId',
	),
	'mytiki' => array(
		'feature' => '',
		'key' => 'user',
		'itemkey' => '',
	),
	'user_messages' => array(
		'feature' => 'feature_messages',
		'key' => 'msgId',
		'itemkey' => '',
	),
	'newsreader' => array(
		'feature' => 'feature_newsreader',
		'key' => 'serverId',
		'itemkey' => 'id',
	),
	'webmail' => array(
		'feature' => 'feature_webmail',
		'key' => 'msgId',
		'itemkey' => '',
	),
	'contacts' => array(
		'feature' => 'feature_contacts',
		'key' => 'contactId',
		'itemkey' => '',
	),
	'faqs' => array(
		'feature' => 'feature_faqs',
		'key' => 'faqId',
		'itemkey' => '',
	),
	'quizzes' => array(
		'feature' => 'feature_quizzes',
		'key' => 'quizId',
		'itemkey' => '',
	),
	'poll' => array(
		'feature' => 'feature_polls',
		'key' => 'pollId',
		'itemkey' => '',
	),
	'surveys' => array(
		'feature' => 'feature_surveys',
		'key' => 'surveyId',
		'itemkey' => '',
	),
	'featured_links' => array(
		'feature' => 'feature_featuredLinks',
		'key' => 'url',
		'itemkey' => '',
	),
	'directory' => array(
		'feature' => 'feature_directory',
		'key' => 'directoryId',
		'itemkey' => '',
	),
	'calendar' => array(
		'feature' => 'feature_calendar',
		'key' => 'calendarId',
		'itemkey' => 'calitmId',
	),
	'workflow' => array(
		'feature' => 'feature_workflow',
		'key' => '',
		'itemkey' => '',
	),
	'charts' => array(
		'feature' => 'feature_charts',
		'key' => '',
		'itemkey' => '',
	),
	'maps' => array(
		'feature' => 'feature_maps',
		'key' => 'mapId',
		'itemkey' => '',
	),
	'gmaps' => array(
		'feature' => 'feature_gmap',
		'key' => '',
		'itemkey' => '',
	),
	'categories' => array(
		'feature' => 'feature_categories',
		'key' => 'categId',
		'itemkey' => '',
	),
	'games' => array(
		'feature' => 'feature_games',
		'key' => 'gameId',
		'itemkey' => '',
	),
	'html_pages' => array(
		'feature' => 'feature_html_pages',
		'key' => 'pageId',
		'itemkey' => '',
	),
	'swffix' => array(
		'feature' => 'feature_swffix',
	),
	'workflow' => array(
		'feature' => 'feature_workflow',
		'key' => '',
		'itemkey' => '',
	),
);

if ( ! isset($section) ) $section = '';
$sections_enabled = array();

foreach ( $sections as $sec => $dat ) {
	$feat = $dat['feature'];
	if ( $feat === '' or ( isset($prefs[$feat]) and $prefs[$feat] == 'y' ) ) {
		$sections_enabled[$sec] = $dat;
	}
}
ksort($sections_enabled);
if ( ! empty($section) ) $smarty->assign('section', $section);
