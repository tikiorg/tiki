<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_user_bookmarks_info() {
	return array(
		'name' => tra('My Bookmarks'),
		'description' => tra('Lightweight interface to user bookmarks, enabling to view them concisely, do some manipulations and bookmark the page being viewed'),
		'prefs' => array("feature_user_bookmarks"),
		'params' => array()
	);
}

function module_user_bookmarks( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	
	global $bookmarklib, $imagegallib, $user, $prefs, $tiki_p_create_bookmarks;
	include_once ('lib/bookmarks/bookmarklib.php');
	
	$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);
	
	if (isset($setup_parsed_uri["query"])) {
		TikiLib::parse_str($setup_parsed_uri["query"], $setup_query_data);
	} else {
		$setup_query_data = array();
	}
	
	if ($user && $tiki_p_create_bookmarks == 'y') {
		// check the session to get the directory or create directory =0
		$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
	
		if (isset($_REQUEST["bookmarks_directory"])) {
			$_SESSION["bookmarks_directory"] = $_REQUEST["bookmarks_directory"];
		} elseif (!isset($_SESSION["bookmarks_directory"])) {
			$_SESSION["bookmarks_directory"] = 0;
		}
	
		$ownurl = $tikilib->httpPrefix(). $_SERVER["REQUEST_URI"];
	
		// Now build urls
		if (strstr($ownurl, '?')) {
			$modb_sep = '&amp;';
		} else {
			$modb_sep = '?';
		}
	
		$smarty->assign('modb_sep', $modb_sep);
	
		if (isset($_REQUEST["bookmark_removeurl"])) {
			$bookmarklib->remove_url($_REQUEST["bookmark_removeurl"], $user);
		}
	
		if (isset($_REQUEST["bookmark_create_folder"])) {
			$bookmarklib->add_folder($_SESSION["bookmarks_directory"], $_REQUEST['modb_name'], $user);
		}
	
		if (isset($_REQUEST["bookmark_mark"])) {
			$name = $_REQUEST["modb_name"];
			if (empty($name)) {
				// Check if we are bookmarking a wiki-page	
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-index')) {
					// Get the page
					if (isset($setup_query_data["page"])) {
						$name = $setup_query_data["page"];
					} else {
						$name = $prefs['wikiHomePage'];
					}
				}
	
				// Check if we are bookmarking an article
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-read_article')) {
					global $artlib; require_once 'lib/articles/artlib.php';
					$info = $artlib->get_article($setup_query_data["articleId"]);
	
					$name = $info["title"];
				}
	
				// Check if we are bookmarking a file gallery
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-list_file_gallery')) {
					$filegallib = TikiLib::lib('filegal');
					$info = $filegallib->get_file_gallery($setup_query_data["galleryId"]);
	
					$name = $info["name"];
				}
	
				// Check if we are bookmarking an image gallery
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-browse_gallery') || strstr($_SERVER["REQUEST_URI"], 'tiki-list_gallery')) {
					include_once ("lib/imagegals/imagegallib.php");
					$info = $imagegallib->get_gallery($setup_query_data["galleryId"]);
	
					$name = $info["name"];
				}

				// Check if we are bookmarking an image
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-browse_image')) {
					include_once ("lib/imagegals/imagegallib.php");
					$info = $imagegallib->get_image($setup_query_data["imageId"]);
	
					$name = $info["name"];
				}
	
				// Check if we are bookmarking a forum
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-view_forum')) {
					require_once('lib/comments/commentslib.php'); global $commentslib;
					if (!isset($commentslib)) {
						$commentslib = new Comments($dbTiki);
					}
					$info = $commentslib->get_forum($setup_query_data["forumId"]);
	
					$name = $info["name"];
				}
	
				// Check if we are bookmarking a faq
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-view_faq')) {
					$info = $tikilib->get_faq($setup_query_data["faqId"]);
	
					$name = $info["title"];
				}
	
				// Check if we are bookmarking a weblog
				if (strstr($_SERVER["REQUEST_URI"], 'tiki-view_blog')) {
					global $bloglib; require_once('lib/blogs/bloglib.php');
					$info = $bloglib->get_blog($setup_query_data["blogId"]);
	
					$name = $info["title"];
				}
			}
	
			if (!empty($name)) {
				$bookmarklib->replace_url(0, $_SESSION["bookmarks_directory"], $name, $ownurl, $user);
			}
		}
	
		$modb_p_info = $bookmarklib->get_folder($_SESSION["bookmarks_directory"], $user);
		$modb_father = $modb_p_info["parentId"];
		// get urls
		$modb_urls = $bookmarklib->list_folder($_SESSION["bookmarks_directory"], 0, -1, 'name_asc', '', $user);
		$smarty->assign('modb_urls', $modb_urls["data"]);
		// get folders
		$modb_folders = $bookmarklib->get_child_folders($_SESSION["bookmarks_directory"], $user);
		$modb_pf = array(
			"name" => "..",
			"folderId" => $modb_father,
			"parentId" => 0,
			"user" => $user
		);
	
		if ($_SESSION["bookmarks_directory"]) {
			array_unshift($modb_folders, $modb_pf);
		}
	
		$smarty->assign('modb_folders', $modb_folders);
		$smarty->clear_assign('tpl_module_title');
	}
}
