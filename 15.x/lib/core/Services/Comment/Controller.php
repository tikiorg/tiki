<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


// NOTE : This controller excludes anything related to the comments. Previous code mixed comments and forums
//        as they use the same storage. However, the way the are interacted with is so different that the code
//        needs to remain separate to keep everything simple.

class Services_Comment_Controller
{
	function action_list($input)
	{
		$type = $input->type->text();
		$objectId = $input->objectId->pagename();

		if (! $this->isEnabled($type, $objectId)) {
			throw new Services_Exception(tr('Comments not allowed on this page.'), 403);
		}

		if (! $this->canView($type, $objectId)) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		$commentslib = TikiLib::lib('comments');
		// TODO : Add pagination, sorting, thread style, moderation, ...
		$offset = 0;
		$per_page = 100;
		$comments = $commentslib->get_comments("$type:$objectId", null, $offset, $per_page);

		$this->markEditable($comments['data']);

		return array(
			'title' => tr('Comments'),
			'comments' => $comments['data'],
			'type' => $type,
			'objectId' => $objectId,
			'parentId' => 0,
			'cant' => $comments['cant'],
			'offset' => $offset,
			'per_page' => $per_page,
			'allow_post' => $this->canPost($type, $objectId) && ! $input->hidepost->int(),
			'allow_remove' => $this->canRemove($type, $objectId),
			'allow_lock' => $this->canLock($type, $objectId),
			'allow_unlock' => $this->canUnlock($type, $objectId),
			'allow_archive' => $this->canArchive($type, $objectId),
			'allow_moderate' => $this->canModerate($type, $objectId),
		);
	}

	function action_post($input)
	{
		global $prefs, $user;

		$type = $input->type->text();
		$objectId = $input->objectId->pagename();
		$parentId = $input->parentId->int();
		$return_url = $input->return_url->url();

		// Check general permissions

		if (! $this->isEnabled($type, $objectId)) {
			throw new Services_Exception(tr('Comments not allowed on this page.'), 403);
		}

		if (! $this->canPost($type, $objectId)) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		$commentslib = TikiLib::lib('comments');
		if ( $parentId && $prefs['feature_comments_locking'] == 'y') {
			$parent = $commentslib->get_comment($parentId);

			if ($parent['locked'] == 'y') {
				throw new Services_Exception(tr('Parent is locked.'), 403);
			}
		}

		$errors = array();

		$title = trim($input->title->text());
		$data = trim($input->data->wikicontent());
		$contributions = array();
		$anonymous_name = '';
		$anonymous_email = '';
		$anonymous_website = '';

		if (empty($user) || $prefs['feature_comments_post_as_anonymous'] == 'y') {
			$anonymous_name = $input->anonymous_name->text();
			$anonymous_email = $input->anonymous_email->email();
			$anonymous_website = $input->anonymous_website->website();
		}

		if ($input->post->int()) {
			// Validate

			if (empty($user)) {
				if (empty($anonymous_name)) {
					$errors['anonymous_name'] = tr('Pseudonym must be specified');
				}
			}

			if (! empty($anonymous_name) && empty($anonymous_email)) {
				$errors['anonymous_emal'] = tr('Email must be specified');
			}

			if ($prefs['comments_notitle'] != 'y' && empty($title)) {
				$errors['title'] = tr('Title is empty');
			}

			if (empty($data)) {
				$errors['data'] = tr('Content is empty');
			}

			if (empty($user) && $prefs['feature_antibot'] == 'y') {
				$captchalib = TikiLib::lib('captcha');

				if (! $captchalib->validate($input->none())) {
					$errors[] = $captchalib->getErrors();
				}
			}

			if ($prefs['comments_notitle'] == 'y') {
				$title = 'Untitled ' . TikiLib::lib('tiki')->get_long_datetime(TikiLib::lib('tikidate')->getTime());
			}

			if (count($errors) === 0) {
				$message_id = ''; // By ref
				$threadId = $commentslib->post_new_comment(
					"$type:$objectId",
					$parentId,
					$user,
					$title,
					$data,
					$message_id,
					isset($parent['message_id']) ? $parent['message_id'] : '',
					'n',
					'',
					'',
					$contributions,
					$anonymous_name,
					'',
					$anonymous_email,
					$anonymous_website
				);

				$feedback = array();

				if ($prefs['feature_comments_moderation'] === 'y' && ! $this->canModerate($type, $objectId)) {
					$feedback[] = tr('Your message has been queued for approval and will be posted after a moderator approves it.');
				}

				if ($threadId) {
					$this->rememberCreatedComment($threadId);

					if ($prefs['wiki_watch_comments'] == 'y' && $type == 'wiki page') {
						require_once('lib/notifications/notificationemaillib.php');
						sendCommentNotification('wiki', $objectId, $title, $data);
					} else if ($type == 'article') {
						require_once('lib/notifications/notificationemaillib.php');
						sendCommentNotification('article', $objectId, $title, $data);
					} elseif ($prefs['feature_blogs'] == 'y' && $type == 'blog post') { // Blog comment mail
						require_once('lib/notifications/notificationemaillib.php');
						 sendCommentNotification('blog', $objectId, $title, $data);
					} elseif ($type == 'trackeritem') {
						require_once('lib/notifications/notificationemaillib.php');
						sendCommentNotification('trackeritem', $objectId, $title, $data, $threadId);
					}

					$access = TikiLib::lib('access');
					if ($return_url && ! $access->is_xml_http_request()) {
						$access->redirect($return_url, tr('Your comment was posted.'));
					}
					

					return array(
						'threadId' => $threadId,
						'parentId' => $parentId,
						'type' => $type,
						'objectId' => $objectId,
						'feedback' => $feedback,
					);
				}
			}
		}

		return array(
			'parentId' => $parentId,
			'type' => $type,
			'objectId' => $objectId,
			'title' => $title,
			'data' => $data,
			'contributions' => $contributions,
			'anonymous_name' => $anonymous_name,
			'anonymous_email' => $anonymous_email,
			'anonymous_website' => $anonymous_website,
			'errors' => $errors,
			'return_url' => $return_url,
		);
	}

	function action_edit($input)
	{
		$threadId = $input->threadId->int();

		if (! $comment = $this->getCommentInfo($threadId)) {
			throw new Services_Exception_NotFound;
		}

		if (! $this->canEdit($comment)) {
			throw new Services_Exception_Denied;
		}

		if ($input->edit->int()) {
			$title = trim($input->title->text());
			$data = trim($input->data->wikicontent());
			
			$commentslib = TikiLib::lib('comments');
			$commentslib->update_comment($threadId, $title, $comment['comment_rating'], $data);

			return array(
				'threadId' => $threadId,
				'comment' => $comment,
			);
		}

		return array(
			'comment' => $comment,
		);
	}

	function action_remove($input)
	{
		$threadId = $input->threadId->int();
		$confirmation = $input->confirm->int();
		$status = '';

		if ($comment = $this->getCommentInfo($threadId)) {
			$type = $comment['objectType'];
			$object = $comment['object'];

			if (! $this->canRemove($type, $object)) {
				throw new Services_Exception(tr('Permission denied.'), 403);
			}

			if ($confirmation) {
				$commentslib = TikiLib::lib('comments');
				$commentslib->remove_comment($threadId);
				$status = 'DONE';
			}
		} else {
			$status = 'DONE'; // Already gone
		}


		return array(
			'threadId' => $threadId,
			'status' => $status,
			'objectType' => $type,
			'objectId' => $object,
			'parsed' => $comment['parsed'],
		);
	}

	function action_lock($input)
	{
		return $this->_action_lock($input, 'lock');
	}

	function action_unlock($input)
	{
		return $this->_action_lock($input, 'unlock');
	}

	private function _action_lock($input, $mode)
	{
		$type = $input->type->text();
		$objectId = $input->objectId->pagename();
		$confirmation = $input->confirm->int();
		$status = '';

		if (! $this->isEnabled($type, $objectId)) {
			throw new Services_Exception(tr('Comments not allowed on this page.'), 403);
		}

		$method = 'can' . ucfirst($mode);
		if (! $this->$method($type, $objectId)) {
			throw new Services_Exception(tr('Permissions denied.'), 403);
		}

		if ($confirmation) {
			$method = $mode . '_object_thread';

			$commentslib = TikiLib::lib('comments');
			$commentslib->$method("$type:$objectId");
			$status = 'DONE';
		}

		if ($mode === 'lock') {
			$title = tr('Lock comments');
		} 
		else {
			$title = tr('Unlock comments');
		}
		
		return array(
			'title' => $title,
			'type' => $type,
			'objectId' => $objectId,
			'status' => $status,
		);
	}

	function action_moderate($input)
	{
		$threadId = $input->threadId->int();
		$confirmation = $input->confirm->int();
		$do = $input->do->alpha();
		$status = '';

		if (! $comment = $this->getCommentInfo($threadId)) {
			throw new Services_Exception(tr('Comment not found.'), 404);
		}
		$type = $comment['objectType'];
		$object = $comment['object'];

		if ($comment['approved'] == 'y') {
			throw new Services_Exception(tr('Comment already approved.'), 403);
		}

		if (! $this->canModerate($type, $object)) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		$commentslib = TikiLib::lib('comments');

		if ($do == 'approve') {
			if ($confirmation) {
				$status = 'DONE';
				$commentslib->approve_comment($threadId);
			}
		} elseif ($do == 'reject') {
			if ($confirmation) {
				$status = 'DONE';
				$commentslib->reject_comment($threadId);
			}
		} else {
			throw new Exception(tr('Invalid argument.'), 500);
		}

		return array(
			'threadId' => $threadId,
			'type' => $type,
			'objectId' => $object,
			'status' => $status,
			'do' => $do,
		);
	}

	function action_archive($input)
	{
		$threadId = $input->threadId->int();
		$do = $input->do->alpha();
		$confirmation = $input->confirm->int();
		$status = '';

		if (! $comment = $this->getCommentInfo($threadId)) {
			throw new Services_Exception(tr('Comment not found.'), 404);
		}

		$type = $comment['objectType'];
		$object = $comment['object'];

		if (! $this->canArchive($type, $object)) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		if ($confirmation) {
			$status = 'DONE';

			$commentslib = TikiLib::lib('comments');
			if ($do == 'archive') {
				$commentslib->archive_thread($threadId);
			} else {
				$commentslib->unarchive_thread($threadId);
			}
		}

		return array(
			'threadId' => $threadId,
			'type' => $type,
			'objectId' => $object,
			'status' => $status,
			'do' => $do,
		);
	}

	function action_deliberation_item($input)
	{
		return array();
	}

	private function canView($type, $objectId)
	{
		// Note: $perms provides a magic method __get as an accessor for attributes.
		// I.e. $perms->wiki_view_comments or $perms->tracker_view_comments are returend by that accessor method
		// and do not exist as a property.
		// Wether they are true or false depends on the assigned permissions stored in $perms->resolver
		// for the respective groups.
		 
		$perms = $this->getApplicablePermissions($type, $objectId);

		switch ($type) {
			case 'wiki page':
				return $perms->wiki_view_comments;
				break;
			
			// canPost() requires also view access frontend/template wise. 
			// So we return also true if post ($perms->comment_tracker_items) is enabled. 
			case 'trackeritem':
				return ($perms->tracker_view_comments || $perms->comment_tracker_items);
				break;
				
			
			// @TODO which $types do use / or should use these permissions?
			// taken from the prevoius developer: seems that view should be automatically assigned if edit / post is granted.
			default:
				if (! ($perms->read_comments || $perms->post_comments || $perms->edit_comments)) {
					return false;
				}
				break;
		}

		return true;
	}

	
	public function canPost($type, $objectId)
	{
		global $prefs;
		
		// see comment about $perms in canView().

		$perms = $this->getApplicablePermissions($type, $objectId);

		if ($prefs['feature_comments_locking'] == 'y' &&  TikiLib::lib('comments')->is_object_locked("$type:$objectId")) {
			return false;
		}
		
		switch ($type) {
					
			// requires also view access from the front/template part
			// so we add $perms->comment_tracker_items also to canView()
			case 'trackeritem':
				return $perms->comment_tracker_items;
				break;
		
			// @TODO which $types do use / or should use these permissions?
			default:
				if (! ($perms->post_comments)) {
					return false;
				}
				break;
		}
		

		return true;
	}
	

	public function isEnabled($type, $objectId)
	{
		global $prefs;

		switch ($type) {
		case 'wiki page':
			if ($prefs['feature_wiki_comments'] != 'y') {
				return false;
			}

			if ($prefs['wiki_comments_allow_per_page'] == 'y') {
				$info = TikiLib::lib('tiki')->get_page_info($objectId);
				if (! empty($info['comments_enabled'])) {
					return $info['comments_enabled'] == 'y';
				}
			}

			return true;
		case 'image gallery':
			return $prefs['feature_image_galleries_comments'] == 'y';
		case 'file gallery':
			return $prefs['feature_file_galleries_comments'] == 'y';
		case 'poll':
			return $prefs['feature_poll_comments'] == 'y';
		case 'faq':
			return $prefs['feature_faq_comments'] == 'y';
		case 'blog post':
			return $prefs['feature_blogposts_comments'] == 'y';
		case 'trackeritem':
			return true;
		case 'article':
			return $prefs['feature_article_comments'] == 'y';
		case 'activity':
			return $prefs['activity_basic_events'] == 'y' || $prefs['activity_custom_events'] == 'y' || $prefs['monitor_enabled'] == 'y';
		default:
			return false;
		}
	}

	private function getCommentInfo($threadId)
	{
		if (! $threadId) {
			throw new Services_Exception(tr('Thread not specified.'), 500);
		}

		$commentslib = TikiLib::lib('comments');
		$comment = $commentslib->get_comment($threadId);

		if ($comment) {
			$type = $comment['objectType'];
			$object = $comment['object'];

			if (! $this->isEnabled($type, $object)) {
				throw new Services_Exception(tr('Comments not allowed on this page.'), 403);
			}

			return $comment;
		}
	}

	private function canLock($type, $objectId)
	{
		global $prefs;

		if ($prefs['feature_comments_locking'] != 'y') {
			return false;
		}

		$perms = $this->getApplicablePermissions($type, $objectId);

		if (! $perms->lock_comments) {
			return false;
		}

		$commentslib = TikiLib::lib('comments');
		return ! $commentslib->is_object_locked("$type:$objectId");
	}

	private function canUnlock($type, $objectId)
	{
		global $prefs;

		if ($prefs['feature_comments_locking'] != 'y') {
			return false;
		}

		$perms = $this->getApplicablePermissions($type, $objectId);

		if (! $perms->lock_comments) {
			return false;
		}

		$commentslib = TikiLib::lib('comments');
		return $commentslib->is_object_locked("$type:$objectId");
	}

	private function canArchive($type, $objectId)
	{
		global $prefs;

		if ($prefs['comments_archive'] != 'y') {
			return false;
		}

		$perms = $this->getApplicablePermissions($type, $objectId);

		return $perms->admin_comments;
	}

	private function canRemove($type, $objectId)
	{
		$perms = $this->getApplicablePermissions($type, $objectId);
		return $perms->remove_comments;
	}

	private function canModerate($type, $objectId)
	{
		global $prefs;

		if ($prefs['feature_comments_moderation'] != 'y') {
			return false;
		}

		$perms = $this->getApplicablePermissions($type, $objectId);

		return $perms->admin_comments;
	}

	private function markEditable(& $comments)
	{
		foreach ($comments as & $comment) {
			$comment['can_edit'] = $this->canEdit($comment);

			if ($comment['replies_info']['numReplies'] > 0) {
				$this->markEditable($comment['replies_info']['replies']);
			}
		}
	}

	private function canEdit(array $comment)
	{
		global $prefs, $user, $tiki_p_admin;

		if ($tiki_p_admin == 'y') {
			return true;
		}

		if ($prefs['comments_allow_correction'] != 'y') {
			return false;
		}

		$tikilib = TikiLib::lib('tiki');
		$thirtyMinutes = 30*60;

		if ($comment['commentDate'] < $tikilib->now - $thirtyMinutes) {
			return false;
		}

		if ($comment['userName'] == $user) {
			return true;
		}

		// Handles comments created by anonymous users
		if (isset($_SESSION['created_comments']) && in_array($comment['threadId'], $_SESSION['created_comments'])) {
			return true;
		}

		return false;
	}

	private function getApplicablePermissions($type, $objectId)
	{
		switch ($type) {
		case 'trackeritem':
			$item = Tracker_Item::fromId($objectId);
			return $item->getPerms();
		default:
			return Perms::get($type, $objectId);
		}
	}

	private function rememberCreatedComment($threadId)
	{
		if (! isset($_SESSION['created_comments'])) {
			$_SESSION['created_comments'] = array();
		}

		$_SESSION['created_comments'][] = $threadId;
	}
}

