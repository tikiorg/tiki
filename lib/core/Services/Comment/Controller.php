<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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

		if (! $this->isEnabled($type, $object)) {
			throw new Services_Exception(tr('Comments not allowed on this page.'), 403);
		}

		if (! $this->canView($type, $objectId)) {
			throw new Services_Exception(tr('Permission denied.'), 403);
		}

		$commentslib = TikiLib::lib('comments');
		// TODO : Add pagination, sorting, thread style, moderation, ...
		$offset = 0;
		$per_page = 100;
		$comments_coms = $commentslib->get_comments("$type:$objectId", null, $offset, $per_page);

		return array(
			'comments' => $comments_coms['data'],
			'type' => $type,
			'objectId' => $objectId,
			'parentId' => 0,
			'cant' => $comments_coms['cant'],
			'offset' => $offset,
			'per_page' => $per_page,
			'allow_post' => $this->canPost($type, $objectId),
			'allow_lock' => $this->canLock($type, $objectId),
			'allow_unlock' => $this->canUnlock($type, $objectId),
		);
	}

	function action_post($input)
	{
		global $prefs, $user;

		$type = $input->type->text();
		$objectId = $input->objectId->pagename();
		$parentId = $input->parentId->int();

		// Check general permissions

		if (! $this->isEnabled($type, $object)) {
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

				if (! $captchalib->validate()) {
					$errors[] = $captchalib->getErrors();
				}
			}

			if (count($errors) === 0) {
				$message_id = ''; // By ref
				$threadId = $commentslib->post_new_comment("$type:$objectId", $parentId, $user, $title, $data, $message_id, $parent ? $parent['message_id'] : '', 'n', '', '', $contributions, $anonymous_name, '', $anonymous_email, $anonymous_website);
				return array(
					'threadId' => $threadId,
					'parentId' => $parentId,
					'type' => $type,
					'objectId' => $objectId,
				);
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

			$perms = Perms::get($type, $object);
			if (! $perms->remove_comments) {
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

		return array(
			'type' => $type,
			'objectId' => $objectId,
			'status' => $status,
		);
	}

	private function canView($type, $objectId)
	{
		$perms = Perms::get($type, $objectId);

		if (! ($perms->read_comments || $perms->post_comments || $perms->edit_comments)) {
			return false;
		}

		switch ($type) {
		case 'wiki page':
			return $perms->wiki_view_comments;
		}

		return true;
	}

	private function canPost($type, $objectId)
	{
		global $prefs;

		$perms = Perms::get($type, $objectId);
		if (! $perms->post_comments) {
			return false;
		}

		if ($prefs['feature_comments_locking'] == 'y' &&  TikiLib::lib('comments')->is_object_locked("$type:$objectId")) {
			return false;
		}

		return true;
	}

	private function isEnabled($type, $objectId)
	{
		global $prefs;

		switch ($type) {
		case 'wiki page':
			if ($prefs['feature_wiki_comments'] != 'y') {
				return false;
			}

			if ($prefs['wiki_comments_allowed_per_page'] == 'y') {
				$info = TikiLib::lib('tiki')->get_page_info($objectId);
				if (! empty($info['comments_enabled'])) {
					return $info['comments_enabled'] == 'y';
				}
			}

			return true;
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

	private function canLock($type, $objectId) {
		global $prefs;

		if ($prefs['feature_comments_locking'] != 'y') {
			return false;
		}

		$perms = Perms::get($type, $objectId);

		if (! $perms->lock_comments) {
			return false;
		}

		$commentslib = TikiLib::lib('comments');
		return ! $commentslib->is_object_locked("$type:$objectId");
	}

	private function canUnlock($type, $objectId) {
		global $prefs;

		if ($prefs['feature_comments_locking'] != 'y') {
			return false;
		}

		$perms = Perms::get($type, $objectId);

		if (! $perms->lock_comments) {
			return false;
		}

		$commentslib = TikiLib::lib('comments');
		return $commentslib->is_object_locked("$type:$objectId");
	}
}

