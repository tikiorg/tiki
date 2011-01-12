<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

include_once('lib/reportslib.php');

class ArtLib extends TikiLib
{
	//Special parsing for multipage articles
	function get_number_of_pages($data)
	{
		$parts = explode('...page...', $data);
		return count($parts);
	}

	function get_page($data, $i)
	{
		// Get slides
		$parts = explode('...page...', $data);

		if (!isset($parts[$i - 1])) {
			$i = 1;
		}
		$ret = $parts[$i - 1];
		if (substr($parts[$i - 1], 1, 5) == '<br/>')
			$ret = substr($parts[$i - 1], 6);

		if (substr($parts[$i - 1], 1, 6) == '<br />')
			$ret = substr($parts[$i - 1], 7);

		return $ret;
	}

	function approve_submission($subId)
	{
		$data = $this->get_submission($subId);

		if (!$data)
			return false;

		if (!$data['image_x'])
			$data['image_x'] = 0;

		if (!$data['image_y'])
			$data['image_y'] = 0;

		$articleId = $this->replace_article($data['title']
																			, $data['authorName']
																			, $data['topicId']
																			, $data['useImage']
																			, $data['image_name']
																			, $data['image_size']
																			, $data['image_type']
																			, $data['image_data']
																			, $data['heading']
																			, $data['body']
																			, $data['publishDate']
																			, $data['expireDate']
																			, $data['author']
																			, 0
																			, $data['image_x']
																			, $data['image_y']
																			, $data['type']
																			, $data['topline']
																			, $data['subtitle']
																			, $data['linkto']
																			, $data['image_caption']
																			, $data['lang']
																			, $data['rating']
																			, $data['isfloat']
																			);
		$this->transfer_attributes_from_submission($subId, $articleId);
		global $prefs;
		if ($prefs['feature_categories'] == 'y') {
			global $categlib; include_once('lib/categories/categlib.php');
			$categlib->approve_submission($subId, $articleId);
		}
		$query = 'update `tiki_objects` set `href`=?, `type`=? where `href`=?';
		$this->query($query, array("'tiki-read_article.php?articleId=$articleId", 'article', "tiki-edit_submission.php?subId=$subId"));
		
		$this->remove_submission($subId);
	}

	function add_article_hit($articleId)
	{
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_articles` set `nbreads`=`nbreads`+1 where `articleId`=?";

			$result = $this->query($query, array($articleId));
		}

		return true;
	}

	function remove_article($articleId, $article_data ='')
	{
		global $smarty, $tikilib, $user, $prefs, $reportslib;
		
		if ($articleId) {
			if (empty($article_data)) $article_data = $this->get_article($articleId);
			$query = 'delete from `tiki_articles` where `articleId`=?';

			$result = $this->query($query, array($articleId));
			$this->remove_object('article', $articleId);
			
			// TODO refactor
			$nots = $tikilib->get_event_watches('article_deleted', '*');
			if (!empty($article_data['topicId']))
				$nots2 = $tikilib->get_event_watches('topic_article_deleted', $article_data['topicId']);
			else
				$nots2 = array();
			$smarty->assign('mail_action', 'Delete');
			
			$nots3 = array();
			foreach ($nots as $n) {
				$nots3[] = $n['email'];
			}
			foreach ($nots2 as $n) {
				if (!in_array($n['email'], $nots3))
					$nots[] = $n;
			}
			if (!isset($_SERVER['SERVER_NAME'])) {
				$_SERVER['SERVER_NAME'] = $_SERVER["HTTP_HOST"];
			}

			if ($prefs['feature_user_watches'] == 'y' && $prefs['feature_daily_report_watches'] == 'y') {
				$reportslib->makeReportCache( $nots
																		, array('event'			=> 'article_deleted'
																			, 'articleId'			=> $articleId
																			, 'articleTitle'	=> $article_data['title']
																			, 'authorName'		=> $article_data['authorName']
																			, 'user'					=> $user)
																		);
			}

			if (count($nots) || (!empty($emails) && is_array($emails))) {
				include_once('lib/notifications/notificationemaillib.php');
	
				$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
				$smarty->assign('mail_title', 'articleId=' . $articleId);
				$smarty->assign('mail_postid', $articleId);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_data', $article_data['heading'] . "\n----------------------\n");
				$smarty->assign('mail_heading', $heading);
				$smarty->assign('mail_body', $body);
				sendEmailNotification($nots, 'watch', 'user_watch_article_post_subject.tpl', $_SERVER['SERVER_NAME'], 'user_watch_article_post.tpl');
			}

			return true;
		}
	}

	function remove_submission($subId)
	{
		if ($subId) {
			$query = 'delete from `tiki_submissions` where `subId`=?';
			$result = $this->query($query, array((int) $subId));
			$this->remove_object('submission', $subId);
			return true;
		}
	}

	function replace_submission($title
														, $authorName
														, $topicId
														, $useImage
														, $imgname
														, $imgsize
														, $imgtype
														, $imgdata
														, $heading
														, $body
														, $publishDate
														, $expireDate
														, $user
														, $subId
														, $image_x
														, $image_y
														, $type
														,	$topline
														, $subtitle
														, $linkto
														, $image_caption
														, $lang
														, $rating = 0
														, $isfloat = 'n'
														)
	{
		global $smarty, $tiki_p_autoapprove_submission, $tikilib, $dbTiki, $prefs;

		if ($expireDate < $publishDate) {
			$expireDate = $publishDate;
		}
		
		if (empty($imgdata))
			$imgdata = '';
		
		global $notificationlib;
		if (!is_object($notificationlib)) {
			require_once('lib/notifications/notificationlib.php');
		}
		$hash = md5($title . $heading . $body);
		$query = 'select `name` from `tiki_topics` where `topicId` = ?';
		$topicName = $this->getOne($query, array((int) $topicId));
		$size = strlen($body);

		if ($subId) {
			// Update the article
			$query = 'update `tiki_submissions` set
									`title` = ?,
									`authorName` = ?,
									`topicId` = ?,
									`topicName` = ?,
									`size` = ?,
									`useImage` = ?,
									`isfloat` = ?,
									`image_name` = ?,
									`image_type` = ?,
									`image_size` = ?,
									`image_data` = ?,
									`image_x` = ?,
									`image_y` = ?,
									`heading` = ?,
									`body` = ?,
									`publishDate` = ?,
									`expireDate` = ?,
									`created` = ?,
									`author` = ? ,
									`type` = ?,
									`rating` = ?,
									`topline`=?,
									`subtitle`=?,
									`linkto`=?,
									`image_caption`=?,
									`lang`=?
							where `subId` = ?';

			$result = $this->query($query, array( $title
																					, $authorName
																					, (int) $topicId
																					, $topicName
																					, (int) $size
																					, $useImage
																					, $isfloat
																					, $imgname
																					, $imgtype
																					, (int) $imgsize
																					, $imgdata
																					, (int) $image_x
																					, (int) $image_y
																					, $heading
																					, $body
																					, (int) $publishDate
																					, (int) $expireDate
																					, (int) $this->now
																					, $user
																					, $type
																					, (float) $rating
																					, $topline
																					, $subtitle
																					, $linkto
																					, $image_caption
																					, $lang
																					, (int) $subId
																				)
																			);
			$id = $subId;
		} else {
			// Insert the article
			$query = 'insert into `tiki_submissions`(`title`,`authorName`,`topicId`,`useImage`'
								. ',`image_name`,`image_size`,`image_type`,`image_data`,`publishDate`,`expireDate`'
								.	',`created`,`heading`,`body`,`hash`,`author`,`nbreads`,`votes`,`points`'
								.	',`size`,`topicName`,`image_x`,`image_y`,`type`,`rating`,`isfloat`,`topline`'
								.	', `subtitle`, `linkto`,`image_caption`, `lang`)'
							.	' values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
							;

			$result = $this->query($query, array( $title
																					, $authorName
																					, (int) $topicId
																					, $useImage
																					, $imgname
																					, (int) $imgsize
																					, $imgtype
																					, $imgdata
																					, (int) $publishDate
																					, (int) $expireDate
																					, (int) $this->now
																					, $heading
																					, $body
																					, $hash
																					, $user
																					, 0
																					, 0
																					, 0
																					, (int) $size
																					, $topicName
																					, (int) $image_x
																					, (int) $image_y
																					, $type
																					, (float) $rating
																					, $isfloat
																					, $topline
																					, $subtitle
																					, $linkto
																					, $image_caption
																					, $lang
																				)
																			);
			// Fixed query. -edgar
			$id = $this->lastInsertId();
		}

		if ($tiki_p_autoapprove_submission != 'y') {
			$emails = $tikilib->get_event_watches('article_submitted', '*');
			$emails2 = $tikilib->get_event_watches('topic_article_created', $topicId);
			$emails3 = array();
			foreach ($emails as $n) {
			$emails3[] = $n['email'];
			}
			foreach ($emails2 as $n) {
				if (!in_array($n['emails'], $emails3))
					$emails[] = $n;
			}
			if (!isset($_SERVER['SERVER_NAME'])) {
				$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
			}
			if (count($emails)) {
				include_once('lib/notifications/notificationemaillib.php');
				$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_title', $title);
				$smarty->assign('mail_heading', $heading);
				$smarty->assign('mail_body', $body);
				$smarty->assign('mail_subId', $id);
				sendEmailNotification($emails, 'watch', 'submission_notification_subject.tpl', $_SERVER['SERVER_NAME'], 'submission_notification.tpl');
			}
		}
		global $tikilib;
		$tikilib->object_post_save( array(
			'type' => 'submission',
			'object' => $id,
			'description' => substr($heading, 0, 200), 
			'name' => $title,
			'href' => "tiki-edit_submission.php?subId=$id",
			),
			array( 'content' => $heading . "\n" .$body )
		);

		return $id;
	}

	function replace_article( $title
													, $authorName
													, $topicId
													, $useImage
													, $imgname
													, $imgsize
													, $imgtype
													, $imgdata
													, $heading
													, $body
													, $publishDate
													, $expireDate
													, $user
													, $articleId
													, $image_x
													, $image_y
													, $type
													, $topline
													, $subtitle
													, $linkto
													, $image_caption
													, $lang
													, $rating = 0
													, $isfloat = 'n'
													, $emails = ''
													, $from = ''
													, $list_image_x = ''
													, $ispublished='y'
												)
	{
		
		global $smarty, $tikilib, $reportslib;
		
		if ($expireDate < $publishDate) {
			$expireDate = $publishDate;
		}
		$hash = md5($title . $heading . $body);
		if (empty($imgdata) || $useImage === 'n') {	// remove image data if not using it
			$imgdata = '';
		}
		
		$query = 'select `name` from `tiki_topics` where `topicId` = ?';
		$topicName = $this->getOne($query, array($topicId) );
		$size = strlen($body);

		if ($articleId) {
			$query	= 'update `tiki_articles` set `title` = ?, `authorName` = ?, `topicId` = ?, `topicName` = ?, `size` = ?, `useImage` = ?, `image_name` = ?, ';
			$query .= ' `image_type` = ?, `image_size` = ?, `image_data` = ?, `isfloat` = ?, `image_x` = ?, `image_y` = ?, `list_image_x` = ?, `heading` = ?, `body` = ?, ';
			$query .= ' `publishDate` = ?, `expireDate` = ?, `created` = ?, `author` = ?, `type` = ?, `rating` = ?, `topline`=?, `subtitle`=?, `linkto`=?, ';
			$query .= ' `image_caption`=?, `lang`=?, `ispublished`=? where `articleId` = ?';

			$result = $this->query($query, array( $title
																					, $authorName
																					, (int) $topicId
																					, $topicName
																					, (int) $size
																					, $useImage
																					, $imgname
																					, $imgtype
																					, (int) $imgsize
																					, $imgdata
																					, $isfloat
																					, (int) $image_x
																					, (int) $image_y
																					, (int) $list_image_x
																					, $heading
																					, $body
																					, (int) $publishDate
																					, (int) $expireDate
																					, (int) $this->now
																					, $user
																					, $type
																					, (float) $rating
																					, $topline
																					, $subtitle
																					, $linkto
																					, $image_caption
																					, $lang
																					, $ispublished
																					, (int) $articleId
																				)
																			);
			// Clear article image cache because image may just have been changed
			$this->delete_image_cache('article', $articleId);
			
			$event = 'article_edited';
			$nots = $tikilib->get_event_watches('article_edited', '*');
			$nots2 = $tikilib->get_event_watches('topic_article_edited', $topicId);
			$smarty->assign('mail_action', 'Edit');
			
		} else {
			// Insert the article
			$query	= 'insert into `tiki_articles` (`title`, `authorName`, `topicId`, `useImage`, `image_name`, `image_size`, `image_type`, `image_data`, ';
			$query .= ' `publishDate`, `expireDate`, `created`, `heading`, `body`, `hash`, `author`, `nbreads`, `votes`, `points`, `size`, `topicName`, ';
			$query .= ' `image_x`, `image_y`, `list_image_x`, `type`, `rating`, `isfloat`,`topline`, `subtitle`, `linkto`,`image_caption`, `lang`, `ispublished`) ';
			$query .= ' values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
	
			$result = $this->query($query, array( $title
																					, $authorName
																					, (int) $topicId
																					, $useImage
																					, $imgname
																					, (int) $imgsize
																					, $imgtype
																					, $imgdata
																					, (int) $publishDate
																					, (int) $expireDate
																					, (int) $this->now
																					, $heading
																					, $body
																					, $hash
																					, $user
																					, 0
																					, 0
																					, 0
																					, (int) $size
																					, $topicName
																					, (int) $image_x
																					, (int) $image_y
																					, (int) $list_image_x
																					, $type
																					, (float) $rating
																					, $isfloat
																					, $topline
																					, $subtitle
																					, $linkto
																					, $image_caption
																					, $lang
																					, $ispublished
																					
																				)
																			);

			$query2 = 'select max(`articleId`) from `tiki_articles` where `created` = ? and `title`=? and `hash`=?';
			$articleId = $this->getOne($query2, array( (int) $this->now, $title, $hash ) );

			global $prefs;
			if ($prefs['feature_score'] == 'y') {
				$this->score_event($user, 'article_new');
			}
			$event = 'article_submitted';
			$nots = $tikilib->get_event_watches('article_submitted', '*');
			$nots2 = $tikilib->get_event_watches('topic_article_created', $topicId);
			$smarty->assign('mail_action', 'New');
		}
		
		$nots3 = array();
		foreach ($nots as $n) {
			$nots3[] = $n['email'];
		}
		foreach ($nots2 as $n) {
			if (!in_array($n['email'], $nots3))
				$nots[] = $n;
		}
		if (is_array($emails) && (empty ($from) || $from == $prefs['sender_email'])) {
			foreach ($emails as $n) {
				if (!in_array($n, $nots3))
					$nots[] = array('email' => $n, 'language' => $prefs['site_language']);
			}
		}
		if (!isset($_SERVER['SERVER_NAME'])) {
			$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
		}

		global $prefs;
		if ($prefs['feature_user_watches'] == 'y' && $prefs['feature_daily_report_watches'] == 'y') {
			$reportslib->makeReportCache($nots, array('event' => $event
																							, 'articleId' => $articleId
																							, 'articleTitle' => $title
																							, 'authorName' => $authorName
																							, 'user' => $user
																							)
																						);
		}

		if (count($nots) || is_array($emails)) {
			include_once('lib/notifications/notificationemaillib.php');

			$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
			$smarty->assign('mail_title', $title);
			$smarty->assign('mail_postid', $articleId);
			$smarty->assign('mail_user', $user);
			$smarty->assign('mail_data', $heading."\n----------------------\n" . $body);
			$smarty->assign('mail_heading', $heading);
			$smarty->assign('mail_body', $body);
			sendEmailNotification($nots, 'watch', 'user_watch_article_post_subject.tpl', $_SERVER['SERVER_NAME'], 'user_watch_article_post.tpl');
			if (is_array($emails) && !empty($from) && $from != $prefs['sender_email']) {
				$nots = array();
				foreach ($emails as $n) {
					$nots[] = array('email' => $n, 'language' => $prefs['site_language']);
				}	
				sendEmailNotification($nots, 'watch', 'user_watch_article_post_subject.tpl', $_SERVER['SERVER_NAME'], 'user_watch_article_post.tpl', $from);
			}
		}


		require_once('lib/search/refresh-functions.php');
		refresh_index('articles', $articleId);

		global $tikilib;
		$tikilib->object_post_save( array(
			'type' => 'article',
			'object' => $articleId,
			'description' => substr($heading, 0, 200),
			'name' => $title,
			'href' => "tiki-read_article.php?articleId=$articleId"
			),
			array( 'content' => $body . "\n" . $heading )
		);

		return $articleId;
	}

	function add_topic($name, $imagename, $imagetype, $imagesize, $imagedata)
	{
		$query = 'insert into `tiki_topics`(`name`,`image_name`,`image_type`,`image_size`,`image_data`,`active`,`created`) values(?,?,?,?,?,?,?)';
		$result = $this->query($query, array($name, $imagename, $imagetype, (int) $imagesize, $imagedata, 'y', (int) $this->now));

		$query = 'select max(`topicId`) from `tiki_topics` where `created`=? and `name`=?';
		$topicId = $this->getOne($query, array((int) $this->now, $name));
		return $topicId;
	}

	function remove_topic($topicId, $all = 0)
	{
		$query = 'delete from `tiki_topics` where `topicId`=?';

		$result = $this->query($query, array($topicId));

		if ($all == 1) {
			$query = 'delete from `tiki_articles` where `topicId`=?';
			$result = $this->query($query, array($topicId));
		}
		else {
			$query = 'update `tiki_articles` set `topicId`=?, `topicName`=? where `topicId`=?';
			$result = $this->query($query, array(NULL, NULL, $topicId));
		}

		return true;
	}

	function replace_topic_name($topicId, $name)
	{
		$query = 'update `tiki_topics` set `name` = ? where `topicId` = ?';
		$result = $this->query($query, array($name, (int)$topicId));

		$query = 'update `tiki_articles` set `topicName` = ? where `topicId`= ?';
		$result = $this->query($query, array($name, (int)$topicId));
		return true;
	}

	function replace_topic_image($topicId, $imagename, $imagetype, $imagesize, $imagedata)
	{
		$topicId = (int)$topicId;
		$query = 'update `tiki_topics` set `image_name` = ?, `image_type` = ?, `image_size` = ?, `image_data` = ? where `topicId` = ?';
		$result = $this->query($query, array($imagename, $imagetype,
					$imagesize, $imagedata, $topicId));

		return true;
	}

	function activate_topic($topicId)
	{
		$query = 'update `tiki_topics` set `active`=? where `topicId`=?';

		$result = $this->query($query, array('y', $topicId));
	}

	function deactivate_topic($topicId)
	{
		$query = 'update `tiki_topics` set `active`=? where `topicId`=?';

		$result = $this->query($query, array('n', $topicId));
	}

	function get_topic($topicId)
	{
		$query = 'select `topicId`,`name`,`image_name`,`image_size`,`image_type` from `tiki_topics` where `topicId`=?';

		$result = $this->query($query, array($topicId));

		$res = $result->fetchRow();
		return $res;
	}

	function get_topicId($name)
	{
		$query = 'select `topicId` from `tiki_topics` where `name`=?';
		return $this->getOne($query, array($name));
	}

	function list_topics()
	{
		$query = 'select `topicId`,`name`,`image_name`,`image_size`,`image_type`,`active` from `tiki_topics` order by `name`';

		$result = $this->query($query, array());

		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['subs'] = $this->getOne('select count(*) from `tiki_submissions` where `topicId`=?', array($res['topicId']));

			$res['arts'] = $this->getOne('select count(*) from `tiki_articles` where `topicId`=?', array($res['topicId']));
			$ret[] = $res;
		}

		return $ret;
	}

	function list_active_topics()
	{
		$query = 'select * from `tiki_topics` where `active`=?';

		$result = $this->query($query, array('y'));

		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

// Article Type functions
	function add_type($type)
	{
		$result = $this->query('insert into `tiki_article_types`(`type`) values(?)', array($type));

		return true;
	}

	function edit_type( $type
										, $use_ratings
										, $show_pre_publ
										, $show_post_expire
										, $heading_only
										, $allow_comments
										, $comment_can_rate_article
										, $show_image
										, $show_avatar
										, $show_author
										, $show_pubdate
										, $show_expdate
										, $show_reads
										, $show_size
										, $show_topline
										, $show_subtitle
										, $show_linkto
										, $show_image_caption
										, $show_lang
										, $creator_edit
										)
	{
		if ($use_ratings == 'on') {
			$use_ratings = 'y';
		} else {
			$use_ratings = 'n';
		}
		
		if ($show_pre_publ == 'on') {
			$show_pre_publ = 'y';
		} else {
			$show_pre_publ = 'n';
		}
		
		if ($show_post_expire == 'on') {
			$show_post_expire = 'y';
		} else {
			$show_post_expire = 'n';
		}
		
		if ($heading_only == 'on') {
			$heading_only = 'y';
		} else {
			$heading_only = 'n';
		}
		
		if ($allow_comments == 'on') {
			$allow_comments = 'y';
		} else {
			$allow_comments = 'n';
		}
		
		if ($comment_can_rate_article == 'on') {
			$comment_can_rate_article = 'y';
		} else {
			$comment_can_rate_article = 'n';
		}
		
		if ($show_image == 'on') {
			$show_image = 'y';
		} else {
			$show_image = 'n';
		}
		
		if ($show_avatar == 'on') {
			$show_avatar = 'y';
		} else {
			$show_avatar = 'n';
		}
		
		if ($show_author == 'on') {
			$show_author = 'y';
		} else {
			$show_author = 'n';
		}
		
		if ($show_pubdate == 'on') {
			$show_pubdate = 'y';
		} else {
			$show_pubdate = 'n';
		}
		
		if ($show_expdate == 'on') {
			$show_expdate = 'y';
		} else {
			$show_expdate = 'n';
		}
		
		if ($show_reads == 'on') {
			$show_reads = 'y';
		} else {
			$show_reads = 'n';
		}
		
		if ($show_size == 'on') {
			$show_size = 'y';
		} else {
			$show_size = 'n';
		}
		
		if ($show_topline == 'on') {
			$show_topline = 'y';
		} else {
			$show_topline = 'n';
		}
		if ($show_subtitle == 'on')
		{
			$show_subtitle = 'y';
		} else {
			$show_subtitle = 'n';
		}
		
		if ($show_linkto == 'on') {
			$show_linkto = 'y';
		} else {
			$show_linkto = 'n';
		}
		
		if ($show_image_caption == 'on') {
			$show_image_caption = 'y';
		} else {
			$show_image_caption = 'n';
		}
		
		if ($show_lang == 'on') {
			$show_lang = 'y';
		} else {
			$show_lang = 'n';
		}
		
		if ($creator_edit == 'on') {
			$creator_edit = 'y';
		} else {
			$creator_edit = 'n';
		}
		$query = "update `tiki_article_types` set
			`use_ratings` = ?,
			`show_pre_publ` = ?,
			`show_post_expire` = ?,
			`heading_only` = ?,
			`allow_comments` = ?,
			`comment_can_rate_article` = ?,
			`show_image` = ?,
			`show_avatar` = ?,
			`show_author` = ?,
			`show_pubdate` = ?,
			`show_expdate` = ?,
			`show_reads` = ?,
			`show_size` = ?,
			`show_topline` = ?,
			`show_subtitle` = ?,
			`show_linkto` = ?,
			`show_image_caption` = ?,
			`show_lang` = ?,
			`creator_edit` = ?
			where `type` = ?";
	
		$result = $this->query($query, array( $use_ratings
																				, $show_pre_publ
																				, $show_post_expire
																				, $heading_only
																				, $allow_comments
																				, $comment_can_rate_article
																				, $show_image
																				, $show_avatar
																				, $show_author
																				, $show_pubdate
																				, $show_expdate
																				, $show_reads
																				, $show_size
																				, $show_topline
																				, $show_subtitle
																				, $show_linkto
																				, $show_image_caption
																				, $show_lang
																				, $creator_edit
																				, $type
																			)
																		);
	}

	function remove_type($type)
	{
		$query = 'delete from `tiki_article_types` where `type`=?';
		$result = $this->query($query, array($type));
		// remove attributes set for this type too
		$query = "delete from `tiki_object_relations` where `source_type` = 'articletype' and `source_itemId`=?";
		$result = $this->query($query, array($type));
	}

	function get_type($type)
	{
		$query = 'select * from `tiki_article_types` where `type`=?';

		$result = $this->query($query, array($type));

		$res = $result->fetchRow();
		return $res;
	}

	function list_types()
	{
		$query = 'select * from `tiki_article_types`';
		$result = $this->query($query, array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['article_cnt'] = $this->getOne('select count(*) from `tiki_articles` where `type` = ?', array($res['type']));
			$ret[] = $res;
		}

		return $ret;
	}

	function list_types_byname() {
		$query = "select * from `tiki_article_types` order by `type` asc";
		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[$res['type']] = $res;
		}

		return $ret;
	}

	function get_user_articles($user, $max)
	{
		$query = 'select `articleId` ,`title` from `tiki_articles` where `author`=? order by `publishDate` desc';
	
		$result = $this->query($query, array($user), $max);
		$ret = array();
	
		while ($res = $result->fetchRow()) {
			if ($this->user_has_perm_on_object($user, $res['articleId'], 'article', 'tiki_p_read_article')) {
				$ret[] = $res;
			}
		}
	
		return $ret;
	}
	
	function import_csv($fileName, &$msgs, $csvDelimiter = ',')
	{
		global $user, $prefs, $tikilib;
		$fhandle = fopen($fileName, 'r');
		if (($fds = fgetcsv($fhandle, 4096, $csvDelimiter)) === false || empty($fds[0])) {
			$msgs[] = tra('The file is not a CSV file or has not a correct syntax');
			return false;
		}
		for ($i = 0, $icount_fds = count($fds); $i < $icount_fds; $i++) {
			$fields[trim($fds[$i])] = $i;
		}
		if (!isset($fields['title']))					$fields['title']				= $i++;
		if (!isset($fields['authorName']))		$fields['authorName']		= $i++;
		if (!isset($fields['topicId']))				$fields['topicId']			= $i++;
		if (!isset($fields['useImage']))			$fields['useImage']			= $i++;
		if (!isset($fields['imgname']))				$fields['imgname']			= $i++;
		if (!isset($fields['imgsize']))				$fields['imgsize']			= $i++;
		if (!isset($fields['imgtype']))				$fields['imgtype']			= $i++;
		if (!isset($fields['imgdata']))				$fields['imgdata']			= $i++;
		if (!isset($fields['heading']))				$fields['heading']			= $i++;
		if (!isset($fields['body']))					$fields['body']					= $i++;
		if (!isset($fields['publishDate']))		$fields['publishDate']	= $i++;
		if (!isset($fields['expireDate']))		$fields['expireDate']		= $i++;
		if (!isset($fields['user']))					$fields['user']					= $i++;
		if (!isset($fields['image_x']))				$fields['image_x']			= $i++;
		if (!isset($fields['image_y']))				$fields['image_y']			= $i++;
		if (!isset($fields['type']))					$fields['type']					= $i++;
		if (!isset($fields['topline']))				$fields['topline']			= $i++;
		if (!isset($fields['subtitle']))			$fields['subtitle']			= $i++;
		if (!isset($fields['linkto']))				$fields['linkto']				= $i++;
		if (!isset($fields['image_caption'])) $fields['image_caption']= $i++;
		if (!isset($fields['lang']))					$fields['lang']					= $i++;
		if (!isset($fields['rating']))				$fields['rating']				= $i++;
		if (!isset($fields['isfloat']))				$fields['isfloat']			= $i++;
		if (!isset($fields['emails']))				$fields['emails']				= $i++;
		$line = 1;
		while (($data = fgetcsv($fhandle, 4096, $csvDelimiter)) !== false) {
			++$line;
			if (!isset($data[$fields['title']]))				$data[$fields['title']]					= '';
			if (!isset($data[$fields['authorName']]))		$data[$fields['authorName']]		= '';
			if (!isset($data[$fields['topicId']]))			$data[$fields['topicId']]				= 0;
			if (!isset($data[$fields['useImage']]))			$data[$fields['useImage']]			= 'n';
			if (!isset($data[$fields['imgname']]))			$data[$fields['imgname']]				= '';
			if (!isset($data[$fields['imgsize']]))			$data[$fields['imgsize']]				= '';
			if (!isset($data[$fields['imgtype']]))			$data[$fields['imgtype']]				= '';
			if (!isset($data[$fields['imgdata']]))			$data[$fields['imgdata']]				= '';
			if (!isset($data[$fields['heading']]))			$data[$fields['heading']]				= '';
			if (!isset($data[$fields['body']]))					$data[$fields['body']]					= '';
			if (!isset($data[$fields['publishDate']]))	$data[$fields['publishDate']]		= $tikilib->now;
			if (!isset($data[$fields['expireDate']]))		$data[$fields['expireDate']]		= $tikilib->now + 365*24*60*60;
			if (!isset($data[$fields['user']]))					$data[$fields['user']]					= $user;
			if (!isset($data[$fields['image_x']]))			$data[$fields['image_x']]				= 0;
			if (!isset($data[$fields['image_y']]))			$data[$fields['image_y']]				= 0;
			if (!isset($data[$fields['type']]))					$data[$fields['type']]					= 'Article';
			if (!isset($data[$fields['topline']]))			$data[$fields['topline']]				= '';
			if (!isset($data[$fields['subtitle']]))			$data[$fields['subtitle']]			= '';
			if (!isset($data[$fields['linkto']]))				$data[$fields['linkto']]				= '';
			if (!isset($data[$fields['image_caption']]))$data[$fields['image_caption']] = '';
			if (!isset($data[$fields['lang']]))					$data[$fields['lang']]					= $prefs['language'];
			if (!isset($data[$fields['rating']]))				$data[$fields['rating']]				= 7;
			if (!isset($data[$fields['isfloat']]))			$data[$fields['isfloat']]				= 'n';
			if (!isset($data[$fields['emails']]))				$data[$fields['emails']]				= '';

			$articleId = $this->replace_article($data[$fields['title']]
																				, $data[$fields['authorName']]
																				, $data[$fields['topicId']]
																				, $data[$fields['useImage']]
																				, $data[$fields['imgname']]
																				, $data[$fields['imgsize']]
																				, $data[$fields['imgtype']]
																				, $data[$fields['imgdata']]
																				, $data[$fields['heading']]
																				, $data[$fields['body']]
																				, $data[$fields['publishDate']]
																				, $data[$fields['expireDate']]
																				, $data[$fields['user']]
																				, 0
																				, $data[$fields['image_x']]
																				, $data[$fields['image_y']]
																				, $data[$fields['type']]
																				, $data[$fields['topline']]
																				, $data[$fields['subtitle']]
																				, $data[$fields['linkto']]
																				, $data[$fields['image_caption']]
																				, $data[$fields['lang']]
																				, $data[$fields['rating']]
																				, $data[$fields['isfloat']]
																				, $data[$fields['emails']]
																			);
			if (empty($articleId)) {
				$msgs[] = sprintf(tra('Error line: %d'), $line);
			}
		}
		return true;
	}

	function delete_image_cache($image_type, $imageId)
	{
		global $prefs, $tikidomain;
		// Input validation: imageId must be a number, and not 0
		if (!ctype_digit("$imageId") || !($imageId>0)) {
			return false;
		}
		switch ($image_type) {
			case 'article':
				$image_cache_prefix = 'article';
							break;
			case 'submission':
				$image_cache_prefix = 'article_submission';
							break;
			case 'preview':
				$image_cache_prefix = 'article_preview';
							break;
			default:
				return false;
		}
		$article_image_cache = $prefs['tmpDir'];
		if ($tikidomain) { 
			$article_image_cache .= "/$tikidomain"; 
		}
		$article_image_cache .= "/$image_cache_prefix.".$imageId;
		if ( @unlink($article_image_cache) ) {
			return true;
		} else {
			return false;
		}
	}

	function get_title($articleId)
	{
		$query = 'select `title` from `tiki_articles` where `articleId`=?';
		return $this->getOne($query, array((int)$articleId));
	}

	function fetchtopicId($topic)
	{
		$topicId = '';
		$query = 'select `topicId` from `tiki_topics` where `name` = ?';
		$topicId = $this->getOne($query, array($topic) );
		return $topicId;
	}
	
	function get_most_recent_article_id() {
		$maxRecords = 1;
		$sort_mode = 'publishDate_desc';
		$date_min = 0;
		$date_max = $this->now;
		$query = 'SELECT `tiki_articles`.`articleId` FROM `tiki_articles` INNER JOIN `tiki_article_types` on `tiki_articles`.`type` = `tiki_article_types`.`type` '.
				 'WHERE `tiki_articles`.`publishDate`>=\'0\' AND (`tiki_articles`.`publishDate`<=? OR `tiki_article_types`.`show_pre_publ`=\'y\') AND '.
				 '(`tiki_articles`.`expireDate`>? OR `tiki_article_types`.`show_post_expire`=\'y\') AND `tiki_articles`.`ispublished`=\'y\' '.
				 'ORDER BY `publishDate` DESC';
		$bindvars = array( $date_max, $date_max );
		$id = $this->getOne($query, $bindvars);
		return $id;
	}

	function list_articles( $offset = 0
												, $maxRecords = -1
												, $sort_mode = 'publishDate_desc'
												, $find = ''
												, $date_min = 0
												, $date_max = 0
												, $user = false
												, $type = ''
												, $topicId = ''
												, $visible_only = 'y'
												, $topic = ''
												, $categId = ''
												, $creator = ''
												, $group = ''
												, $lang = ''
												, $min_rating = ''
												, $max_rating = ''
												, $override_dates = false
												, $ispublished = ''
												)
		{

		global $userlib, $user, $prefs;

		$mid = '';
		$bindvars = array();
		$fromSql = '';

		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = ' where (`title` like ? or `heading` like ? or `body` like ?) ';
			$bindvars = array($findesc, $findesc, $findesc);
		}

		// type=>[!]a+b+c+d+...
		if ($type) {
			$invert = '';
			$connect = ' or ';
			// parameter list negated?
			if (substr($type, 0, 1) == '!') {
				$type = substr($type, 1);
				$invert = '!';
				$connect = ' and ';
			}
			$add = '';
			$rest = explode ('+', $type);
			foreach ($rest as $type) {
				if ($add == '') {
					if ($mid) {
						$mid .= ' and ';
					} else {
						$mid = ' where ';
					}
				} else {
					$add .= $connect;
				}
				$add .= " `tiki_articles`.`type`$invert=? ";
				$bindvars[] = $type;
			}
			if ($add <> '') {
				$mid .= ' ( ' . $add . ' ) ';
			}
		}

		// topicId=>[!]a+b+c+d+...
		if (($topicId) || ($topicId == '0')) {
			$invert = '';
			$connect = ' or ';
			// parameter list negated?
			if (substr($topicId, 0, 1) == '!') {
				$topicId = substr($topicId, 1);
				$invert = '!';
				$connect = ' and ';
			}
			$add = '';
			$rest = explode ('+', $topicId);
			foreach ($rest as $topicId) {
				if ($add == '') {
					if ($mid) {
						$mid .= ' and ';
					} else {
						$mid = ' where ';
					}
				} else {
					$add .= $connect;
				}
				$add .= " `tiki_articles`.`topicId`$invert=? ";
				$bindvars[] = $topicId;
			}
			if ($add <> '') {
				$mid .= ' ( ' . $add . ' ) ';
			}
		}

		// topic=>[!]a+b+c+d+...
		if ($topic) {
			$invert = '';
			// parameter list negated?
			if (substr($topic, 0, 1) == '!') {
				$topic = substr($topic, 1);
				$invert = '!';
			}
			$rest = explode('\+', $topic);
			
			if ($mid) {
				$mid .= ' and ';
			} else {
				$mid = ' where ';
			}
			$add = $this->in('tiki_articles.topicName', $rest, $bindvars);
			if ($add <> '') {
				$add = ($invert ? ' NOT' : '') . ' ( ' . $add . ' ) ';
				if ($invert)
					$add = 'COALESCE(' . $add . ', TRUE)';
				$mid .= $add;
			}
		}
		if (($visible_only) && ($visible_only <> 'n')) {
			if ( $date_max <= 0 ) {
				// show articles published today
				$date_max = $this->now;
			}
			$bindvars[] = (int)$date_min;
			$bindvars[] = (int)$date_max;
			if ($override_dates) {
				$condition = "`tiki_articles`.`publishDate`>=? and `tiki_articles`.`publishDate`<=?";
			} else {
				$bindvars[] = (int)$this->now;
				$condition = "`tiki_articles`.`publishDate`>=? and (`tiki_articles`.`publishDate`<=? or `tiki_article_types`.`show_pre_publ`='y')"
										. " and (`tiki_articles`.`expireDate`>? or `tiki_article_types`.`show_post_expire`='y')"
										;
			}
			$mid .= ( $mid ? ' and ' : ' where ' ) . $condition;
		}
		if (!empty($lang)) {
			$condition = '`tiki_articles`.`lang`=?';
			$mid .= ($mid)? ' and ': ' where ';
			$mid .= $condition.' ';
			$bindvars[] = $lang;
		}
		if (!empty($ispublished)) {
			$condition = '`tiki_articles`.`ispublished`=?';
			$mid .= ($mid)? ' and ': ' where ';
			$mid .= $condition.' ';
			$bindvars[] = $ispublished;
		}
		if ($mid)
			$mid2 = ' and 1 = 1 ';
		else
			$mid2 = ' where 1 = 1 ';

		if ($creator!=''){
			$mid2 .= ' and `tiki_articles`.`author` like ? ';
			$bindvars[] = "%$creator%";
		}

		if ($min_rating || $max_rating) {
			$min_rating = isset($min_rating) ? $min_rating : '0.0';
			$max_rating = isset($max_rating) ? $max_rating : '10.0';
			$mid2 .= ' and (`tiki_articles`.`rating` >= ? and `tiki_articles`.`rating` <= ? )';
			$bindvars[] = $min_rating;
			$bindvars[] = $max_rating;
		}

		global $categlib; require_once('lib/categories/categlib.php');
		if ( $categId ) {
			$jail = $categId;
		} else {
			$jail = $categlib->get_jail();
		}
		if ($jail) {
			$categlib->getSqlJoin($jail, 'article', '`tiki_articles`.`articleId`', $fromSql, $mid2, $bindvars);
		}

		if ( $prefs['rating_advanced'] == 'y' ) {
			global $ratinglib; require_once 'lib/rating/ratinglib.php';
			$fromSql .= $ratinglib->convert_rating_sort($sort_mode, 'article', '`articleId`');
		}

		$fromSql .= ' inner join `tiki_article_types` on `tiki_articles`.`type` = `tiki_article_types`.`type` ';
		
		$query = "select distinct `tiki_articles`.*,
			`tiki_article_types`.`use_ratings`,
			`tiki_article_types`.`show_pre_publ`,
			`tiki_article_types`.`show_post_expire`,
			`tiki_article_types`.`heading_only`,
			`tiki_article_types`.`allow_comments`,
			`tiki_article_types`.`show_image`,
			`tiki_article_types`.`show_avatar`,
			`tiki_article_types`.`show_author`,
			`tiki_article_types`.`show_pubdate`,
			`tiki_article_types`.`show_expdate`,
			`tiki_article_types`.`show_reads`,
			`tiki_article_types`.`show_size`,
			`tiki_article_types`.`show_topline`,
			`tiki_article_types`.`show_subtitle`,
			`tiki_article_types`.`show_linkto`,
			`tiki_article_types`.`show_image_caption`,
			`tiki_article_types`.`show_lang`,
			`tiki_article_types`.`creator_edit`
				from `tiki_articles`
				$fromSql
				$mid $mid2 order by " . $this->convertSortMode($sort_mode);

		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$query_cant = "select distinct count(*) from `tiki_articles` $fromSql $mid $mid2";
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			if ($res['topicId'] != 0 && $userlib->object_has_one_permission($res['topicId'], 'topic')) {// if no topic or if topic has no special perm don't have to check for topic perm
				$add1 = $this->user_has_perm_on_object($user, $res['topicId'],'topic','tiki_p_topic_read');
			} else {
				$add1 = $this->user_has_perm_on_object($user, $res['articleId'],'article', 'tiki_p_read_article');
			}
			$add2 = $this->user_has_perm_on_object($user, $res['articleId'], 'article', 'tiki_p_articles_read_heading');
			// no need to do all of the following if we are not adding this article to the array
			if ($add1 || $add2) {
				$res['entrating'] = floor($res['rating']);
				if (empty($res['body'])) {
					$res['isEmpty'] = 'y';
				} else {
					$res['isEmpty'] = 'n';
				}
				if (strlen($res['image_data']) > 0) {
					$res['hasImage'] = 'y';
				} else {
					$res['hasImage'] = 'n';
				}
				$res['count_comments'] = 0;

				// Determine if the article would be displayed in the view page
				$res['disp_article'] = 'y';
				if (($res['show_pre_publ'] != 'y') and ($this->now < $res['publishDate']) && !$override_dates) {
					$res['disp_article'] = 'n';
				}
				if (($res['show_post_expire'] != 'y') and ($this->now > $res['expireDate']) && !$override_dates) {
					$res['disp_article'] = 'n';
				}
				$ret[] = $res;
			}
		}
		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function list_submissions($offset = 0, $maxRecords = -1, $sort_mode = 'publishDate_desc', $find = '', $date = '')
	{
		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');
			$mid = " where (`title` like ? or `heading` like ? or `body` like ?) ";
			$bindvars = array($findesc, $findesc, $findesc);
		} else {
			$mid = '';
			$bindvars = array();
		}

		if ($date) {
			if ($mid) {
				$mid .= ' and `publishDate` <= ? ';
			} else {
				$mid = ' where `publishDate` <= ? ';
			}
			$bindvars[] = $date;
		}

		$query = "select * from `tiki_submissions` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_submissions` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['entrating'] = floor($res['rating']);

			if (empty($res['body'])) {
				$res['isEmpty'] = 'y';
			} else {
				$res['isEmpty'] = 'n';
			}

			if (strlen($res['image_data']) > 0) {
				$res['hasImage'] = 'y';
			} else {
				$res['hasImage'] = 'n';
			}

			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function get_article($articleId, $checkPerms = true)
	{
		global $user, $tiki_p_admin_cms, $prefs, $userlib;
		$mid = ' where `tiki_articles`.`type` = `tiki_article_types`.`type` ';
		$query = "select `tiki_articles`.*,
								`users_users`.`avatarLibName`,
								`tiki_article_types`.`use_ratings`,
								`tiki_article_types`.`show_pre_publ`,
								`tiki_article_types`.`show_post_expire`,
								`tiki_article_types`.`heading_only`,
								`tiki_article_types`.`allow_comments`,
								`tiki_article_types`.`comment_can_rate_article`,
								`tiki_article_types`.`show_image`,
								`tiki_article_types`.`show_avatar`,
								`tiki_article_types`.`show_author`,
								`tiki_article_types`.`show_pubdate`,
								`tiki_article_types`.`show_expdate`,
								`tiki_article_types`.`show_reads`,
								`tiki_article_types`.`show_size`,
								`tiki_article_types`.`show_topline`,
								`tiki_article_types`.`show_subtitle`,
								`tiki_article_types`.`show_linkto`,
								`tiki_article_types`.`show_image_caption`,
								`tiki_article_types`.`show_lang`,
								`tiki_article_types`.`creator_edit`
						from (`tiki_articles`, `tiki_article_types`) 
						left join `users_users` on `tiki_articles`.`author` = `users_users`.`login` $mid and `tiki_articles`.`articleId`=?"
						;
		
		$result = $this->query($query, array((int)$articleId));
		if ($result->numRows()) {
			$res = $result->fetchRow();
			$res['entrating'] = floor($res['rating']);
		} else {
			return '';
		}
		if ( $checkPerms ) {
			$perms = Perms::get( 'article', $articleId );

			$permsok = $perms->admin_cms || $perms->read_article || $perms->articles_read_heading;

			// If not allowed to view article, check if allowed to view topic
			$permsok = $permsok || ( $res['topicId'] && Perms::get( 'topic', $res['topicId'] )->read_topic );

			if ( ! $permsok ) {
				return false;
			}
		}

		if ($prefs['feature_score'] == 'y') {
			$this->score_event($user, 'article_read', $articleId);
			$this->score_event($res['author'], 'article_is_read', $articleId . '_' . $user);
		}

		return $res;
	}

	function get_submission($subId)
	{
		$query = 'select * from `tiki_submissions` where `subId`=?';
		$result = $this->query($query, array((int) $subId));
		if ($result->numRows()) {
			$res = $result->fetchRow();
			$res['entrating'] = floor($res['rating']);
		} else {
			return false;
		}
		return $res;
	}

	function get_topic_image($topicId)
	{
		$query = 'select `image_name` ,`image_size`,`image_type`, `image_data` from `tiki_topics` where `topicId`=?';
		$result = $this->query($query, array((int) $topicId));
		$res = $result->fetchRow();
		return $res;
	}

	function get_article_image($id)
	{
		$query = 'select `image_name` ,`image_size`,`image_type`, `image_data` from `tiki_articles` where `articleId`=?';
		$result = $this->query($query, array((int) $id));
		$res = $result->fetchRow();
		return $res;
	}
	
	function add_article_type_attribute($artType, $attributeName)
	{
		global $relationlib, $attributelib;
		if (!is_object($relationlib)) {
			include_once('lib/attributes/relationlib.php');
		}
		if (!is_object($attributelib)) {
			include_once('lib/attributes/attributelib.php');
		}
		$fullAttributeName = TikiFilter::get( 'attribute_type' )->filter( trim('tiki.article.' . $attributeName) );
		$relationId = $relationlib->add_relation( 'tiki.article.attribute', 'articletype', $artType, 'attribute', $fullAttributeName);
		if (!$relationId) {
			return 0;
		} else {
			$attributelib->set_attribute( 'relation', $relationId, 'tiki.relation.target', $attributeName );
			return $relationId;
		}
	}
	
	function delete_article_type_attribute($artType, $relationId)
	{
		global $relationlib;
		if (!is_object($relationlib)) {
			include_once('lib/attributes/relationlib.php');
		}
		// double check relation is associated with article type before deleting
		$currentAttributes = $relationlib->get_relations_from( 'articletype', $artType, 'tiki.article.attribute' );
		foreach ($currentAttributes as $att) {
			if ($att['relationId'] == $relationId) {
				$relationlib->remove_relation($att['relationId']);
			}
		}
		return true;
	}
	
	function get_article_type_attributes($artType)
	{
		global $relationlib, $attributelib;
		
		if (!is_object($relationlib)) {
			include_once('lib/attributes/relationlib.php');
		}
		if (!is_object($attributelib)) {
			include_once('lib/attributes/attributelib.php');
		}

		$attributes = $relationlib->get_relations_from( 'articletype', $artType, 'tiki.article.attribute' );
		$ret = array();
		foreach ($attributes as $att) {
			$relationAtt = $attributelib->get_attributes( 'relation', $att['relationId']);
			if (isset($relationAtt['tiki.relation.target'])) {
				$ret[$relationAtt['tiki.relation.target']] = $att;
			}
		}
		return $ret;
	}
	
	function set_article_attributes($articleId, $attributeArray, $isSubmission = false)
	{
		// expects attributeArray in the form of $key=>$val where $key is tiki.article.xxxx and $val is value
		global $attributelib;
		if (!is_object($attributelib)) {
			include_once('lib/attributes/attributelib.php');
		}
		if ($isSubmission) {
			$type = 'submission';
		} else {
			$type = 'article';
		}
		$currentAtt = $this->get_article_attributes($articleId);
		foreach ($attributeArray as $name => $value) {
				if ( !in_array($name, array_keys($currentAtt)) || $value != $currentAtt[$name]['value'] ) {
					$attributelib->set_attribute( $type, $articleId, $name, $value ); 						
				}
		}
		return true;
	}
	
	function get_article_attributes($articleId, $isSubmission = false)
	{
		global $attributelib;
		if (!is_object($attributelib)) {
			include_once('lib/attributes/attributelib.php');
		}

		if ($isSubmission) {
			$type = 'submission';
		} else {
			$type = 'article';
		}

		$allAttributes = $attributelib->get_attributes( $type, $articleId );
		$ret = array();
		foreach ($allAttributes as $k => $att) {
			if (substr($k, 0, 13) == 'tiki.article.') {
				$ret[$k] = $att;
			}
		}
		return $ret;
	}
	
	function transfer_attributes_from_submission($subId, $articleId)
	{
		$this->query( 'UPDATE `tiki_object_attributes` set `type` = ?, `itemId` = ? where `type` = ? and `itemId` = ?',
		
		array( 'article', $articleId, 'submission', $subId ) );
	}
}

$artlib = new ArtLib;
