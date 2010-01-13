<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$sections = array(
	// tra('Wiki Page') -- tra() comments are there for get_strings.php
	'wiki page' => array(
		'feature' => 'feature_wiki',
		'key' => 'page',
		'itemkey' => '',
		'objectType' =>'wiki page',
		'commentsFeature' => 'feature_wiki_comments',
	),
	// tra('Blog')
	// tra('Blog Post')
	'blogs' => array(
		'feature' => 'feature_blogs',
		'key' => 'blogId',
		'itemkey' => 'postId',
		'objectType' =>'blog post',
		'itemObjectType' => 'blog post',
		'commentsFeature' => 'feature_blog_comments',
		'itemCommentsFeature' => 'feature_blogposts_comments'
	),
	// tra('File Gallery')
	// tra('File')
	'file_galleries' => array(
		'feature' => 'feature_file_galleries',
		'key' => 'galleryId',
		'itemkey' => 'fileId',
		'objectType' =>'file gallery',
		'itemObjectType' => 'file',
		'commentsFeature' => 'feature_file_galleries_comments',
	),
	// tra('Image Gallery')
	// tra('Image')
	'galleries' => array(
		'feature' => 'feature_galleries',
		'key' => 'galleryId',
		'itemkey' => 'imageId',
		'objectType' =>'image gallery',
		'itemObjectType' => 'image',
		'commentsFeature' => 'feature_image_galleries_comments',
	),
	// tra('Forum')
	// tra('Forum Post')
	'forums' => array(
		'feature' => 'feature_forums',
		'key' => 'forumId',
		'itemkey' => 'postId',
		'objectType' =>'forum',
		'itemObjectType' => 'forum post',
	),
	// tra('Article')
	'cms' => array(
		'feature' => 'feature_articles',
		'key' => 'articleId',
		'itemkey' => '',
		'objectType' => 'article',
		'commentsFeature' => 'feature_article_comments'
	),
	// tra('Tracker')
	'trackers' => array(
		'feature' => 'feature_trackers',
		'key' => 'trackerId',
		'itemkey' => 'itemId',
		'objectType' =>'tracker',
		'itemObjectType' => 'tracker %d',
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
	// tra('Faq')
	'faqs' => array(
		'feature' => 'feature_faqs',
		'key' => 'faqId',
		'itemkey' => '',
		'objectType' => 'faq',
		'commentsFeature' => 'feature_faq_comments',
	),
	// tra('Quizz')
	'quizzes' => array(
		'feature' => 'feature_quizzes',
		'key' => 'quizId',
		'itemkey' => '',
		'objectType' => 'quiz',
	),
	// tra('Poll')
	'poll' => array(
		'feature' => 'feature_polls',
		'key' => 'pollId',
		'itemkey' => '',
		'objectType' => 'poll',
		'commentsFeature' => 'feature_poll_comments',
	),
	// tra('Survey')
	'surveys' => array(
		'feature' => 'feature_surveys',
		'key' => 'surveyId',
		'itemkey' => '',
		'objectType' => 'survey',
	),
	'featured_links' => array(
		'feature' => 'feature_featuredLinks',
		'key' => 'url',
		'itemkey' => '',
	),
	// tra('Directory')
	'directory' => array(
		'feature' => 'feature_directory',
		'key' => 'directoryId',
		'itemkey' => '',
		'objectType' => 'directory',
	),
	// tra('Calendar')
	'calendar' => array(
		'feature' => 'feature_calendar',
		'key' => 'calendarId',
		'itemkey' => 'calitmId',
		'objectType' => 'calendar',
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
	// tra('Map')
	'maps' => array(
		'feature' => 'feature_maps',
		'key' => 'mapId',
		'itemkey' => '',
		'objectType' => 'map',
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
	// tra('Html Page')
	'html_pages' => array(
		'feature' => 'feature_html_pages',
		'key' => 'pageId',
		'itemkey' => '',
		'objectType' => 'html page',
	),
	'swfobj' => array(
		'feature' => 'feature_swfobj',
	),
	// tra('Newsletter')
	'newsletters' => array(
		'feature' => 'feature_newsletters',
		'key' => 'nlId',
		'objectType' => 'newsletter',
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
$smarty->assign_by_ref('sections_enabled', $sections_enabled);
if ( ! empty($section) ) $smarty->assign('section', $section);
if ( ! empty($section_class) ) {
	$smarty->assign('section_class', $section_class);
}elseif ( ! empty($section) ) {
	$smarty->assign('section_class', str_replace(' ','_',$section));
}
