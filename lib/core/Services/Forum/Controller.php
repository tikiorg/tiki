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
 * Class Services_Forum_Controller
 */
class Services_Forum_Controller
{
	private $lib;

	function setUp()
	{
		Services_Exception_Disabled::check('feature_forums');
		$this->lib = TikiLib::lib('comments');
	}

	/**
	 * Moderator action that locks a forum topic
	 * @param $input
	 * @return array
	 */
	function action_lock_topic($input)
	{
		return $this->lockUnlock($input, 'lock');
	}

	/**
	 * Moderator action that unlocks a forum topic
	 * @param $input
	 * @return array
	 */
	function action_unlock_topic($input)
	{
		return $this->lockUnlock($input, 'unlock');
	}

	/**
	 * Moderator action to merge selected forum topics or posts with another topic
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_merge_topic($input)
	{
		parse_str($input->offsetGet('params'), $params);
		$this->checkPerms($params['forumId']);
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			if (count($params['forumtopic']) > 0) {
				$items = $this->getTopicTitles($params['forumtopic']);
				$toList = json_decode($params['comments_coms'], true);
				$object = count($items) > 1 ? 'topics' : 'topic';
				if (isset($params['comments_parentId'])) {
					unset($toList[$params['comments_parentId']]);
					$object = count($items) > 1 ? 'posts' : 'post';
				}
				$diff = array_diff_key($toList, $items);
				if (count($diff) > 0) {
					[
						'action' => 'merge_topic',
						'title' => tr('Merge selected %0 with another topic', $object),
						'items' => $items,
						'ticket' => $check['ticket'],
						'toList' => $toList,
						'object' => $object,
						'modal' => '1',
					];
				} else {
					//oops if all topics were selected
					return [
						'FORWARD' => [
							'controller' => 'utilities',
							'action' => 'alert',
							'type' => 'warning',
							'title' => tra('Topic merge feedback'),
							'heading' => tra('Oops'),
							'msg' => tra('All topics or posts were selected, leaving none to merge with. Please make your selection again.'),
							'modal' => '1'
						]
					];
				}
				return [
					'action' => 'merge_topic',
					'title' => tr('Merge selected %0 with another topic', $object),
					'items' => $items,
					'ticket' => $check['ticket'],
					'toList' => $toList,
					'object' => $object,
					'modal' => '1',
				];
			} else {
				//oops if no topics were selected
				return [
					'FORWARD' => [
						'controller' => 'utilities',
						'action' => 'alert',
						'type' => 'warning',
						'title' => tra('Topic merge feedback'),
						'heading' => tra('Oops'),
						'msg' => tra('No topics were selected. Please select the topics you wish to merge before clicking the merge button.'),
						'modal' => '1'
					]
				];
			}
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['toId'])) {
			$items = json_decode($input->offsetGet('items'), true);
			$toId = $input->toId->int();
			foreach ($items as $id => $topic) {
				if ($id !== $toId) {
					$this->lib->set_parent($id, $toId);
				}
			}
			return true;
 		} elseif ($check === false) {
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'error',
					'title' => tra('Topic merge feedback'),
					'heading' => tra('Error'),
					'msg' => tra('Sea Surfing (CSRF) detected. Operation blocked.'),
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Moderator action to move selected forum topics to another forum
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_move_topic($input)
	{
		parse_str($input->offsetGet('params'), $params);
		$this->checkPerms($params['forumId']);
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			if (count($params['forumtopic']) > 0) {
				$items = $this->getTopicTitles($params['forumtopic']);
				$toList = json_decode($params['all_forums'], true);
				return [
					'action' => 'move_topic',
					'title' => tra('Move selected topics to another forum'),
					'items' => $items,
					'ticket' => $check['ticket'],
					'toList' => $toList,
					'forumId' => $params['forumId'],
					'forumName' => $toList[$params['forumId']],
					'modal' => '1',
				];
			} else {
				//oops if no topics were selected
				return [
					'FORWARD' => [
						'controller' => 'utilities',
						'action' => 'alert',
						'type' => 'warning',
						'title' => tra('Topic move feedback'),
						'heading' => tra('Oops'),
						'msg' => tra('No topics were selected. Please select the topics you wish to move before clicking the move button.'),
						'modal' => '1'
					]
				];
			}
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['toId'])) {
			$items = json_decode($input->offsetGet('items'), true);
			$toId = $input->toId->int();
			$forumId = $input->forumId->int();
			foreach ($items as $id => $topic) {
				// To move a topic you just have to change the object
				$obj = 'forum:' . $toId;
				$this->lib->set_comment_object($id, $obj);
				// update the stats for the source and destination forums
				$this->lib->forum_prune($forumId);
				$this->lib->forum_prune($toId);
			}
			return true;
		} elseif ($check === false) {
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'error',
					'title' => tra('Topic move feedback'),
					'heading' => tra('Error'),
					'msg' => tra('Sea Surfing (CSRF) detected. Operation blocked.'),
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Moderator action to delete one or more topics
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_delete_topic($input)
	{
		parse_str($input->offsetGet('params'), $params);
		$this->checkPerms($params['forumId']);
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			if (count($params['forumtopic']) > 0) {
				$items = $this->getTopicTitles($params['forumtopic']);
				if (isset($params['comments_parentId'])) {
					$object = count($items) > 1 ? 'posts' : 'post';
				} else {
					$object = count($items) > 1 ? 'topics' : 'topic';
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-forum-delete_topic',
						'customVerb' => tra('delete'),
						'customObject' => tr('forum %0', $object),
						'items' => $items,
						'ticket' => $check['ticket'],
						'extra' => ['forumId' => $params['forumId']],
						'modal' => '1',
					]
				];
			} else {
				//oops if no topics were selected
				return [
					'FORWARD' => [
						'controller' => 'utilities',
						'action' => 'alert',
						'type' => 'warning',
						'title' => tra('Topic delete feedback'),
						'heading' => tra('Oops'),
						'msg' => tra('No topics were selected. Please select the topics you wish to delete before clicking the delete button.'),
						'modal' => '1'
					]
				];
			}
		} elseif ($check === true && count($_POST['items']) > 0) {
			$items = $input->asArray('items');
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$this->lib->remove_comment($id);
				}
			}
			$extra = $input->asArray('extra');
			$this->lib->forum_prune((int) $extra['forumId']);
			return true;
		} elseif ($check === false) {
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'error',
					'title' => tra('Topic delete feedback'),
					'heading' => tra('Error'),
					'msg' => tra('Sea Surfing (CSRF) detected. Operation blocked.'),
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Moderator action to delete a forum post attachment
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_delete_attachment($input)
	{
		parse_str($input->offsetGet('params'), $params);
		$this->checkPerms($params['forumId']);
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			if (!empty($params['remove_attachment'])) {
				$items[$params['remove_attachment']] = $params['filename'];
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-forum-delete_attachment',
						'customVerb' => tra('delete'),
						'customObject' => tra('attachment'),
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				//oops if no attachments were selected
				return [
					'FORWARD' => [
						'controller' => 'utilities',
						'action' => 'alert',
						'type' => 'warning',
						'title' => tra('Attachment delete feedback'),
						'heading' => tra('Oops'),
						'msg' => tra('No attachments were selected. Please select an attachment to delete.'),
						'modal' => '1'
					]
				];
			}
		} elseif ($check === true && count($_POST['items']) > 0) {
			$items = $input->asArray('items');
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$this->lib->remove_thread_attachment($id);
				}
			}
			return true;
		} elseif ($check === false) {
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'error',
					'title' => tra('Attachment delete feedback'),
					'heading' => tra('Error'),
					'msg' => tra('Sea Surfing (CSRF) detected. Operation blocked.'),
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Moderator action that archives a forum thread
	 * @param $input
	 * @return array
	 */
	function action_archive_topic($input)
	{
		return $this->archiveUnarchive($input, 'archive');
	}

	/**
	 * Moderator action that archives a forum thread
	 * @param $input
	 * @return array
	 */
	function action_unarchive_topic($input)
	{
		return $this->archiveUnarchive($input, 'unarchive');
	}

	/**
	 * Action to delete one or more forums
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_delete_forum($input)
	{
		parse_str($input->offsetGet('params'), $params);
		if (isset($params['batchaction']) && $params['batchaction'] !== 'delete_forum') {
			//oops if no action is selected
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'warning',
					'title' => tra('Forum delete feedback'),
					'heading' => tra('Oops'),
					'msg' => tra('No action was selected. Please select an action before clicking the OK button.'),
					'modal' => '1'
				]
			];
		}
		$perms = Perms::get('forum', $params['checked']);
		if (!$perms->admin_forum) {
			throw new Services_Exception_Denied(tr('Reserved for forum administrators'));
		}
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			if (count($params['checked']) > 0) {
				$items = $this->getForumNames($params['checked']);
				$object = count($items) > 1 ? 'forums' : 'forum';
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-forum-delete_forum',
						'customVerb' => tra('delete'),
						'customObject' => tr($object),
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				//oops if no topics were selected
				return [
					'FORWARD' => [
						'controller' => 'utilities',
						'action' => 'alert',
						'type' => 'warning',
						'title' => tra('Forum delete feedback'),
						'heading' => tra('Oops'),
						'msg' => tra('No forums were selected. Please select the forums you wish to delete before clicking the OK button.'),
						'modal' => '1'
					]
				];
			}
		} elseif ($check === true && count($_POST['items']) > 0) {
			$items = $input->asArray('items');
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$this->lib->remove_forum($id);
				}
			}
			return true;
		} elseif ($check === false) {
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'error',
					'title' => tra('Topic delete feedback'),
					'heading' => tra('Error'),
					'msg' => tra('Sea Surfing (CSRF) detected. Operation blocked.'),
					'modal' => '1'
				]
			];
		}
	}

	private function checkPerms($forumId)
	{
		$perms = Perms::get('forum', $forumId);
		if (!$perms->admin_forum) {
			$info = $this->lib->get_forum($forumId);
			global $user;
			if ($info['moderator'] !== $user) {
				$userlib = TikiLib::lib('user');
				if (!in_array($info['moderator_group'], $userlib->get_user_groups($user))) {
					throw new Services_Exception_Denied(tr('Reserved for forum administrators and moderators'));
				} else {
					return true;
				}
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	/**
	 * Utility to get topic names
	 *
	 * @param $topicIds
	 * @return mixed
	 * @throws Exception
	 */
	private function getTopicTitles(array $topicIds)
	{
		foreach ($topicIds as $id) {
			$info = $this->lib->get_comment($id);
			$ret[(int) $id] = $info['title'];
		}
		return $ret;
	}

	/**
	 * Utility to get forum names
	 *
	 * @param $forumIds
	 * @return mixed
	 * @throws Exception
	 */
	private function getForumNames(array $forumIds)
	{
		foreach ($forumIds as $id) {
			$info = $this->lib->get_forum($id);
			$ret[(int) $id] = $info['name'];
		}
		return $ret;
	}


	/**
	 * Utility used by action_lock_topic and action_unlock_topic since the code for both is similar
	 * @param $input
	 * @param $type
	 * @return array
	 * @throws Exception
	 */
	private function lockUnlock($input, $type)
	{
		parse_str($input->params->none(), $params);
		$this->checkPerms($params['forumId']);
		$fn = $type . '_comment';
		if (count($params['forumtopic']) > 0) {
			check_ticket('view-forum');
			$items = $this->getTopicTitles($params['forumtopic']);
			foreach ($items as $id => $topic) {
				$this->lib->$fn($id);
			}
			return true;
		} else {
			//oops if no topics were selected
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'warning',
					'title' => tr('Topic %0 feedback', tra($type)),
					'heading' => tra('Oops'),
					'msg' => tr('No topics were selected. Please select the topics you wish to %0 before clicking the %1 button.',
						tra($type), tra($type)),
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Utility used by action_archive_topic and action_unarchive_topic since the code for both is similar
	 * @param $input
	 * @param $type
	 * @return array
	 * @throws Exception
	 */
	private function archiveUnarchive($input, $type)
	{
		$params = $input->asArray('params');
		$this->checkPerms($params['forumId']);
		$fn = $type . '_thread';
		if (!empty($params['comments_parentId'])) {
			check_ticket('view-forum');
			$this->lib->$fn($params['comments_parentId']);
			return true;
		} else {
			//oops if no topics were selected
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'warning',
					'title' => tr('Topic %0 feedback', tra($type)),
					'heading' => tra('Oops'),
					'msg' => tr('No topics were selected. Please select the topics you wish to %0 before clicking the %1 button.',
						tra($type), tra($type)),
					'modal' => '1'
				]
			];
		}
	}
}

