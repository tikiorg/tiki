<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function refresh_index($object_type, $object_id = null) {
	if ( empty($object_type) ) return false;
	global $tikilib;

	$wiki_html = '';
	$query_from = " from `tiki_$object_type`";
	$query_limit = -1;
	$query_offset = 0;
	$query_vars = array();
	$query_where = '';
	$query_fields = '';

	$cant_query = 'select count(*)'.$query_from;
	$cant_vars = null;

	//$filtering_expr = array('$content = strip_tags($content);');
	// strip tags seems to be bugged (maybe due to UTF-8 content ?)
	$mb = function_exists('mb_ereg_replace') ? 'mb_' : '';
	$filtering_expr = array('$content = '.$mb.'ereg_replace("<\s*/?\s*([a-zA-Z]+)[^>]*>", " ", $content);');

	switch ( $object_type ) {

	case 'articles': //case 'art': case 'article':
		$index_type = 'article';
		$f_id = 'articleId';
		$f_content = array('title', 'authorName', 'heading', 'body', 'author', 'topline', 'subtitle');
		break;

	case 'blog_posts': //case 'blog': case 'blog_post':
		$index_type = 'blog_post';
		$f_id = 'postId';
		$f_content = array('title', 'user', 'data');
		break;

	case 'blogs':
		$index_type = 'blog';
		$f_id = 'blogId';
		$f_content = array('title', 'user', 'description');
		break;

	case 'directory_categories': //case 'dir_cat':
		$index_type = 'dir_cat';
		$f_id = 'categId';
		$f_content = array('name', 'description');
		break;

	case 'directory_sites': //case 'dir': case 'dir_site':
		$index_type = 'dir_site';
		$f_id = 'siteId';
		$f_content = array('name', 'description');
		break;

	case 'comments': //case 'wiki comment': case 'comment': 
		$f_index_type = 'objectType';
		$filtering_expr[] = '$index_type .= "comment";';
		$f_id = 'threadId';
		$f_content = array('title', 'data', 'summary', 'objectType');
		break;

	case 'faq_questions':
		$index_type = 'faq_question';
		$f_id = 'questionId';
		$f_content = array('question', 'answer');
		break;

	case 'faqs': //case 'faq': 
		$index_type = 'faq';
		$f_id = 'faqId';
		$f_content = array('title', 'description');
		break;

	case 'file_galleries';
		$index_type = 'filegal';
		$f_id = 'galleryId';
		$f_content = array('name', 'description');
		break;

	case 'files': //case 'fgal': case 'file': 
		$index_type = 'file';
		$f_id = 'fileId';
		$f_content = array('data', 'description', 'name', 'search_data', 'filename', 'comment');
		$f_other = array('archiveId', 'filetype');
		$query_where = ' where archiveId = ?';
		$query_vars = array(0);
		$fulltext_mimetypes_pattern = '/^text\//i'; // Mimetypes that will be fulltext indexed
		unset($filtering_expr);
		break;

	case 'forums': //case 'forum':
		$index_type = 'forum';
		$f_id = 'forumId';
		$f_content = array('name', 'description', 'moderator');
		break;

	case 'images': //case 'gal': case 'img': 
		$index_type = 'img';
		$f_id = 'imageId';
		$f_content = array('name', 'description');
		break;

	case 'pages': //case 'wiki page': case 'wiki': 
		$index_type = 'wiki';
		$f_id = 'pageName';
		$f_content = array('data', 'description', 'pageName');
		array_unshift($filtering_expr, '$content = $tikilib->parse_data($content, array("is_html" => $res["is_html"]));');
		$wiki_html = ', `is_html`';
		break;

	case 'tracker_items': //case 'track': case 'trackeritem': 
		$cant_query = 'select count(*) from `tiki_tracker_item_fields` f, `tiki_tracker_fields` tf where tf.`type` in (?,?) and tf.`fieldId`=f.`fieldId`';
		$cant_vars = array('t','a');
		$index_type = 'trackeritem';
		$query_from = ' from `tiki_tracker_item_fields` f, `tiki_tracker_fields` tf';
		$query_where = ' where tf.`type` in (?,?) and tf.`fieldId`=f.`fieldId`';
		$query_vars = array('t','a');
		$f_id = array('id1' => 'f.`itemId`', 'id2' => 'f.`fieldId`');
		$f_content = array('content' => 'f.`value`');
		break;

	case 'trackers': //case 'tracker':
		$index_type = 'tracker';
		$f_id = 'trackerId';
		$f_content = array('name', 'description');
		break;

	case 'galleries': // case 'imggal':
		$index_type = 'imggal';
		$f_id = 'galleryId';
		$f_content = array('name', 'description');
		break;
	}

	if ( $object_id == -1 ) {
		// Random indexation...
		$query_limit = 1;
		$cant = $tikilib->getOne($cant_query, $cant_vars);
		if ( $cant > 0 ) $query_offset = rand(0, $cant - 1); else return true;
	} elseif ( ( is_integer($object_id) && $object_id != 0 ) || is_string($object_id) ) {
		// Index one object identified by its id
		$query_vars[] = $object_id;
		$v = is_array($f_id) ? $f_id['id1'] : $f_id;
		$query_where .= (($query_where == '') ? ' where ' : ' and ' );
		$query_where .= (strstr($v, '`')? $v: "`$v`");
		$query_where .= ' = ?';
	}

	if ( !empty($f_id) && !empty($f_content) ) {

		if ( !is_array($f_id) ) {
			$f_id = array($f_id);
		}
		if ( !is_array($f_content) ) {
			$f_content = array($f_content);
		}
		foreach ( $f_id as $k_id => $v_id ) {
			$query_fields .= (($query_fields!='')?', ':'');
			$query_fields .= (strstr($v_id, '`')? $v_id: "`$v_id`");
			$query_fields .= (is_string($k_id)?' as '.$k_id:'');
		}
		foreach ( $f_content as $k_content => $v_content ) {
			$query_fields .= ', ';
			$query_fields .= (strstr($v_content, '`') ?$v_content: "`$v_content`");
			$query_fields .= (is_string($k_content)?' as '.$k_content:'');
		}
		if ( !empty($f_other) ) {
			$query_fields .= ', `'.( is_array($f_other) ? implode('`, `', $f_other) : $f_other ) . '`';
		}

		$result = $tikilib->query('select '.$query_fields.$wiki_html.$query_from.$query_where, $query_vars, $query_limit, $query_offset);

		if ( $result ) while ( $res = $result->fetchRow() ) if ( is_array($res) ) {
			$id = '';
			$content = '';

			// For performance reasons, do not index all files with fulltext (depending on their mimetypes)
			if ( isset($index_type) && $index_type == 'file' && ! preg_match($fulltext_mimetypes_pattern, $res['filetype']) ) $res['data'] = '';

			foreach ( $f_id as $k_id => $v_id ) $id .= (($id!='')?'#':'').$res[(is_string($k_id)?$k_id:$v_id)];
			foreach ( $f_content as $k_content => $v_content ) $content .= ' '.$res[(is_string($k_content)?$k_content:$v_content)];
			if ( isset($f_index_type) && $f_index_type != '' ) $index_type = $res[$f_index_type];
			if ( isset($filtering_expr) && is_array($filtering_expr) ) foreach ( $filtering_expr as $expr ) eval($expr);

			if ( $content != '' && $index_type != '' && $id != '' ) {
				insert_index(search_index($content), $index_type, $id);
			}
		}
	}
	return true;
}

function refresh_index_oldest() {
	global $tikilib;
	$result = $tikilib->query('select `location`,`page`,`last_update` from `tiki_searchindex` order by `last_update` asc', null, 1);
	$res = $result->fetchRow();
	switch ( $res['location'] ) {
		case 'wiki': $res['location'] = 'pages'; break;
		case 'forum': $res['location'] = 'forums'; break;
	}
	refresh_index($res['location'], $res['page']);
}

function &search_index($data) {

	$preg_utf8_support=@preg_match('/\p{Lu}/u', "A" );

	// Be sure we will parse UTF-8 data
	if ( function_exists('mb_check_encoding')
		&& function_exists('iconv')
		&& function_exists('mb_detect_encoding')
		&& mb_check_encoding($data, 'UTF-8')
	) {
		$data = iconv(mb_detect_encoding($data), 'UTF-8//TRANSLIT', $data);
	}

	// Clean the UTF-8 string using HTML Purifier
@	include_once('lib/htmlpurifier/HTMLPurifier.auto.php');
@	include_once('lib/htmlpurifier/HTMLPurifier/Encoder.php');
	if ( class_exists('HTMLPurifier_Encoder') ) {
		$data = HTMLPurifier_Encoder::cleanUTF8($data);
	}

	// Remove remaining HTML numeric entities
	if ( function_exists('mb_decode_numericentity') ) {
		if ( ! function_exists('utf8_entity_decode') ) {
			function utf8_entity_decode($entity){
				$convmap = array(0x0, 0x10000, 0, 0xfffff);
				return mb_decode_numericentity($entity, $convmap, 'UTF-8');
			}
		}
		$data = preg_replace('/&#\d{2,5};/ue', "utf8_entity_decode('$0')", $data);
		$data = preg_replace('/&#x([a-fA-F0-7]{2,8});/ue', "utf8_entity_decode('&#'.hexdec('$1').';')", $data );
	}

	// Lowerize
	$data = function_exists('mb_convert_case') ? mb_convert_case($data, MB_CASE_LOWER, 'UTF-8') : strtolower($data);

	// Convert punctuations to spaces
	if ($preg_utf8_support) {
		$data = preg_replace('/[\pP\pZ\pS]/u', ' ', $data);
	} else {
		$data = preg_replace('/[\s\.,!\?\(\)\[\]\{\}\/\\\]/', ' ', $data);
	}

	if ( $data != '' ) {
		// Split into words (do NOT use the split function that doesn't correctly handle some characters !)
		$sstrings = preg_split('/\s+/u', $data, -1, PREG_SPLIT_NO_EMPTY);

		foreach ( $sstrings as $value ) {
			// Keep only alpha-num words
			if ( preg_match('/^[\pL\pN]+$/u', $value) || !$preg_utf8_support ) {
				if ( isset($words[$value]) ) {
					$words[$value]++; // count words
				} else {
					$words[$value] = 1;
				}
			}
		}
	}

	return $words;
}

function insert_index(&$words, $location, $page) {
	global $tikilib, $prefs;
	$query = 'delete from `tiki_searchindex` where `location`=? and `page`=?';
	$tikilib->query($query, array($location,$page), -1, -1, false);

	foreach ( $words as $key => $value ) {
		if ( strlen($key) >= $prefs['search_min_wordlength'] ) {
			$query = 'insert into `tiki_searchindex` (`location`,`page`,`searchword`,`count`,`last_update`) values(?,?,?,?,?)';
			$tikilib->query($query, array($location,$page,$key,(int)$value,$tikilib->now), -1, -1, false);
		}
	}
}

/*
 *  Obsolete functions (you can call directly refresh_index() )
 */

function random_refresh_file() { refresh_index('files', -1); }
function random_refresh_filegal() { refresh_index('file_galleries', -1); }
function random_refresh_img() { refresh_index('images', -1); }
function random_refresh_imggals() { refresh_index('galleries', -1); }
function random_refresh_index_articles() { refresh_index('articles', -1); }
function random_refresh_index_blog_posts() { refresh_index('blog_posts', -1); }
function random_refresh_index_blogs() { refresh_index('blogs', -1); }
function random_refresh_index_comments( $times = 1 ) { for( $i = 1; $i <= $times; $i ++ ) refresh_index('comments', -1); }
function random_refresh_index_dir_cats() { refresh_index('directory_categories', -1); }
function random_refresh_index_dir_sites() { refresh_index('directory_sites', -1); }
function random_refresh_index_faq_questions() { refresh_index('faq_questions', -1); }
function random_refresh_index_faqs() { refresh_index('faqs', -1); }
function random_refresh_index_forum() { refresh_index('forums', -1); }
function random_refresh_index_tracker_items() { refresh_index('tracker_items', -1); }
function random_refresh_index_trackers() { refresh_index('trackers', -1); }
function random_refresh_index_wiki() { refresh_index('pages', -1); }

function refresh_index_articles() { refresh_index('articles'); }
function refresh_index_blogs() { refresh_index('blogs'); refresh_index('blog_post'); }
function refresh_index_directories() { refresh_index('directory_sites'); refresh_index('directory_categories'); }
function refresh_index_faqs() { refresh_index('faqs'); refresh_index('faq_questions'); }
function refresh_index_files() { refresh_index('files'); }
function refresh_index_forums() { refresh_index('forums'); }
function refresh_index_galleries() { refresh_index('galleries'); refresh_index('images'); }
function refresh_index_trackers() { refresh_index('tracker_items'); }
function refresh_index_wiki_all() { refresh_index('pages'); }

function refresh_index_comments($threadId) { refresh_index('comments', $threadId); }
function refresh_index_forum($page) { refresh_index('forums', $page); }
function refresh_index_wiki($page) { refresh_index('pages', $page); }
