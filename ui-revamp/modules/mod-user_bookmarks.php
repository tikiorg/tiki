<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once ('lib/tikilib.php'); # httpScheme()

global $bookmarklib, $imagegallib, $user, $prefs, $tiki_p_create_bookmarks;
include_once ('lib/bookmarks/bookmarklib.php');
include_once ("lib/imagegals/imagegallib.php");

$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);

if (isset($setup_parsed_uri["query"])) {
	parse_str($setup_parsed_uri["query"], $setup_query_data);
} else {
	$setup_query_data = array();
}

if ($prefs['feature_user_bookmarks'] != 'y') {
        $smarty->assign('module_error', tra("This feature is disabled").": feature_user_bookmarks");
} elseif ($user && $tiki_p_create_bookmarks == 'y') {
	// check the session to get the parent or create parent =0
	$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);

	if (!isset($_SESSION["bookmarks_parent"])) {
		$_SESSION["bookmarks_parent"] = 0;
	}

	if (isset($_REQUEST["bookmarks_parent"])) {
		$_SESSION["bookmarks_parent"] = $_REQUEST["bookmarks_parent"];
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
		$bookmarklib->add_folder($_SESSION["bookmarks_parent"], $_REQUEST['bookmark_urlname'], $user);
	}

	if (isset($_REQUEST["bookmark_mark"])) {
		if (empty($_REQUEST["bookmark_urlname"])) {
			// Check if we are bookmarking a wiki-page	
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-index')) {
				// Get the page
				if (isset($setup_query_data["page"])) {
					$_REQUEST["bookmark_urlname"] = $setup_query_data["page"];
				} else {
					$_REQUEST["bookmark_urlname"] = $prefs['wikiHomePage'];
				}
			}

			// Check if we are bookmarking an article
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-read_article')) {
				$info = $tikilib->get_article($setup_query_data["articleId"]);

				$_REQUEST["bookmark_urlname"] = $info["title"];
			}

			// Check if we are bookmarking an image gallery
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-browse_gallery') || strstr($_SERVER["REQUEST_URI"], 'tiki-list_gallery')) {
				$info = $imagegallib->get_gallery($setup_query_data["galleryId"]);

				$_REQUEST["bookmark_urlname"] = $info["name"];
			}

			// Check if we are bookmarking a file gallery
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-list_file_gallery')) {
				$info = $tikilib->get_file_gallery($setup_query_data["galleryId"]);

				$_REQUEST["bookmark_urlname"] = $info["name"];
			}

			// Check if we are bookmarking an image
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-browse_image')) {
				$info = $imagegallib->get_image($setup_query_data["imageId"]);

				$_REQUEST["bookmark_urlname"] = $info["name"];
			}

			// Check if we are bookmarking a forum
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-view_forum')) {
				require_once('lib/commentslib.php');
				if (!isset($commentslib)) {
					$commentslib = new Comments($dbTiki);
				}
				$info = $commentslib->get_forum($setup_query_data["forumId"]);

				$_REQUEST["bookmark_urlname"] = $info["name"];
			}

			// Check if we are bookmarking a faq
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-view_faq')) {
				$info = $tikilib->get_faq($setup_query_data["faqId"]);

				$_REQUEST["bookmark_urlname"] = $info["title"];
			}

			// Check if we are bookmarking a weblog
			if (strstr($_SERVER["REQUEST_URI"], 'tiki-view_blog')) {
				$info = $tikilib->get_blog($setup_query_data["blogId"]);

				$_REQUEST["bookmark_urlname"] = $info["title"];
			}
		}

		if (!empty($_REQUEST["bookmark_urlname"])) {
			$bookmarklib->replace_url(0, $_SESSION["bookmarks_parent"], $_REQUEST["bookmark_urlname"], $ownurl, $user);
		}
	}

	$modb_p_info = $bookmarklib->get_folder($_SESSION["bookmarks_parent"], $user);
	$modb_father = $modb_p_info["parentId"];
	// get folders for the parent
	$modb_urls = $bookmarklib->list_folder($_SESSION["bookmarks_parent"], 0, -1, 'name_asc', '', $user);
	$smarty->assign('modb_urls', $modb_urls["data"]);
	$modb_folders = $bookmarklib->get_child_folders($_SESSION["bookmarks_parent"], $user);
	$modb_pf = array(
		"name" => "..",
		"folderId" => $modb_father,
		"parentId" => 0,
		"user" => $user
	);

	$modb_pfs = array($modb_pf);

	if ($_SESSION["bookmarks_parent"]) {
		$modb_folders = array_merge($modb_pfs, $modb_folders);
	}

	$smarty->assign('modb_folders', $modb_folders);
// get urls for the parent
}

?>
