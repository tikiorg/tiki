<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_sefurl($source, $type='wiki', $with_next = '', $all_langs='', $with_title='y', $title='' )
{
	global $prefs;
	$wikilib = TikiLib::lib('wiki');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');

	$sefurl = $prefs['feature_sefurl'] == 'y';

	switch ($type) {
		case 'wiki page':
		case 'wikipage':
			$type = 'wiki';
			break;
		case 'blog post':
			$type = 'blogpost';
			break;
	}
	switch ($type) {
		case 'wiki':
			return TikiLib::tikiUrlOpt($wikilib->sefurl($source, $with_next, $all_langs));

		case 'blog':
			$href = $sefurl ? "blog$source" : "tiki-view_blog.php?blogId=$source";
			break;

		case 'blogpost':
			$href = $sefurl ? "blogpost$source" : "tiki-view_blog_post.php?postId=$source";
			break;
		case 'calendar':
			$href = $sefurl ? "cal$source" : "tiki-calendar.php?calIds[]=$source";
			break;

		case 'gallery':
			$href = 'tiki-browse_gallery.php?galleryId='. $source;
			break;

		case 'article':
			$href = $sefurl ? "article$source" : "tiki-read_article.php?articleId=$source";
			break;

		case 'topic':
			$href = "tiki-view_articles.php?topic=$source";
			break;

		case 'file':
		case 'thumbnail':
		case 'display':
		case 'preview':
			$attributelib = TikiLib::lib('attribute');
			$attributes = $attributelib->get_attributes('file', $source);

			if ($type == 'file') {
				$prefix = 'dl';
				$suffix = null;
			} else {
				$prefix = $type;
				$suffix = '&amp;' . $type;
			}

			if (isset($attributes['tiki.content.url'])) {
				$href = $attributes['tiki.content.url'];
			} else {
				$href = $sefurl ? "$prefix$source" : "tiki-download_file.php?fileId=$source$suffix";
			}

			break;

		case 'draft':
			$href = 'tiki-download_file.php?fileId='. $source.'&amp;draft';
			break;

		case 'tracker item':
			$type = 'trackeritem';

		case 'trackeritem':
			$replacementpage = '';
			if ($prefs["feature_sefurl_tracker_prefixalias"] == 'y' && $prefs['tracker_prefixalias_on_links'] == 'y') {
				$trklib = TikiLib::lib('trk');
				$replacementpage = $trklib->get_trackeritem_pagealias($source);
			}
			if ($replacementpage) {
				return TikiLib::tikiUrlOpt($wikilib->sefurl($replacementpage, $with_next, $all_langs));
			} else {
				$href = 'tiki-view_tracker_item.php?itemId='. $source;
			}
			break;

		case 'tracker':
			if ($source) {
				$href = 'tiki-view_tracker.php?trackerId=' . $source;
			} else {
				$href = 'tiki-list_trackers.php';
			}
			break;

		case 'filegallery':
		case 'file gallery':
			$type = 'file gallery';
			$href = 'tiki-list_file_gallery.php?galleryId='.$source;
			break;

		case 'forum':
			$href = $sefurl ? "forum$source" : 'tiki-view_forum.php?forumId='.$source;
			break;

		case 'forumthread':
		case 'forum post':	// used in unified search getSupportedTypes()
			$href = $sefurl ? "forumthread$source" : 'tiki-view_forum_thread.php?comments_parentId='.$source;
			break;

		case 'image':
			$href = 'tiki-browse_image.php?imageId='.$source;
			break;

		case 'sheet':
			$href = $sefurl ? "sheet$source" : "tiki-view_sheets.php?sheetId=$source";
			break;

		case 'category':
			$href = $sefurl ? "cat$source": "tiki-browse_categories.php?parentId=$source";
			break;

		case 'freetag':
			$href = "tiki-browse_freetags.php?tag=" . urlencode($source);
			break;

		case 'newsletter':
			$href = "tiki-newsletters.php?nlId=" . urlencode($source);
			break;

		case 'survey':
			$href = "tiki-take_survey.php?surveyId=" . urlencode($source);
			break;

		default:
			$href = $source;
			break;
	}

	if ($with_next && ($with_title != 'y' || $prefs['feature_sefurl'] !== 'y')) {
		$href .= '&amp;';
	}

	if ($prefs['feature_sefurl'] == 'y' && $smarty) {
		include_once('tiki-sefurl.php');
		return TikiLib::tikiUrlOpt(filter_out_sefurl($href, $type, $title, $with_next, $with_title));
	} else {
		return TikiLib::tikiUrlOpt($href);
	}
}
