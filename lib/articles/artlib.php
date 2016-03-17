<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

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

		$articleId = $this->replace_article(
			$data['title'],
			$data['authorName'],
			$data['topicId'],
			$data['useImage'],
			$data['image_name'],
			$data['image_size'],
			$data['image_type'],
			$data['image_data'],
			$data['heading'],
			$data['body'],
			$data['publishDate'],
			$data['expireDate'],
			$data['author'],
			0,
			$data['image_x'],
			$data['image_y'],
			$data['type'],
			$data['topline'],
			$data['subtitle'],
			$data['linkto'],
			$data['image_caption'],
			$data['lang'],
			$data['rating'],
			$data['isfloat']
		);
		$this->transfer_attributes_from_submission($subId, $articleId);

		$query = "update `tiki_objects` set `type`= ?, `itemId`= ?, `href`=? where `itemId` = ? and `type`= ?";
		$this->query($query, array('article', (int)$articleId, "tiki-read_article.php?articleId=$articleId", (int)$subId, 'submission'));
		$query = 'update `tiki_objects` set `href`=?, `type`=? where `href`=?';
		$this->query($query, array("'tiki-read_article.php?articleId=$articleId", 'article', "tiki-edit_submission.php?subId=$subId"));

		$this->remove_submission($subId);
	}

	function add_article_hit($articleId)
	{
		global $prefs, $user;

		if (StatsLib::is_stats_hit()) {
			$query = "update `tiki_articles` set `nbreads`=`nbreads`+1 where `articleId`=?";

			$result = $this->query($query, array($articleId));
		}

		return true;
	}

	function remove_article($articleId, $article_data ='')
	{
		global $user, $prefs;
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');

		if ($articleId) {
			if (empty($article_data)) $article_data = $this->get_article($articleId);
			$query = 'delete from `tiki_articles` where `articleId`=?';

			$result = $this->query($query, array($articleId));
			$this->remove_object('article', $articleId);

			$multilinguallib = TikiLib::lib('multilingual');
			$multilinguallib->detachTranslation('article', $articleId);

			TikiLib::events()->trigger('tiki.article.delete',
				array(
					'type' => 'article',
					'object' => $articleId,
					'user' => $user,
				)
			);

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

			if ($prefs['user_article_watch_editor'] != "y") {
			for ($i = count($nots) - 1; $i >=0; --$i)
				if ($nots[$i]['user'] == $user) {
					unset($nots[$i]);
					break;
				}
			}

			if (!isset($_SERVER['SERVER_NAME'])) {
				$_SERVER['SERVER_NAME'] = $_SERVER["HTTP_HOST"];
			}

			if ($prefs['feature_user_watches'] == 'y' && $prefs['feature_daily_report_watches'] == 'y') {
				$reportsManager = Reports_Factory::build('Reports_Manager');
				$reportsManager->addToCache(
					$nots,
					array(
						'event'				=> 'article_deleted',
						'articleId'			=> $articleId,
						'articleTitle'		=> $article_data['title'],
						'authorName'		=> $article_data['authorName'],
						'user'				=> $user
					)
				);
			}

			if (count($nots) || (!empty($emails) && is_array($emails))) {
				include_once('lib/notifications/notificationemaillib.php');

				$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
				$smarty->assign('mail_title', 'articleId=' . $articleId);
				$smarty->assign('mail_postid', $articleId);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_current_data', $article_data['heading'] . "\n----------------------\n" . $article_data['body']);

				// the strings below are used to localize messages in the template file
				//get_strings tr('New article post:') tr('Edited article post:') tr('Deleted article post:')
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

	function delete_expired_submissions($maxrows = 1000)
	{
		$tiki_submissions = TikiDb::get()->table('tiki_submissions');

		$expired = $tiki_submissions->fetchColumn(
			'subId',
			array('expireDate' => $tiki_submissions->lesserThan($this->now)),
			$maxrows
		);

		$transaction = $this->begin();

		foreach ($expired as $subId) {

			$tiki_submissions->delete(array('subId' => $subId));

			$this->remove_object('submission', $subId);
		}

		$transaction->commit();


		return true;
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
		global $tiki_p_autoapprove_submission, $prefs;
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');

		if ($expireDate < $publishDate) {
			$expireDate = $publishDate;
		}

		if (empty($imgdata))
			$imgdata = '';

		$notificationlib = TikiLib::lib('notification');
		$hash = md5($title . $heading . $body);
		$query = 'select `name` from `tiki_topics` where `topicId` = ?';
		$topicName = $this->getOne($query, array((int) $topicId));
		$size = strlen($body);

		$info = array(
			'title' => $title,
			'authorName' => $authorName,
			'topicId' => (int) $topicId,
			'topicName' => $topicName,
			'size' => (int) $size,
			'useImage' => $useImage,
			'image_name' => $imgname,
			'image_type' => $imgtype,
			'image_size' => (int) $imgsize,
			'image_data' => $imgdata,
			'isfloat' => $isfloat,
			'image_x' => (int) $image_x,
			'image_y' => (int) $image_y,
			'heading' => $heading,
			'body' => $body,
			'publishDate' => (int) $publishDate,
			'expireDate' => (int) $expireDate,
			'created' => (int) $this->now,
			'author' => $user,
			'type' => $type,
			'rating' => (float) $rating,
			'topline' => $topline,
			'subtitle' => $subtitle,
			'linkto' => $linkto,
			'image_caption' => $image_caption,
			'lang' => $lang,
			//'ispublished' => $ispublished, // Does not exist
		);

		$article_table = $this->table('tiki_submissions');
		if ($subId) {
			$article_table->update($info, array(
				'subId' => (int) $subId,
			));
		} else {
			$id = $article_table->insert($info);
		}

		if ($tiki_p_autoapprove_submission != 'y') {
			$nots = $tikilib->get_event_watches('article_submitted', '*');
			$nots2 = $tikilib->get_event_watches('topic_article_created', $topicId);
			$nots3 = array();
			foreach ($nots as $n) {
			$nots3[] = $n['email'];
			}
			foreach ($nots2 as $n) {
				if (!in_array($n['emails'], $nots3))
					$nots[] = $n;
			}
			if (!isset($_SERVER['SERVER_NAME'])) {
				$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
			}

			if ($prefs['user_article_watch_editor'] != "y") {
			for ($i = count($nots) - 1; $i >=0; --$i)
				if ($nots[$i]['user'] == $user) {
					unset($nots[$i]);
					break;
				}
			}

			if (count($nots)) {
				include_once('lib/notifications/notificationemaillib.php');
				$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_title', $title);
				$smarty->assign('mail_heading', $heading);
				$smarty->assign('mail_body', $body);
				$smarty->assign('mail_subId', $id);
				sendEmailNotification($nots, 'watch', 'submission_notification_subject.tpl', $_SERVER['SERVER_NAME'], 'submission_notification.tpl');
			}
		}
		$tikilib = TikiLib::lib('tiki');
		$tikilib->object_post_save(
			array(
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
													, $list_image_y = ''
													, $ispublished='y'
													, $fromurl = false
												)
	{

		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');

		if ($expireDate < $publishDate) {
			$expireDate = $publishDate;
		}
		$hash = md5($title . $heading . $body);
		if (empty($imgdata) || $useImage === 'n') {	// remove image data if not using it
			$imgdata = '';
		}

		$query = 'select `name` from `tiki_topics` where `topicId` = ?';
		$topicName = $this->getOne($query, array($topicId));
		$size = $body ? mb_strlen($body) : mb_strlen($heading);

		$info = array(
			'title' => $title,
			'authorName' => $authorName,
			'topicId' => (int) $topicId,
			'topicName' => $topicName,
			'size' => (int) $size,
			'useImage' => $useImage,
			'image_name' => $imgname,
			'image_type' => $imgtype,
			'image_size' => (int) $imgsize,
			'image_data' => $imgdata,
			'isfloat' => $isfloat,
			'image_x' => (int) $image_x,
			'image_y' => (int) $image_y,
			'list_image_x' => (int) $list_image_x,
			'list_image_y' => (int) $list_image_y,
			'heading' => $heading,
			'body' => $body,
			'publishDate' => (int) $publishDate,
			'expireDate' => (int) $expireDate,
			'author' => $user,
			'type' => $type,
			'rating' => (float) $rating,
			'topline' => $topline,
			'subtitle' => $subtitle,
			'linkto' => $linkto,
			'image_caption' => $image_caption,
			'lang' => $lang,
			'ispublished' => $ispublished,
		);

		$article_table = $this->table('tiki_articles');
		if ($articleId) {
			$oldArticle = $this->get_article($articleId);
			$article_table->update($info, array(
				'articleId' => (int) $articleId,
			));
			// Clear article image cache because image may just have been changed
			$this->delete_image_cache('article', $articleId);

			$event = 'article_edited';
			$nots = $tikilib->get_event_watches('article_edited', '*');
			$nots2 = $tikilib->get_event_watches('topic_article_edited', $topicId);
			$smarty->assign('mail_action', 'Edit');
			$smarty->assign('mail_old_title', $oldArticle['title']);
			$smarty->assign('mail_old_publish_date', $oldArticle['publishDate']);
			$smarty->assign('mail_old_expiration_date', $oldArticle['expireDate']);
			$smarty->assign('mail_old_data', $oldArticle['heading'] . "\n----------------------\n" . $oldArticle['body']);

		} else {
			$info['created'] = (int) $this->now;

			$articleId = $article_table->insert($info);

			global $prefs;
			TikiLib::events()->trigger('tiki.article.create',
				array(
					'type' => 'article',
					'object' => $articleId,
					'user' => $user,
				)
			);
			$event = 'article_submitted';
			$nots = $tikilib->get_event_watches('article_submitted', '*');
			$nots2 = $tikilib->get_event_watches('topic_article_created', $topicId);
			$smarty->assign('mail_action', 'New');

			// Create tracker item as well if feature is enabled 
 			if (!$fromurl && $prefs['tracker_article_tracker'] == 'y' && $trackerId = $prefs['tracker_article_trackerId']) {
 				$trklib = TikiLib::lib('trk');
 				$definition = Tracker_Definition::get($trackerId);
 				if ($fieldId = $definition->getArticleField()) {
					$addit = array();
 					$addit[] = array(
 						'fieldId' => $fieldId,
 						'type' => 'articles',
 						'value' => $articleId,
 					);
 					$itemId = $trklib->replace_item($trackerId, 0, array('data' => $addit));
					TikiLib::lib('relation')->add_relation('tiki.article.attach', 'trackeritem', $itemId, 'article', $articleId);
				}
 			}
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
		global $prefs;

		if ($prefs['user_article_watch_editor'] != "y") {
			for ($i = count($nots) - 1; $i >=0; --$i)
				if ($nots[$i]['user'] == $user) {
					unset($nots[$i]);
					break;
				}
		}

		if (!isset($_SERVER['SERVER_NAME'])) {
			$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
		}

		if ($prefs['feature_user_watches'] == 'y' && $prefs['feature_daily_report_watches'] == 'y') {
			$reportsManager = Reports_Factory::build('Reports_Manager');
			$reportsManager->addToCache(
				$nots,
				array(
					'event' => $event,
					'articleId' => $articleId,
					'articleTitle' => $title,
					'authorName' => $authorName,
					'user' => $user
				)
			);
		}

		if (count($nots) || is_array($emails)) {
			include_once('lib/notifications/notificationemaillib.php');

			$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
			$smarty->assign('mail_title', $title);
			$smarty->assign('mail_postid', $articleId);
			$smarty->assign('mail_user', $user);
			$smarty->assign('mail_current_publish_date', $publishDate);
			$smarty->assign('mail_current_expiration_date', $expireDate);
			$smarty->assign('mail_current_data', $heading."\n----------------------\n" . $body);
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

		$tikilib = TikiLib::lib('tiki');
		$tikilib->object_post_save(
			array(
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
		} else {
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
		$result = $this->query($query, array($imagename, $imagetype, $imagesize, $imagedata, $topicId));

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
			$ret[$res['topicId']] = $res;
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
		if ($show_subtitle == 'on') {
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
			`creator_edit` = ?
			where `type` = ?";

		$result = $this->query(
			$query,
			array(
				$use_ratings,
				$show_pre_publ,
				$show_post_expire,
				$heading_only,
				$allow_comments,
				$comment_can_rate_article,
				$show_image,
				$show_avatar,
				$show_author,
				$show_pubdate,
				$show_expdate,
				$show_reads,
				$show_size,
				$show_topline,
				$show_subtitle,
				$show_linkto,
				$show_image_caption,
				$creator_edit,
				$type
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

	function list_types_byname()
	{
		$query = "select * from `tiki_article_types` order by `type` asc";
		$result = $this->query($query, array());
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
			$msgs[] = tra('The file has incorrect syntax or is not a CSV file');
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

			$articleId = $this->replace_article(
				$data[$fields['title']],
				$data[$fields['authorName']],
				$data[$fields['topicId']],
				$data[$fields['useImage']],
				$data[$fields['imgname']],
				$data[$fields['imgsize']],
				$data[$fields['imgtype']],
				$data[$fields['imgdata']],
				$data[$fields['heading']],
				$data[$fields['body']],
				$data[$fields['publishDate']],
				$data[$fields['expireDate']],
				$data[$fields['user']],
				0,
				$data[$fields['image_x']],
				$data[$fields['image_y']],
				$data[$fields['type']],
				$data[$fields['topline']],
				$data[$fields['subtitle']],
				$data[$fields['linkto']],
				$data[$fields['image_caption']],
				$data[$fields['lang']],
				$data[$fields['rating']],
				$data[$fields['isfloat']],
				$data[$fields['emails']]
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
		$topicId = $this->getOne($query, array($topic));
		return $topicId;
	}

	function get_most_recent_article_id()
	{
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
												, $filter = ''
												)
	{

		global $user, $prefs;
		$userlib = TikiLib::lib('user');

		$mid = $join = '';
		$bindvars = array();
		$fromSql = '';

		if (!empty($filter)) {
			foreach ($filter as $typeF=>$val) {
				if ($typeF == 'translationOrphan') {
					$multilinguallib = TikiLib::lib('multilingual');
					$multilinguallib->sqlTranslationOrphan('article', '`tiki_articles`', 'articleId', $val, $join, $mid, $bindvars);
					$mid = ' where '.$mid;
				}
				if ($typeF == 'articleId' || $typeF == 'notArticleId') {
					$mid .= empty($mid)? ' where ': ' and ';
					$mid .= '`articleId` '.($typeF =='notArticleId'?'not in ':'in').' ('.implode(',', array_fill(0, count($val), '?')).')';
					$bindvars = array_merge($bindvars, $val);
				}
			}
		}

		if ($find) {
			$findesc = '%' . $find . '%';
			$mid .= empty($mid)? ' where ': ' and ';
			$mid .= " (`title` like ? or `heading` like ? or `body` like ? or `author` like ? or `authorName` like ?) ";
			$bindvars = array($findesc, $findesc, $findesc, $findesc, $findesc);
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
			$rest = explode('+', $type);
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
			$rest = explode('+', $topicId);
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

		if ($creator!='') {
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

		$categlib = TikiLib::lib('categ');
		if ( $categId ) {
			$jail = $categId;
		} else {
			$jail = $categlib->get_jail();
		}
		if ($jail) {
			$categlib->getSqlJoin($jail, 'article', '`tiki_articles`.`articleId`', $fromSql, $mid2, $bindvars);
		}

		if (empty($sort_mode)) {
			$sort_mode = 'publishDate_desc';
		}

		if ( $prefs['rating_advanced'] == 'y' ) {
			$ratinglib = TikiLib::lib('rating');
			$fromSql .= $ratinglib->convert_rating_sort($sort_mode, 'article', '`articleId`');
		}

		$fromSql .= ' inner join `tiki_article_types` on `tiki_articles`.`type` = `tiki_article_types`.`type` ';

		$query = "select distinct `tiki_articles`.*,
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
			`tiki_article_types`.`creator_edit`
			from `tiki_articles`
			$fromSql
			$join
			$mid $mid2 order by " .
			$this->convertSortMode(
				$sort_mode,
				array(
					'title',
					'state',
					'authorName',
					'topicId',
					'topicName',
					'publishDate',
					'expireDate',
					'created',
					'author',
					'rating',
					'nbreads',
				)
			);

		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$query_cant = "select distinct count(*) from `tiki_articles` $fromSql $join $mid $mid2";
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			if ($res['topicId'] != 0 && $userlib->object_has_one_permission($res['topicId'], 'topic')) {// if no topic or if topic has no special perm don't have to check for topic perm
				$add1 = $this->user_has_perm_on_object($user, $res['topicId'], 'topic', 'tiki_p_topic_read');
			} else {
				$add1 = $this->user_has_perm_on_object($user, $res['articleId'], 'article', 'tiki_p_read_article');
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

	/**
	 * Work out if body (or heading) should be parsed as html or not
	 * Currently (tiki 11) tries the prefs but also checks for html in body in case wysiwyg_htmltowiki wasn't enabled previously
	 *
	 * @param array $article of article data
	 * @param bool $check_heading	use heading or (default) body
	 * @return bool
	 */
	function is_html($article, $check_heading = false)
	{
		global $prefs;

		$text = $check_heading ? $article['heading'] : $article['body'];

		return ($prefs['feature_wysiwyg'] === 'y' || $prefs['mobile_mode'] === 'y') &&	// this now assumes that if in mobile mode any html in articles should not be encoded
				$prefs['wysiwyg_htmltowiki'] !== 'y' ||
						preg_match('/(<\/p>|<\/span>|<\/div>|<\/?br>)/', $text);
	}

	function list_submissions($offset = 0, $maxRecords = -1, $sort_mode = 'publishDate_desc', $find = '', $date = '', $type = '', $topicId = '', $lang = '')
	{
		if ($find) {
			$findPattern = '%' . $find . '%';
			$mid = " where (`title` like ? or `heading` like ? or `body` like ? or `author` like ? or `authorName` like ?) ";
			$bindvars = array($findPattern, $findPattern, $findPattern, $findPattern, $findPattern);
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

		if ($type) {
			$mid .= $mid ? ' AND ' : ' WHERE ';
			$mid .= ' `type` = ? ';
			$bindvars[] = $type;
		}

		if ($topicId) {
			$mid .= $mid ? ' AND ' : ' WHERE ';
			$mid .= ' `topicId` = ? ';
			$bindvars[] = $topicId;
		}

		if ($lang) {
			$mid .= $mid ? ' AND ' : ' WHERE ';
			$mid .= ' `lang` = ? ';
			$bindvars[] = $lang;
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
		global $user, $prefs;
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
								`tiki_article_types`.`creator_edit`
						from `tiki_articles`
						left join `tiki_article_types` ON `tiki_articles`.`type` = `tiki_article_types`.`type`
						left join `users_users` on `tiki_articles`.`author` = `users_users`.`login` 
						where `tiki_articles`.`articleId`=?"
						;

		$result = $this->query($query, array((int)$articleId));
		if ($result->numRows()) {
			$res = $result->fetchRow();
			$res['entrating'] = floor($res['rating']);
		} else {
			return '';
		}
		if ( $checkPerms ) {
			$perms = Perms::get('article', $articleId);

			$permsok = $perms->admin_cms || $perms->read_article || $perms->articles_read_heading;

			// If not allowed to view article, check if allowed to view topic
			$permsok = $permsok || ( $res['topicId'] && Perms::get('topic', $res['topicId'])->read_topic );

			if ( ! $permsok ) {
				return false;
			}
		}

		if ($res['author'] != $user) {
			TikiLib::events()->trigger('tiki.article.view',
				array(
					'type' => 'article',
					'object' => $articleId,
					'user' => $user,
					'author' => $res['author'],
				)
			);
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
		$relationlib = TikiLib::lib('relation');
		$attributelib = TikiLib::lib('attribute');

		$fullAttributeName = TikiFilter::get('attribute_type')->filter(trim('tiki.article.' . $attributeName));
		$relationId = $relationlib->add_relation('tiki.article.attribute', 'articletype', $artType, 'attribute', $fullAttributeName);
		if (!$relationId) {
			return 0;
		} else {
			$attributelib->set_attribute('relation', $relationId, 'tiki.relation.target', $attributeName);
			return $relationId;
		}
	}

	function delete_article_type_attribute($artType, $relationId)
	{
		$relationlib = TikiLib::lib('relation');
		// double check relation is associated with article type before deleting
		$currentAttributes = $relationlib->get_relations_from('articletype', $artType, 'tiki.article.attribute');
		foreach ($currentAttributes as $att) {
			if ($att['relationId'] == $relationId) {
				$relationlib->remove_relation($att['relationId']);
			}
		}
		return true;
	}

	function get_article_type_attributes($artType, $orderby = '')
	{
		$relationlib = TikiLib::lib('relation');
		$attributelib = TikiLib::lib('attribute');

		$attributes = $relationlib->get_relations_from('articletype', $artType, 'tiki.article.attribute', $orderby);
		$ret = array();
		foreach ($attributes as $att) {
			$relationAtt = $attributelib->get_attributes('relation', $att['relationId']);
			if (isset($relationAtt['tiki.relation.target'])) {
				$ret[$relationAtt['tiki.relation.target']] = $att;
			}
		}
		return $ret;
	}

	function set_article_attributes($articleId, $attributeArray, $isSubmission = false)
	{
		// expects attributeArray in the form of $key=>$val where $key is tiki.article.xxxx and $val is value
		$attributelib = TikiLib::lib('attribute');
		if ($isSubmission) {
			$type = 'submission';
		} else {
			$type = 'article';
		}
		$currentAtt = $this->get_article_attributes($articleId);
		foreach ($attributeArray as $name => $value) {
			if ( !in_array($name, array_keys($currentAtt)) || $value != $currentAtt[$name]['value'] ) {
				$attributelib->set_attribute($type, $articleId, $name, $value);
			}
		}
		return true;
	}

	function get_article_attributes($articleId, $isSubmission = false)
	{
		$attributelib = TikiLib::lib('attribute');

		if ($isSubmission) {
			$type = 'submission';
		} else {
			$type = 'article';
		}

		$allAttributes = $attributelib->get_attributes($type, $articleId);
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
		$this->query(
			'UPDATE `tiki_object_attributes` set `type` = ?, `itemId` = ? where `type` = ? and `itemId` = ?',
			array( 'article', $articleId, 'submission', $subId )
		);
	}

	/**
	 * Get related articles using $freetaglib->get_similar()
	 *
	 * @param int $articleId
	 * @param int $maxResults
	 * @return array
	 */
	function get_related_articles($articleId, $maxResults = 5)
	{
		$freetaglib = TikiLib::lib('freetag');
		$relatedArticles = $freetaglib->get_similar('article', $articleId);

		foreach ($relatedArticles as $key => $article) {
			$relatedArticles[$key]['articleId'] = $relatedId = str_replace('tiki-read_article.php?articleId=', '', $article['href']);

			$relatedArticle = $this->get_article($relatedId);

			// exclude articles from the list if they are not published or if no permission to view them
			if (!$relatedArticle || $relatedArticle['ispublished'] != 'y') {
				unset($relatedArticles[$key]);
			}
		}

		$relatedArticles = array_splice($relatedArticles, 0, $maxResults);

		return $relatedArticles;
	}
}

$artlib = new ArtLib;
