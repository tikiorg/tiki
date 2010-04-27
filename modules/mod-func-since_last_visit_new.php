<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function module_since_last_visit_new_info() {
	return array(
		'name' => tra('Since last visit'),
		'description' => tra('Displays to logged in users new or updated objects since a point in time, by default their last login date and time.'),
		'params' => array(
			'showuser' => array(
				'name' => tra('Show users'),
				'description' => tra('If set to "n", do not show new users.') . ' ' . tra('Default:') . ' "y"'
			),
			'showtracker' => array(
				'name' => tra('Show trackers'),
				'description' => tra('If set to "n", do not show tracker changes.') . ' ' . tra('Default:') . ' "y"'
			),
			'calendar_focus' => array(
				'name' => tra('Calendar focus'),
				'description' => tra('Unless set to "ignore", the module changes the reference point in time from the user\'s last login date and time to a day where users browse to using the calendar.')
			),
		),
		'common_params' => array( 'nonums', 'rows' ),
	);
}

function module_since_last_visit_new($mod_reference, $params = null) {
	global $smarty, $user;
	include_once('tiki-sefurl.php');
	if (!$user) return false;

	$resultCount = $mod_reference['rows'];

	global $tikilib, $userlib, $prefs;
	$ret = array();
	if ( $params == null ) $params = array();

	if ((empty($params['calendar_focus']) || $params['calendar_focus'] != 'ignore') && strpos($_SERVER["SCRIPT_NAME"],"tiki-calendar.php") && isset($_REQUEST["todate"]) && $_REQUEST["todate"]) {
		$last = $_REQUEST["todate"];
		$_SESSION["slvn_last_login"] = $last;
		$smarty->assign('tpl_module_title', tra('Changes since'));
	} else if (isset($_SESSION["slvn_last_login"])) {
		$last = $_SESSION["slvn_last_login"];
		$smarty->assign('tpl_module_title', tra('Changes since'));
	} else {
		$details = $userlib->get_user_details($user);
		$last = $details['info']['lastLogin'];
		if (!$last) $last = time();
		$smarty->assign('tpl_module_title', tra('Since your last visit...'));
	}
	$ret["lastLogin"] = $last;

	$ret["items"]["comments"]["label"] = tra('new comments');
	$ret["items"]["comments"]["cname"] = "slvn_comments_menu";
	$query = "select `object`,`objectType`,`title`,`commentDate`,`userName`,`threadId`, `parentId` from `tiki_comments` where `commentDate`>? and `objectType` != 'forum' order by `commentDate` desc";
	$result = $tikilib->query($query, array((int)$last), $resultCount);

	$count = 0;
	while ($res = $result->fetchRow())
	{
		switch($res["objectType"]) {
		case "article":
			$perm = 'tiki_p_read_article';
			$ret["items"]["comments"]["list"][$count]["href"]
				= filter_out_sefurl('tiki-read_article.php?articleId=' . $res['object'], $smarty, 'article', $res['title']);
			break;
		case "post":
			$perm = 'tiki_p_read_blog';
			$ret["items"]["comments"]["list"][$count]["href"]
				= filter_out_sefurl('tiki-view_blog_post.php?postId=' . $res['object'], $smarty, 'blogpost', $res['title']);
			break;
		case "blog":
			$perm = 'tiki_p_read_blog';
			$ret["items"]["comments"]["list"][$count]["href"]
				= filter_out_sefurl('tiki-view_blog.php?blogId=' . $res['object'], $smarty, 'blog', $res['title']);
			break;
		case "faq":
			$perm = 'tiki_p_view_faqs';
			$ret["items"]["comments"]["list"][$count]["href"]
				= "tiki-view_faq.php?faqId=" . $res["object"];
			break;
		case "file gallery":
			$perm = 'tiki_p_view_file_gallery';
			$ret["items"]["comments"]["list"][$count]["href"]
				= "tiki-list_file_gallery.php?galleryId=" . $res["object"];
			break;
		case "image gallery":
			$perm = 'tiki_p_view_image_gallery';
			$ret["items"]["comments"]["list"][$count]["href"]
				= "tiki-browse_gallery.php?galleryId=" . $res["object"];
			break;
		case "poll":
			// no perm check for viewing polls, only a perm for taking them
			$ret["items"]["comments"]["list"][$count]["href"]
				= "tiki-poll_results.php?pollId=" . $res["object"];
			break;
		case "wiki page":
			$perm = 'tiki_p_view';
			$ret["items"]["comments"]["list"][$count]["href"]
				= "tiki-index.php?page=" . urlencode($res["object"]);
			break;
		default:
			$perm = 'tiki_p_read_comments';
			break;
		}

		if (!isset($perm) || $userlib->user_has_perm_on_object($user, $res['object'], $res['objectType'], $perm)) {
			require_once('lib/smarty_tiki/modifier.username.php');
			if (isset($ret["items"]["comments"]["list"][$count]["href"])) {
				$ret["items"]["comments"]["list"][$count]["href"] .= '&comzone=show#threadId'.$res['threadId'];
			}
			$ret["items"]["comments"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["commentDate"]) ." ". tra("by") ." ". smarty_modifier_username($res["userName"]);
			$ret["items"]["comments"]["list"][$count]["label"] = $res["title"]; 
			$count++;
		}
	}
	$ret["items"]["comments"]["count"] = $count;


	/////////////////////////////////////////////////////////////////////////
	// FORUMS
	if ($prefs['feature_forums'] == 'y') {
		$ret["items"]["posts"]["label"] = tra('new posts');
		$ret["items"]["posts"]["cname"] = "slvn_posts_menu";
		$query = "select `posts`.`object`,`posts`.`objectType`,`posts`.`title`,`posts`.`commentDate`,`posts`.`userName`,`posts`.`threadId`, `posts`.`parentId`,`topics`.`title` `topic_title` from `tiki_comments` `posts` left join `tiki_comments` `topics` ON `posts`.`parentId` = `topics`.`threadId` where `posts`.`commentDate`>? and `posts`.`objectType` = 'forum' order by `posts`.`commentDate` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['object'], $res['objectType'], 'tiki_p_forum_read')) {
				$ret["items"]["posts"]["list"][$count]["href"]
					= "tiki-view_forum_thread.php?forumId=" . $res["object"] . "&comments_parentId=";
				if ($res["parentId"]) {
					$ret["items"]["posts"]["list"][$count]["href"].=$res["parentId"].'#threadId'.$res['threadId'];
				} else {
					$ret["items"]["posts"]["list"][$count]["href"].=$res["threadId"];
				}
				$ret["items"]["posts"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["commentDate"]) ." ". tra("by") ." ". $res["userName"];
				if ($res["parentId"] == 0 || $prefs['forum_reply_notitle'] != 'y') {
					$ret["items"]["posts"]["list"][$count]["label"] = $res["title"]; 
				} else {
					$ret["items"]["posts"]["list"][$count]["label"] = $res["topic_title"]; 
				}
				++$count;	
			}
		}
		$ret["items"]["posts"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// WIKI PAGES
	if ($prefs['feature_wiki'] == 'y') {  
		$ret["items"]["pages"]["label"] = tra('wiki pages changed');
		$ret["items"]["pages"]["cname"] = "slvn_pages_menu";
		$query = "select `pageName`, `user`, `lastModif`  from `tiki_pages` where `lastModif`>? order by `lastModif` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['pageName'], 'wiki page', 'tiki_p_view')) {
				$ret["items"]["pages"]["list"][$count]["href"]  = filter_out_sefurl('tiki-index.php?page=' . urlencode($res['pageName']), $smarty);;
				$ret["items"]["pages"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["lastModif"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["pages"]["list"][$count]["label"] = $res["pageName"]; 
				$count++;
			}
		}
		$ret["items"]["pages"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// ARTICLES
	if ($prefs['feature_articles'] == 'y' ) {    
		$ret["items"]["articles"]["label"] = tra('new articles');
		$ret["items"]["articles"]["cname"] = "slvn_articles_menu";

		if($userlib->user_has_permission($user, "tiki_p_edit_article")) {
			$query = "select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `created`>? and `expireDate`>?";
			$bindvars = array((int)$last,time());
		} else {
			$query = "select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `publishDate`>? and `publishDate`<=? and `expireDate`>?";
			$bindvars = array((int)$last,time(),time());
		}
		$result = $tikilib->query($query, $bindvars, $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['articleId'], 'article', 'tiki_p_read_article')) {
				$ret["items"]["articles"]["list"][$count]["href"]  = filter_out_sefurl('tiki-read_article.php?articleId=' . $res['articleId'], $smarty, 'article', $res['title']);
				$ret["items"]["articles"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["publishDate"]) ." ". tra("by") ." ". $res["authorName"];
				$ret["items"]["articles"]["list"][$count]["label"] = $res["title"]; 
				$count++;
			}
		}
		$ret["items"]["articles"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// FAQs
	if ($prefs['feature_faqs'] == 'y') {    
		$ret["items"]["faqs"]["label"] = tra('new FAQs');
		$ret["items"]["faqs"]["cname"] = "slvn_faqs_menu";

		$query = "select `faqId`, `title`, `created`  from `tiki_faqs` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['faqId'], 'faq', 'tiki_p_view_faq')) {
				$ret["items"]["faqs"]["list"][$count]["href"]  = "tiki-view_faq.php?faqId=" . $res["faqId"];
				$ret["items"]["faqs"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]);
				$ret["items"]["faqs"]["list"][$count]["label"] = $res["title"]; 
				$count++;
			}
		}
		$ret["items"]["faqs"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// BLOGS
	if ($prefs['feature_blogs'] == 'y') {    
		$ret["items"]["blogs"]["label"] = tra('new blogs');
		$ret["items"]["blogs"]["cname"] = "slvn_blogs_menu";

		$query = "select `blogId`, `title`, `user`, `created`  from `tiki_blogs` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$ret["items"]["blogs"]["list"][$count]["href"]  = filter_out_sefurl('tiki-view_blog.php?blogId=' . $res['blogId'], $smarty, 'blog', $res['title']);
				$ret["items"]["blogs"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["blogs"]["list"][$count]["label"] = $res["title"]; 
				$count++;
			}
		}

		$ret["items"]["blogs"]["count"] = $count;

		$ret["items"]["blogPosts"]["label"] = tra('new blog posts');
		$ret["items"]["blogPosts"]["cname"] = "slvn_blogPosts_menu";

		$query = "select `postId`, `blogId`, `title`, `user`, `created`  from `tiki_blog_posts` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$ret["items"]["blogPosts"]["list"][$count]["href"]  = filter_out_sefurl('tiki-view_blog_post.php?postId=' . $res['postId'], $smarty, 'blogpost', $res['title']);
				$ret["items"]["blogPosts"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["blogPosts"]["list"][$count]["label"] = $res["title"]; 
				$count++;
			}
		}
		$ret["items"]["blogPosts"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// IMAGE GALLERIES
	if ($prefs['feature_galleries'] == 'y') {
		// image galleries
		$ret["items"]["imageGalleries"]["label"] = tra('new image galleries');
		$ret["items"]["imageGalleries"]["cname"] = "slvn_imageGalleries_menu";
		$query = "select `galleryId`,`name`,`created`,`user` from `tiki_galleries` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$ret["items"]["imageGalleries"]["list"][$count]["href"]  = "tiki-browse_gallery.php?galleryId=" . $res["galleryId"];
				$ret["items"]["imageGalleries"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["imageGalleries"]["list"][$count]["label"] = $res["name"]; 
				$count++;
			}
		}
		$ret["items"]["imageGalleries"]["count"] = $count;

		// images
		$ret["items"]["images"]["label"] = tra('new images');
		$ret["items"]["images"]["cname"] = "slvn_images_menu";
		$query = "select `imageId`,`galleryId`,`name`,`created`,`user` from `tiki_images` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$ret["items"]["images"]["list"][$count]["href"]  = "tiki-browse_image.php?galleryId=" . $res["galleryId"]. "&imageId=" .$res["imageId"];
				$ret["items"]["images"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["images"]["list"][$count]["label"] = $res["name"]; 
				$count++;
			}
		}
		$ret["items"]["images"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// FILE GALLERIES
	if ($prefs['feature_file_galleries'] == 'y') {
		// file galleries
		$ret["items"]["fileGalleries"]["label"] = tra('new file galleries');
		$ret["items"]["fileGalleries"]["cname"] = "slvn_fileGalleries_menu";
		$query = "select `galleryId`,`name`,`created`,`user` from `tiki_file_galleries` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$ret["items"]["fileGalleries"]["list"][$count]["href"]  = "tiki-list_file_gallery.php?galleryId=" . $res["galleryId"];
				$ret["items"]["fileGalleries"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["fileGalleries"]["list"][$count]["label"] = $res["name"]; 
				$count++;
			}
		}
		$ret["items"]["fileGalleries"]["count"] = $count;

		// files
		$ret["items"]["files"]["label"] = tra('new files');//get_strings tra("new files");
		$ret["items"]["files"]["cname"] = "slvn_files_menu";
		$query = "select `galleryId`,`name`,`filename`,`created`,`user` from `tiki_files` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$ret["items"]["files"]["list"][$count]["href"]  = "tiki-list_file_gallery.php?galleryId=" . $res["galleryId"];
				$ret["items"]["files"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["created"]) ." ". tra("by") ." ". $res["user"];
				$ret["items"]["files"]["list"][$count]["label"] = $res["name"]. " (".$res["filename"].")"; 
				$count++;
			}
		}
		$ret["items"]["files"]["count"] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// POLLS
	if ($prefs['feature_polls'] == 'y') {
		$ret["items"]["polls"]["label"] = tra('new polls');
		$ret["items"]["polls"]["cname"] = "slvn_polls_menu";

		$query = "select `pollId`, `title`, `publishDate` from `tiki_polls` where `publishDate`>? order by `publishDate` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			$ret["items"]["polls"]["list"][$count]["href"]  = "tiki-poll_results.php?pollId=" . $res["pollId"];
			$ret["items"]["polls"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["publishDate"]);
			$ret["items"]["polls"]["list"][$count]["label"] = $res["title"]; 
			$count++;
		}
		$ret["items"]["polls"]["count"] = $count;
	}
  

	/////////////////////////////////////////////////////////////////////////
	// NEW USERS
	if (!isset($params['showuser']) || $params['showuser'] != 'n') {
		$ret["items"]["users"]["label"] = tra('new users');
		$ret["items"]["users"]["cname"] = "slvn_users_menu";
		$query = "select `login`, `registrationDate` from `users_users` where `registrationDate`>? and `provpass`=?";
		$result = $tikilib->query($query, array((int)$last, ''), $resultCount);

		$count = 0;
		$slvn_tmp_href = $userlib->user_has_permission($user, "tiki_p_admin") ? "tiki-assignuser.php?assign_user=" : "tiki-user_information.php?view_user=";
		while ($res = $result->fetchRow()) {
			$ret["items"]["users"]["list"][$count]["href"]  = $slvn_tmp_href . $res["login"];
			$ret["items"]["users"]["list"][$count]["title"] = $tikilib->get_short_datetime($res["registrationDate"]);
			$ret["items"]["users"]["list"][$count]["label"] = $res["login"]; 
			$count++;
		}
		$ret["items"]["users"]["count"] = $count;
	}

	/////////////////////////////////////////////////////////////////////////
	// TRACKER ITEMS
	// This breaks out tracker updates into sub-sections, by tracker, separating new items and updated items.
		// NEW TRACKER ITEMS
	if ($prefs['feature_trackers'] == 'y' && (!isset($params['showtracker']) || $params['showtracker'] != 'n')) {    
		$ret["items"]["trackers"]["label"] = tra('new tracker items');
		$ret["items"]["trackers"]["cname"] = "slvn_trackers_menu";

		$query = "select `itemId`, `trackerId`, `created`, `lastModif`  from `tiki_tracker_items` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		$counta = array();
		$tracker_name = array();
		global $cachelib;
		require_once('lib/cache/cachelib.php');
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				// Initialize tracker counter if needed.
				if (!isset($counta[$res['trackerId']])) $counta[$res['trackerId']] = 0;

				// Pull Tracker Name
				if ($res['trackerId'] > 0 && !isset($tracker_name[$res['trackerId']])) {
					$query = "select `name` from `tiki_trackers` where `trackerId` = ?";
					$tracker_name[$res['trackerId']] = $tikilib->getOne($query, $res['trackerId']);
				}

				$ret["items"]["trackers"]["tid"][$res['trackerId']]["label"] = tra('in') . ' ' . tra($tracker_name[$res["trackerId"]]);
				$ret["items"]["trackers"]["tid"][$res['trackerId']]["cname"] = "slvn_tracker" . $res["trackerId"] . "_menu";
				$ret["items"]["trackers"]["tid"][$res['trackerId']]["list"][$counta[$res['trackerId']]]["href"]  = "tiki-view_tracker_item.php?itemId=" . $res["itemId"];
				$ret["items"]["trackers"]["tid"][$res['trackerId']]["list"][$counta[$res['trackerId']]]["title"] = $tikilib->get_short_datetime($res["created"]);
	   
				// routine to verify field in tracker that's used as label
				$cacheKey = 'trackerItemLabel'.$res['itemId'];
				if (! $label = $cachelib->getCached($cacheKey)) {
					$query = "select `fieldId` from `tiki_tracker_fields` where `isMain` = ? and `trackerId` = ? order by `position`";
					$fieldId = $tikilib->getOne($query, array('y',$res['trackerId']));
					$query = "select `value` from `tiki_tracker_item_fields` where `fieldId` = ? and `itemId` = ?";
					$label = $tikilib->getOne($query, array($fieldId,$res['itemId']));

					$cachelib->cacheItem($cacheKey, $label);
				}

				// If the label is empty (b:0;), then use the item ID
				if ($label == 'b:0;' || $label == '') {
					$label = "Trk i" . $res['trackerId'] . " - ID: " . $res['itemId'];
				}
				$ret["items"]["trackers"]["tid"][$res['trackerId']]["list"][$counta[$res['trackerId']]]["label"] = $label;
 				$counta[$res['trackerId']]++;
				$ret["items"]["trackers"]["tid"][$res['trackerId']]["count"] = $counta[$res['trackerId']];
 				$count++;
			}
		}
		$ret["items"]["trackers"]["count"] = $count;


		/////////////////////////////////////////////////////////////////////////
		// UPDATED TRACKER ITEMS - ignore updates on same day as creation
		$ret["items"]["utrackers"]["label"] = tra('updated tracker items');
		$ret["items"]["utrackers"]["cname"] = "slvn_utrackers_menu";

		$query = "select `itemId`, `trackerId`, `created`, `lastModif`  from `tiki_tracker_items` where `lastModif`>? and `lastModif`!=`created` order by `lastModif` desc";
		$result = $tikilib->query($query, array((int)$last), $resultCount);

		$count = 0;
		$countb = array();
		global $cachelib;
		require_once('lib/cache/cachelib.php');
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user,$res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				// Initialize tracker counter if needed.
				if (!isset($countb[$res['trackerId']])) $countb[$res['trackerId']] = 0;

				// Pull Tracker Name
				if (!isset($tracker_name[$res['trackerId']])) {
					$query = "select `name` from `tiki_trackers` where `trackerId` = ?";
					$tracker_name[$res['trackerId']] = $tikilib->getOne($query, $res['trackerId']);
				}

				$ret["items"]["utrackers"]["tid"][$res['trackerId']]["label"] = tra("in") .' '. tra($tracker_name[$res["trackerId"]]);
				$ret["items"]["utrackers"]["tid"][$res['trackerId']]["cname"] = "slvn_utracker" . $res["trackerId"] . "_menu";
				$ret["items"]["utrackers"]["tid"][$res['trackerId']]["list"][$countb[$res['trackerId']]]["href"]  = "tiki-view_tracker_item.php?itemId=" . $res["itemId"];
				$ret["items"]["utrackers"]["tid"][$res['trackerId']]["list"][$countb[$res['trackerId']]]["title"] = $tikilib->get_short_datetime($res["lastModif"]);
	   
				// routine to verify field in tracker that's used as label
				$cacheKey = 'trackerItemLabel'.$res['itemId'];
				if (! $label = $cachelib->getCached($cacheKey)) {
					$query = "select `fieldId` from `tiki_tracker_fields` where `isMain` = ? and `trackerId` = ? order by `position`";
					$fieldId = $tikilib->getOne($query, array('y',$res['trackerId']));
					$query = "select `value` from `tiki_tracker_item_fields` where `fieldId` = ? and `itemId` = ?";
					$label = $tikilib->getOne($query, array($fieldId,$res['itemId']));

					$cachelib->cacheItem($cacheKey, $label);
				}

				// If the label is empty (b:0;), then use the item ID
				if ($label == 'b:0;' || $label == '') {
					$label = "Trk i" . $res['trackerId'] . " - ID: " . $res['itemId'];
				}
				$ret["items"]["utrackers"]["tid"][$res['trackerId']]["list"][$countb[$res['trackerId']]]["label"] = $label;
 				$countb[$res['trackerId']]++;
				$ret["items"]["utrackers"]["tid"][$res['trackerId']]["count"] = $countb[$res['trackerId']];
 				$count++;
			}
		}
		$ret["items"]["utrackers"]["count"] = $count;
	}



	//////////////////////////////////////////////////////////////////////////
	// SUMMARY
  	//get the total of items
	$ret["cant"] = 0;
	foreach ($ret["items"] as $item) {
		$ret["cant"] += $item["count"];
	}

	$smarty->assign('slvn_info', $ret);
}
