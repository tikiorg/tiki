<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_since_last_visit_new_info()
{
	return array(
		'name' => tra('Since Last Visit'),
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
			'date_as_link' => array(
				'name' => tra('Show date as a calendar link'),
				'description' => tra('If set to "n", do not add a link to tiki calendar on the date in the header (even if feature calendar is set).') . ' ' . tra('Default:') . ' "y"'
			),
			'fold_sections' => array(
				'name' => tra('Fold sections by default'),
				'description' => tra('If set to "y", fold automatically sections and show only the title (user has to click on each section in order to see the details of modifications).') . ' ' . tra('Default:') . ' "n"'
			),
			'use_jquery_ui' => array(
				'name' => tra('Use tabbed presentation'),
				'description' => tra('If set to "y", use Bootstrap tabs to show the result.') . ' ' . tra('Default:') . ' "n"'
			),
			'daysAtLeast' => array(
				'name' => tra('Minimum timespan'),
				'description' => tra('Instead of the last login time, go back this minimum time, specified in days, in case the last login time is more recent.') . ' ' . tra('Default value:') . ' "0"',
				'filter' => 'int'
			),
			'commentlength' => array(
				'name' => tra('Maximum comment length'),
				'description' => tra("If comments don't use titles this sets the maximum length for the comment snippet."),
				'filter' => 'digits',
				'default' => 40,
			),
		),
		'common_params' => array('nonums', 'rows'),
	);
}

/**
 * @param $mod_reference
 * @param null $params
 * @return bool
 */
function module_since_last_visit_new($mod_reference, $params = null)
{
	global $user;
	$smarty = TikiLib::lib('smarty');
	include_once('tiki-sefurl.php');

	if (!$user) {
		return false;
	}

	if (!isset($params['use_jquery_ui']) || $params['use_jquery_ui'] != 'y') {
		$smarty->assign('use_jquery_ui', 'n');
	} else {
		$smarty->assign('use_jquery_ui', 'y');
	}

	if (!isset($params['date_as_link']) || $params['date_as_link'] != 'n') {
		$smarty->assign('date_as_link', 'y');
	} else {
		$smarty->assign('date_as_link', 'n');
	}

	if (!isset($params['fold_sections']) || $params['fold_sections'] != 'y') {
		$smarty->assign('default_folding', 'block');
		$smarty->assign('opposite_folding', 'none');
	} else {
		$smarty->assign('default_folding', 'none');
		$smarty->assign('opposite_folding', 'block');
	}

	if (empty($params['commentlength'])) {
		$params['commentlength'] = 40;
	}

	$resultCount = $mod_reference['rows'];

	global $prefs;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty->loadPlugin('smarty_modifier_username');

	$ret = array();
	if ($params == null) {
		$params = array();
	}

	if ((empty($params['calendar_focus']) || $params['calendar_focus'] != 'ignore')
			&& strpos($_SERVER['SCRIPT_NAME'], 'tiki-calendar.php') !== false
			&& ! empty($_REQUEST['todate'])
	) {
		$last = $_REQUEST['todate'];
		$_SESSION['slvn_last_login'] = $last;
		$smarty->assign('tpl_module_title', tra('Changes since'));
	} else if (isset($_SESSION['slvn_last_login'])) {
		$last = $_SESSION['slvn_last_login'];
		$smarty->assign('tpl_module_title', tra('Changes since'));
	} else {
		$last = $tikilib->getOne('select `lastLogin` from `users_users` where `login`=?', array($user));
		$smarty->assign('tpl_module_title', tra('Since your last visit...'));
		if (!$last || !empty($params['daysAtLeast'])) {
			$now = TikiLib::lib('tiki')->now;
			if (!$last) {
				$last = $now;
			}
			if (!empty($params['daysAtLeast']) && $now - $last < $params['daysAtLeast']*60*60*24) {
				$last = $now - $params['daysAtLeast']*60*60*24;
				$smarty->assign('tpl_module_title', tr('In the last %0 days...', $params['daysAtLeast']));
			}
		}
	}
	$ret['lastLogin'] = $last;

	$ret['items']['comments']['label'] = tra('new comments');
	$ret['items']['comments']['cname'] = 'slvn_comments_menu';

	//TODO: should be a function on commentslib.php or use one of the existent functions
	$query = 'select `object`,`objectType`,`title`,`commentDate`,`userName`,`threadId`, `parentId`, `approved`, `archived`, `data`' .
					" from `tiki_comments` where `commentDate`>? and `objectType` != 'forum' order by `commentDate` desc";
	$result = $tikilib->query($query, array((int) $last), $resultCount);

	$count = 0;
	while ($res = $result->fetchRow()) {
		$ret['items']['comments']['list'][$count]['href'] = TikiLib::lib('comments')->getHref($res['objectType'], $res['object'], $res['threadId']);
		switch ($res['objectType']) {
			case 'article':
				$perm = 'tiki_p_read_article';
				$ret['items']['comments']['list'][$count]['href'] =
							filter_out_sefurl($ret['items']['comments']['list'][$count]['href'], 'article', $res['title']);
				break;

			case 'post':
				$perm = 'tiki_p_read_blog';
				$ret['items']['comments']['list'][$count]['href'] =
							filter_out_sefurl($ret['items']['comments']['list'][$count]['href'], 'blogpost', $res['title']);
				break;

			case 'blog':
				$perm = 'tiki_p_read_blog';
				$ret['items']['comments']['list'][$count]['href'] =
							filter_out_sefurl($ret['items']['comments']['list'][$count]['href'], 'blog', $res['title']);
				break;

			case 'faq':
				$perm = 'tiki_p_view_faqs';
				break;

			case 'file gallery':
				$perm = 'tiki_p_view_file_gallery';
				break;

			case 'image gallery':
				$perm = 'tiki_p_view_image_gallery';
				break;

			case 'poll':
				// no perm check for viewing polls, only a perm for taking them
				break;

			case 'wiki page':
				$perm = 'tiki_p_view';
				break;

			default:		// note trackeritme needs more complex perms checking due to status and ownership
				$perm = 'tiki_p_read_comments';
				break;
		}

		if ($res['approved'] == 'n' || $res['archived'] == 'y') {
			$visible = $userlib->user_has_perm_on_object($user, $res['object'], $res['objectType'], 'tiki_p_admin_comments');

		} else if ($res['objectType'] === 'trackeritem') {
			$item = Tracker_Item::fromId($res['object']);
			$visible = $item->canView();
		} else {
			$visible = !isset($perm) || $userlib->user_has_perm_on_object($user, $res['object'], $res['objectType'], $perm);
		}

		if ($visible) {
			$ret['items']['comments']['list'][$count]['title'] = $tikilib->get_short_datetime($res['commentDate']) .' '. tra('by') .' '. smarty_modifier_username($res['userName']);
			$ret['items']['comments']['list'][$count]['label'] = TikiLib::lib('comments')->process_comment_title($res, $params['commentlength']);;

			if ($res['archived'] == 'y') {
				$ret['items']['comments']['list'][$count]['label'] .= tra(' (archived)');
			}

			$count++;
		}
	}
	$ret['items']['comments']['count'] = $count;


	/////////////////////////////////////////////////////////////////////////
	// FORUMS
	if ($prefs['feature_forums'] == 'y') {
		$ret['items']['posts']['label'] = tra('new posts');
		$ret['items']['posts']['cname'] = 'slvn_posts_menu';
		$query = 'select `posts`.`object`,`posts`.`objectType`,`posts`.`title`,`posts`.`commentDate`,' .
							' `posts`.`userName`,`posts`.`threadId`, `posts`.`parentId`,`topics`.`title` `topic_title`' .
							' from `tiki_comments` `posts`' .
							' left join `tiki_comments` `topics` ON `posts`.`parentId` = `topics`.`threadId`' .
							" where `posts`.`commentDate`>? and `posts`.`objectType` = 'forum'" .
							' order by `posts`.`commentDate` desc';

		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['object'], $res['objectType'], 'tiki_p_forum_read')) {
				$ret['items']['posts']['list'][$count]['href']
					= 'tiki-view_forum_thread.php?forumId=' . $res['object'] . '&comments_parentId=';
				if ($res['parentId']) {
					$ret['items']['posts']['list'][$count]['href'].=$res['parentId'].'#threadId'.$res['threadId'];
				} else {
					$ret['items']['posts']['list'][$count]['href'].=$res['threadId'];
				}
				$ret['items']['posts']['list'][$count]['title'] = $tikilib->get_short_datetime($res['commentDate']) .' '. tra('by') .' '. smarty_modifier_username($res['userName']);
				if ($res['parentId'] == 0 || $prefs['forum_reply_notitle'] != 'y') {
					$ret['items']['posts']['list'][$count]['label'] = $res['title'];
				} else {
					$ret['items']['posts']['list'][$count]['label'] = $res['topic_title'];
				}
				++$count;
			}
		}
		$ret['items']['posts']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// WIKI PAGES
	if ($prefs['feature_wiki'] == 'y') {
		$ret['items']['pages']['label'] = tra('wiki pages changed');
		$ret['items']['pages']['cname'] = 'slvn_pages_menu';
		$query = 'select `pageName`, `user`, `lastModif` from `tiki_pages` where `lastModif`>? order by `lastModif` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['pageName'], 'wiki page', 'tiki_p_view')) {
				$ret['items']['pages']['list'][$count]['href']  = filter_out_sefurl('tiki-index.php?page=' . urlencode($res['pageName']));;
				$ret['items']['pages']['list'][$count]['title'] = $tikilib->get_short_datetime($res['lastModif']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['pages']['list'][$count]['label'] = $res['pageName'];
				$count++;
			}
		}
		$ret['items']['pages']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// ARTICLES
	if ($prefs['feature_articles'] == 'y') {
		$ret['items']['articles']['label'] = tra('new articles');
		$ret['items']['articles']['cname'] = 'slvn_articles_menu';

		if ($userlib->user_has_permission($user, 'tiki_p_edit_article')) {
			$query = 'select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `created`>? and `expireDate`>? order by `articleId` desc';
			$bindvars = array((int) $last, time());
		} else {
			$query = 'select `articleId`,`title`,`publishDate`,`authorName` from `tiki_articles` where `publishDate`>? and `publishDate`<=? and `expireDate`>? order by `articleId` desc';
			$bindvars = array((int) $last,time(),time());
		}
		$result = $tikilib->query($query, $bindvars, $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['articleId'], 'article', 'tiki_p_read_article')) {
				$ret['items']['articles']['list'][$count]['href']  = filter_out_sefurl('tiki-read_article.php?articleId=' . $res['articleId'], 'article', $res['title']);
				$ret['items']['articles']['list'][$count]['title'] = $tikilib->get_short_datetime($res['publishDate']) .' '. tra('by') .' '. $res['authorName'];
				$ret['items']['articles']['list'][$count]['label'] = $res['title'];
				$count++;
			}
		}
		$ret['items']['articles']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// FAQs
	if ($prefs['feature_faqs'] == 'y') {
		$ret['items']['faqs']['label'] = tra('new FAQs');
		$ret['items']['faqs']['cname'] = 'slvn_faqs_menu';

		$query = 'select `faqId`, `title`, `created` from `tiki_faqs` where `created`>? order by `created` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['faqId'], 'faq', 'tiki_p_view_faq')) {
				$ret['items']['faqs']['list'][$count]['href']  = 'tiki-view_faq.php?faqId=' . $res['faqId'];
				$ret['items']['faqs']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']);
				$ret['items']['faqs']['list'][$count]['label'] = $res['title'];
				$count++;
			}
		}
		$ret['items']['faqs']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// BLOGS
	if ($prefs['feature_blogs'] == 'y') {
		$ret['items']['blogs']['label'] = tra('new blogs');
		$ret['items']['blogs']['cname'] = 'slvn_blogs_menu';

		$query = "select `blogId`, `title`, `user`, `created` from `tiki_blogs` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$ret['items']['blogs']['list'][$count]['href']  = filter_out_sefurl('tiki-view_blog.php?blogId=' . $res['blogId'], 'blog', $res['title']);
				$ret['items']['blogs']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['blogs']['list'][$count]['label'] = $res['title'];
				$count++;
			}
		}

		$ret['items']['blogs']['count'] = $count;

		$ret['items']['blogPosts']['label'] = tra('new blog posts');
		$ret['items']['blogPosts']['cname'] = 'slvn_blogPosts_menu';

		$query = 'select `postId`, `blogId`, `title`, `user`, `created` from `tiki_blog_posts` where `created`>? order by `created` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['blogId'], 'blog', 'tiki_p_read_blog')) {
				$ret['items']['blogPosts']['list'][$count]['href']  = filter_out_sefurl('tiki-view_blog_post.php?postId=' . $res['postId'], 'blogpost', $res['title']);
				$ret['items']['blogPosts']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['blogPosts']['list'][$count]['label'] = $res['title'];
				$count++;
			}
		}
		$ret['items']['blogPosts']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// IMAGE GALLERIES
	if ($prefs['feature_galleries'] == 'y') {
		// image galleries
		$ret['items']['imageGalleries']['label'] = tra('new image galleries');
		$ret['items']['imageGalleries']['cname'] = 'slvn_imageGalleries_menu';
		$query = "select `galleryId`,`name`,`created`,`user` from `tiki_galleries` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$ret['items']['imageGalleries']['list'][$count]['href']  = 'tiki-browse_gallery.php?galleryId=' . $res['galleryId'];
				$ret['items']['imageGalleries']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['imageGalleries']['list'][$count]['label'] = $res['name'];
				$count++;
			}
		}
		$ret['items']['imageGalleries']['count'] = $count;

		// images
		$ret['items']['images']['label'] = tra('new images');
		$ret['items']['images']['cname'] = 'slvn_images_menu';
		$query = 'select `imageId`,`galleryId`,`name`,`created`,`user` from `tiki_images` where `created`>? order by `created` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$ret['items']['images']['list'][$count]['href']  = 'tiki-browse_image.php?galleryId=' . $res['galleryId']. '&imageId=' . $res['imageId'];
				$ret['items']['images']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['images']['list'][$count]['label'] = $res['name'];
				$count++;
			}
		}
		$ret['items']['images']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// FILE GALLERIES
	if ($prefs['feature_file_galleries'] == 'y') {
		// file galleries
		$ret['items']['fileGalleries']['label'] = tra('new file galleries');
		$ret['items']['fileGalleries']['cname'] = 'slvn_fileGalleries_menu';
		$query = 'select `galleryId`,`name`,`created`,`user` from `tiki_file_galleries` where `created`>? order by `created` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$ret['items']['fileGalleries']['list'][$count]['href']  = filter_out_sefurl('tiki-list_file_gallery.php?galleryId=' . $res['galleryId'], 'file gallery');
				$ret['items']['fileGalleries']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) . ' ' . tra('by') . ' ' . smarty_modifier_username($res['user']);
				$ret['items']['fileGalleries']['list'][$count]['label'] = $res['name'];
				$count++;
			}
		}
		$ret['items']['fileGalleries']['count'] = $count;

		// files
		$ret['items']['files']['label'] = tra('new files');//get_strings tra('new files');
		$ret['items']['files']['cname'] = 'slvn_files_menu';
		$query = 'select `fileId`, `galleryId`,`name`,`filename`,`created`,`user` from `tiki_files` where `created`>? order by `created` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['galleryId'], 'file gallery', 'tiki_p_view_file_gallery')) {
				$ret['items']['files']['list'][$count]['href']  = filter_out_sefurl('tiki-list_file_gallery.php?galleryId=' . $res['galleryId']. '&fileId=' . $res['fileId']. '&view=page', 'file gallery');
				$ret['items']['files']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['files']['list'][$count]['label'] = $res['name'] . ' (' . $res['filename'] . ')';
				$count++;
			}
		}
		$ret['items']['files']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// POLLS
	if ($prefs['feature_polls'] == 'y') {
		$ret['items']['polls']['label'] = tra('new polls');
		$ret['items']['polls']['cname'] = 'slvn_polls_menu';

		$query = 'select `pollId`, `title`, `publishDate` from `tiki_polls` where `publishDate`>? order by `publishDate` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			$ret['items']['polls']['list'][$count]['href']  = 'tiki-poll_results.php?pollId=' . $res['pollId'];
			$ret['items']['polls']['list'][$count]['title'] = $tikilib->get_short_datetime($res['publishDate']);
			$ret['items']['polls']['list'][$count]['label'] = $res['title'];
			$count++;
		}
		$ret['items']['polls']['count'] = $count;
	}


	/////////////////////////////////////////////////////////////////////////
	// NEW USERS
	if (!isset($params['showuser']) || $params['showuser'] != 'n') {
		$ret['items']['users']['label'] = tra('new users');
		$ret['items']['users']['cname'] = 'slvn_users_menu';
		$query = 'select `login`, `registrationDate` from `users_users` where `registrationDate`>? and `provpass`=?';
		$result = $tikilib->query($query, array((int) $last, ''), $resultCount);

		$count = 0;
		$slvn_tmp_href = $userlib->user_has_permission($user, 'tiki_p_admin') ? 'tiki-assignuser.php?assign_user=' : 'tiki-user_information.php?view_user=';
		while ($res = $result->fetchRow()) {
			$ret['items']['users']['list'][$count]['href']  = $slvn_tmp_href . rawurlencode($res['login']);
			$ret['items']['users']['list'][$count]['title'] = $tikilib->get_short_datetime($res['registrationDate']);
			$ret['items']['users']['list'][$count]['label'] = smarty_modifier_username($res['login']);
			$count++;
		}
		$ret['items']['users']['count'] = $count;
	}

	/////////////////////////////////////////////////////////////////////////
	// TRACKER ITEMS
	// This breaks out tracker updates into sub-sections, by tracker, separating new items and updated items.
		// NEW TRACKER ITEMS
	if ($prefs['feature_trackers'] == 'y' && (!isset($params['showtracker']) || $params['showtracker'] != 'n')) {
		$ret['items']['trackers']['label'] = tra('new tracker items');
		$ret['items']['trackers']['cname'] = 'slvn_trackers_menu';

		$query = 'select `itemId`, `trackerId`, `created`, `lastModif`  from `tiki_tracker_items` where `created`>? order by `created` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		$counta = array();
		$tracker_name = array();
		$cachelib = TikiLib::lib('cache');
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				// Initialize tracker counter if needed.
				if (!isset($counta[$res['trackerId']])) {
					$counta[$res['trackerId']] = 0;
				}

				// Pull Tracker Name
				if ($res['trackerId'] > 0 && !isset($tracker_name[$res['trackerId']])) {
					$query = "select `name` from `tiki_trackers` where `trackerId` = ?";
					$tracker_name[$res['trackerId']] = $tikilib->getOne($query, $res['trackerId']);
				}

				$ret['items']['trackers']['tid'][$res['trackerId']]['label'] = tra('in') . ' ' . tra($tracker_name[$res['trackerId']]);
				$ret['items']['trackers']['tid'][$res['trackerId']]['cname'] = 'slvn_tracker' . $res['trackerId'] . '_menu';
				$ret['items']['trackers']['tid'][$res['trackerId']]['list'][$counta[$res['trackerId']]]['href'] = filter_out_sefurl(
					'tiki-view_tracker_item.php?itemId=' . $res['itemId'],
					'trackeritem'
				);
				$ret['items']['trackers']['tid'][$res['trackerId']]['list'][$counta[$res['trackerId']]]['title'] = $tikilib->get_short_datetime($res['created']);

				// routine to verify field in tracker that's used as label
				$cacheKey = 'trackerItemLabel'.$res['itemId'];
				if (! $label = $cachelib->getCached($cacheKey)) {
					$query = 'select `fieldId` from `tiki_tracker_fields` where `isMain` = ? and `trackerId` = ? order by `position`';
					$fieldId = $tikilib->getOne($query, array('y',$res['trackerId']));
					$query = 'select `value` from `tiki_tracker_item_fields` where `fieldId` = ? and `itemId` = ?';
					$label = $tikilib->getOne($query, array($fieldId,$res['itemId']));

					$cachelib->cacheItem($cacheKey, $label);
				}

				// If the label is empty (b:0;), then use the item ID
				if ($label == 'b:0;' || $label == '') {
					$label = 'Trk i' . $res['trackerId'] . ' - ID: ' . $res['itemId'];
				}
				$ret['items']['trackers']['tid'][$res['trackerId']]['list'][$counta[$res['trackerId']]]['label'] = $label;
 				$counta[$res['trackerId']]++;
				$ret['items']['trackers']['tid'][$res['trackerId']]['count'] = $counta[$res['trackerId']];
 				$count++;
			}
		}
		$ret['items']['trackers']['count'] = $count;


		/////////////////////////////////////////////////////////////////////////
		// UPDATED TRACKER ITEMS - ignore updates on same day as creation
		$ret['items']['utrackers']['label'] = tra('updated tracker items');
		$ret['items']['utrackers']['cname'] = 'slvn_utrackers_menu';

		$query = 'select `itemId`, `trackerId`, `created`, `lastModif`' .
						' from `tiki_tracker_items` where `lastModif`>? and `lastModif`!=`created`' .
						' order by `lastModif` desc';
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		$countb = array();
		$cachelib = TikiLib::lib('cache');

		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				// Initialize tracker counter if needed.
				if (!isset($countb[$res['trackerId']])) {
					$countb[$res['trackerId']] = 0;
				}

				// Pull Tracker Name
				if (!isset($tracker_name[$res['trackerId']])) {
					$query = 'select `name` from `tiki_trackers` where `trackerId` = ?';
					$tracker_name[$res['trackerId']] = $tikilib->getOne($query, $res['trackerId']);
				}

				$ret['items']['utrackers']['tid'][$res['trackerId']]['label'] = tra('in') .' '. tra($tracker_name[$res['trackerId']]);
				$ret['items']['utrackers']['tid'][$res['trackerId']]['cname'] = 'slvn_utracker' . $res['trackerId'] . '_menu';
				$ret['items']['utrackers']['tid'][$res['trackerId']]['list'][$countb[$res['trackerId']]]['href']  = filter_out_sefurl(
					'tiki-view_tracker_item.php?itemId=' . $res['itemId'],
					'trackeritem'
				);
				$ret['items']['utrackers']['tid'][$res['trackerId']]['list'][$countb[$res['trackerId']]]['title'] = $tikilib->get_short_datetime($res['lastModif']);

				// routine to verify field in tracker that's used as label
				$cacheKey = 'trackerItemLabel'.$res['itemId'];
				if (! $label = $cachelib->getCached($cacheKey)) {
					$query = 'select `fieldId` from `tiki_tracker_fields` where `isMain` = ? and `trackerId` = ? order by `position`';
					$fieldId = $tikilib->getOne($query, array('y',$res['trackerId']));
					$query = 'select `value` from `tiki_tracker_item_fields` where `fieldId` = ? and `itemId` = ?';
					$label = $tikilib->getOne($query, array($fieldId,$res['itemId']));

					$cachelib->cacheItem($cacheKey, $label);
				}

				// If the label is empty (b:0;), then use the item ID
				if ($label == 'b:0;' || $label == '') {
					$label = 'Trk i' . $res['trackerId'] . ' - ID: ' . $res['itemId'];
				}
				$ret['items']['utrackers']['tid'][$res['trackerId']]['list'][$countb[$res['trackerId']]]['label'] = $label;
 				$countb[$res['trackerId']]++;
				$ret['items']['utrackers']['tid'][$res['trackerId']]['count'] = $countb[$res['trackerId']];
 				$count++;
			}
		}
		$ret['items']['utrackers']['count'] = $count;
	}

	/////////////////////////////////////////////////////////////////////////
	// CALENDARS & THEIR EVENTS
	if ($prefs['feature_calendar'] == 'y') {
		$ret['items']['calendar']['label'] = tra('new calendars');
		$ret['items']['calendar']['cname'] = 'slvn_calendar_menu';

		$query = "select `calendarId`, `name`, `user`, `created` from `tiki_calendars` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['calendarId'], 'calendar', 'tiki_p_view_calendar')) {
				$ret['items']['calendar']['list'][$count]['href']  = filter_out_sefurl('tiki-calendar.php?calIds[]=' . $res['calendarId'], 'calendar', $res['name']);
				$ret['items']['calendar']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']);
				$ret['items']['calendar']['list'][$count]['label'] = $res['name'];
				$count++;
			}
		}
		$ret['items']['calendar']['count'] = $count;

		$ret['items']['events']['label'] = tra('new events');
		$ret['items']['events']['cname'] = 'slvn_events_menu';

		$query = "select `calitemId`, `calendarId`, `name`, `user`, `created`, `start` from `tiki_calendar_items` where `created`>? order by `created` desc";
		$result = $tikilib->query($query, array((int) $last), $resultCount);

		$count = 0;
		while ($res = $result->fetchRow()) {
			if ($userlib->user_has_perm_on_object($user, $res['calendarId'], 'calendar', 'tiki_p_view_events')) {
				$ret['items']['events']['list'][$count]['href']  = filter_out_sefurl('tiki-calendar_edit_item.php?viewcalitemId=' . $res['calitemId'], 'event', $res['name']);
				$ret['items']['events']['list'][$count]['title'] = $tikilib->get_short_datetime($res['created']) .' '. tra('by') .' '. smarty_modifier_username($res['user']) .', '. tra('starting on') .' '. $tikilib->get_short_datetime($res['start']) ;
				$ret['items']['events']['list'][$count]['label'] = $res['name'];
				$count++;
			}
		}
		$ret['items']['events']['count'] = $count;
	}

	//////////////////////////////////////////////////////////////////////////
	// SUMMARY
	//get the total of items
	$ret['cant'] = 0;
	$ret['nonempty'] = 0;
	foreach ($ret['items'] as $item) {
		$ret['cant'] += $item['count'];
		if ($item['count'] > 0) {
			$ret['nonempty']++;
		}
	}
	if ($ret['nonempty'] > 0) {
		$ret['li_width'] = min(22, (int)90 / $ret['nonempty']);
	} else {
		$ret['li_width'] = 90;
	}

	$smarty->assign('slvn_info', $ret);
}
