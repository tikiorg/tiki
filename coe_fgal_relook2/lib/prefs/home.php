<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_home_list() {

	return array(
		'home_blog' => array(
			'name' => tra('Home Blog (main blog)'),
			'type' => 'list',
			'options' => listblog_pref(),
		),
		'home_forum' => array(
			'name' => tra('Home Forum (main forum)'),
			'type' => 'list',
			'options' => listforum_pref(),
		),
		'home_file_gallery' => array(
			'name' => tra('Home File Gallery (main file gallery)'),
			'type' => 'list',
			'options' => listfgal_pref(),
		),
		'home_gallery' => array(
			'name' => tra('Home Gallery (main gallery)'),
			'type' => 'list',
			'options' => listimgal_pref(),
		),
	);
}

/**
 * listimgal_pref: retrieve the list of image galleries for the home_gallery preference 
 * 
 * @access public
 * @return array: galleryId => name (truncated)
 */
function listimgal_pref()
{
	include_once ('lib/imagegals/imagegallib.php');
	global $imagegallib;

	$allimgals = $imagegallib->list_visible_galleries(0, -1, 'name_desc', 'admin', '');

	$listimgals = array();

	if ($allimgals['cant'] > 0) {
		foreach ($allimgals['data'] as $oneimgal) {
			$listimgals[ $oneimgal['galleryId'] ] = substr($oneimgal['name'], 0, 30);
		}
	} else {
		$listimgals[''] = tra('No image gallery available (create one first)');
	}

	return $listimgals;
}

/**
 * listfgal_pref: retrieve the list of file galleries for the home_file_gallery preference
 * 
 * @access public
 * @return array: galleryId => name(truncated)
 */
function listfgal_pref()
{
	$filegallib = TikiLib::lib('filegal');

	$allfgals = $filegallib->list_visible_file_galleries(0, -1, 'name_desc', 'admin', '');

	$listfgals = array();

	if ($allfgals['cant'] > 0) {
		foreach ($allfgals['data'] as $onefgal) {
			$listfgals[ $onefgal['galleryId'] ] = substr($onefgal['name'], 0, 30);
		}
	} else {
		$listfgals[''] = tra('No file gallery available (create one first)');
	}

	return $listfgals;
}

/**
 * listforum_pref: retrieve the list of forums for the home_forum preference
 *
 * @access public
 * @return array: forumId => name(truncated)
 */
function listforum_pref()
{
	include_once ('lib/comments/commentslib.php');
	$commentslib = new Comments();
	$allforums = $commentslib->list_forums(0, -1, 'name_desc', '');

	$listforums = array();

	if ($allforums['cant'] > 0) {
		foreach ($allforums['data'] as $oneforum) {
			$listforums[ $oneforum['forumId'] ] = substr($oneforum['name'], 0, 30);
		}
	} else {
		$listforums[''] = tra('No forum available (create one first)');
	}

	return $listforums;
}

/**
 * listblog_pref: retrieve the list of blogs for the home_blog preference
 *
 * @access public
 * @return array: blogId => title(truncated)
 */
function listblog_pref()
{
	global $bloglib; require_once('lib/blogs/bloglib.php');

	$allblogs = $bloglib->list_blogs(0, -1, 'created_desc', '');
	$listblogs = array();

	if ($allblogs['cant'] > 0) {
		foreach ($allblogs['data'] as $blog) {
			$listblogs[ $blog['blogId'] ] = substr($blog['title'], 0, 30);
		}
	} else {
		$listblogs[''] = tra('No blog available (create one first)');
	}

	return $listblogs;
}
