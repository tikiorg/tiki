<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @param $object_type
 * @param null $object_id
 * @param bool $process
 * @return bool
 */
function refresh_index($object_type, $object_id = null, $process = true)
{
	global $prefs;

	// First process unified search, then process the legacy indexing if required.

	if ( $prefs['feature_search'] == 'y' && $prefs['unified_incremental_update'] == 'y' && $object_id ) {
		$unified_type = refresh_index_convert_type($object_type);

		try {
			$unifiedsearchlib = TikiLib::lib('unifiedsearch');
			$unifiedsearchlib->invalidateObject($unified_type, $object_id);

			if ($process) {
				$unifiedsearchlib->processUpdateQueue();
			}

		} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {

			$errlib = TikiLib::lib('errorreport');
			$message = $e->getMessage();
			if (empty($message)) {
				$message = tra('Try rebuilding or optimizing the index on the search admin page');
			}
			$errlib->report(tr('Search index could not be updated: %0', $message));
		}
	}

	return true;
}

/**
 * @param $object_type
 * @return string
 */
function refresh_index_convert_type($object_type)
{
	switch ( $object_type ) {
		case 'articles': //case 'art': case 'article':
			return 'article';
	
		case 'blog_posts': //case 'blog': case 'blog_post':
			return 'blog post'; // FIXME : Unchecked
	
		case 'blogs':
			return 'blog'; // FIXME : Unchecked
	
		case 'directory_categories': //case 'dir_cat':
			return 'directory'; // FIXME : Unchecked
	
		case 'directory_sites': //case 'dir': case 'dir_site':
			return 'directory'; // FIXME : Unchecked
	
		case 'comments': //case 'wiki comment': case 'comment':
			return 'comment'; // FIXME : Unchecked
	
		case 'faq_questions':
			return 'faq'; // FIXME : Unchecked
	
		case 'faqs': //case 'faq':
			return 'faq'; // FIXME : Unchecked
	
		case 'file_galleries';
			return 'file gallery';
	
		case 'files': case 'file'; //case 'fgal':
			return 'file';
	
		case 'forums': //case 'forum':
			return 'forum post';
	
		case 'images': //case 'gal': case 'img':
			return 'image';
	
		case 'pages': //case 'wiki page': case 'wiki':
			return 'wiki page';
	
		case 'tracker_items': //case 'track': case 'trackeritem':
			return 'trackeritem';
	
		case 'trackers': //case 'tracker':
			return 'tracker'; // FIXME : Unchecked
	
		case 'galleries': // case 'imggal':
			return 'image gallery'; // FIXME : Unchecked
		
		default:
			return $object_type;
	}
}

/*
 *  Obsolete functions (you can call directly refresh_index() )
 */

function random_refresh_file()
{
	refresh_index('files', -1);
}
function random_refresh_filegal()
{
	refresh_index('file_galleries', -1);
}
function random_refresh_img()
{
	refresh_index('images', -1);
}
function random_refresh_imggals()
{
	refresh_index('galleries', -1);
}
function random_refresh_index_articles()
{
	refresh_index('articles', -1);
}
function random_refresh_index_blog_posts()
{
	refresh_index('blog_posts', -1);
}
function random_refresh_index_blogs()
{
	refresh_index('blogs', -1);
}

/**
 * @param int $times
 */
function random_refresh_index_comments( $times = 1 )
{
	for( $i = 1; $i <= $times; $i ++ )
		refresh_index('comments', -1);
}
function random_refresh_index_dir_cats()
{
	refresh_index('directory_categories', -1);
}
function random_refresh_index_dir_sites()
{
	refresh_index('directory_sites', -1);
}
function random_refresh_index_faq_questions()
{
	refresh_index('faq_questions', -1);
}
function random_refresh_index_faqs()
{
	refresh_index('faqs', -1);
}
function random_refresh_index_forum()
{
	refresh_index('forums', -1);
}
function random_refresh_index_tracker_items()
{
	refresh_index('tracker_items', -1);
}
function random_refresh_index_trackers()
{
	refresh_index('trackers', -1);
}
function random_refresh_index_wiki()
{
	refresh_index('pages', -1);
}

function refresh_index_articles()
{
	refresh_index('articles');
}
function refresh_index_blogs()
{
	refresh_index('blogs');
	refresh_index('blog_post');
}
function refresh_index_directories()
{
	refresh_index('directory_sites');
	refresh_index('directory_categories');
}
function refresh_index_faqs()
{
	refresh_index('faqs');
	refresh_index('faq_questions');
}
function refresh_index_files()
{
	refresh_index('files');
}
function refresh_index_forums()
{
	refresh_index('forums');
}
function refresh_index_galleries()
{
	refresh_index('galleries');
	refresh_index('images');
}
function refresh_index_trackers()
{
	refresh_index('tracker_items');
}
function refresh_index_wiki_all()
{
	refresh_index('pages');
}

/**
 * @param $threadId
 */
function refresh_index_comments($threadId)
{
	refresh_index('comments', $threadId);
}

/**
 * @param $page
 */
function refresh_index_forum($page)
{
	refresh_index('forums', $page);
}

/**
 * @param $page
 */
function refresh_index_wiki($page)
{
	refresh_index('pages', $page);
}
