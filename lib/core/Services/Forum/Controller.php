<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
	 * Moderator action to merge selected forum topics with another topic
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_merge_topic($input)
	{
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			//check number of topics on first pass
			if (count($params['forumtopic']) > 0) {
				$items = $this->getTopicTitles($params['forumtopic']);
				$toList = json_decode($params['comments_coms'], true);
				return [
					'action' => 'merge_topic',
					'title' => tra('Merge selected topics with another topic'),
					'items' => $items,
					'ticket' => $check['ticket'],
					'toList' => $toList,
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
			$toList = json_decode($input->offsetGet('toList'), true);
			$toId = $input->toId->int();
			$commentslib = TikiLib::lib('comments');
			foreach ($items as $id => $topic) {
				if ($id !== $toId) {
					$commentslib->set_parent($id, $toId);
				}
			}
			$toName = $toList[$toId];
			if (count($items) == 1) {
				$msg = tr('The following topic has been merged with the %0%1%2 topic:', '<em>', $toName, '</em>');
			} else {
				$msg = tr('The following topics have been merged with the %0%1%2 topic:', '<em>', $toName, '</em>');
			}
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'feedback',
					'title' => tra('Topic merge feedback'),
					'heading' => tra('Success'),
					'items' => $items,
					'msg' => $msg,
					'timeoutMsg' => tra('This popup will automatically close in 5 seconds.'),
					'modal' => '1'
				]
			];
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
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
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
			$toList = json_decode($input->offsetGet('toList'), true);
			$toId = $input->toId->int();
			$forumId = $input->forumId->int();
			$commentslib = TikiLib::lib('comments');
			foreach ($items as $id => $topic) {
				// To move a topic you just have to change the object
				$obj = 'forum:' . $toId;
				$commentslib->set_comment_object($id, $obj);
				// update the stats for the source and destination forums
				$commentslib->forum_prune($forumId);
				$commentslib->forum_prune($toId);
			}
			$toName = $toList[$toId];
			if (count($items) == 1) {
				$msg = tr('The following topic has been moved to the %0%1%2 forum:', '<em>', $toName, '</em>');
			} else {
				$msg = tr('The following topics have been moved to the %0%1%2 forum:', '<em>', $toName, '</em>');
			}
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'feedback',
					'title' => tra('Topic move feedback'),
					'heading' => tra('Success'),
					'items' => $items,
					'msg' => $msg,
					'timeoutMsg' => tra('This popup will automatically close in 5 seconds.'),
					'modal' => '1'
				]
			];
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
		$access = TikiLib::lib('access');
		$check = $access->check_authenticity(null, false);
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			//check number of topics on first pass
			if (count($params['forumtopic']) > 0) {
				$items = $this->getTopicTitles($params['forumtopic']);
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-forum-delete_topic',
						'customVerb' => tra('delete'),
						'customObject' => tra('forum topics'),
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
			$commentslib = TikiLib::lib('comments');
			$items = $input->asArray('items');
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$commentslib->remove_comment($id);
				}
			}
			$extra = $input->asArray('extra');
			$commentslib->forum_prune((int) $extra['forumId']);
			if (count($items) == 1) {
				$msg = tra('The following topic has been deleted:');
			} else {
				$msg = tra('The following topics have been deleted:');
			}
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'feedback',
					'title' => tra('Topic delete feedback'),
					'heading' => tra('Success'),
					'items' => $items,
					'msg' => $msg,
					'timeoutMsg' => tra('This popup will automatically close in 5 seconds.'),
					'modal' => '1'
				]
			];
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
	 * Utility to get topic names
	 *
	 * @param $topicIds
	 * @return mixed
	 * @throws Exception
	 */
	private function getTopicTitles($topicIds)
	{
		$commentslib = TikiLib::lib('comments');
		foreach ($topicIds as $id) {
			$info = $commentslib->get_comment($id);
			$ret[(int) $id] = $info['title'];
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
		$fn = $type . '_comment';
		parse_str($input->params->none(), $params);
		if (count($params['forumtopic']) > 0) {
			$commentslib = TikiLib::lib('comments');
			check_ticket('view-forum');
			$items = $this->getTopicTitles($params['forumtopic']);
			foreach ($items as $id => $topic) {
				$commentslib->$fn($id);
			}
			if (count($items) == 1) {
				$msg = tr('The following topic has been %0:', tra($type . 'ed'));
			} else {
				$msg = tr('The following topics have been %0:', tra($type . 'ed'));
			}
			return [
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'alert',
					'type' => 'feedback',
					'title' => tr('Topic %0 feedback', $type),
					'heading' => tra('Success'),
					'items' => $items,
					'msg' => $msg,
					'timeoutMsg' => tra('This popup will automatically close in 5 seconds.'),
					'modal' => '1'
				]
			];
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

