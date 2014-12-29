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
		$ret = $this->lockUnlock($input, 'lock');
		return ['FORWARD' => $ret];
	}

	/**
	 * Moderator action that unlocks a forum topic
	 * @param $input
	 * @return array
	 */
	function action_unlock_topic($input)
	{
		$ret = $this->lockUnlock($input, 'unlock');
		return ['FORWARD' => $ret];
	}

	/**
	 * Moderator action to merge selected forum topics with another topic
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_merge_topic($input)
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['toId'])) {
			$ret = $this->setVars($input, 'merge', 2);
			if (!empty($ret['selectedTopics'])) {
				$commentslib = TikiLib::lib('comments');
				check_ticket('view-forum');
				foreach ($ret['selectedTopics'] as $id => $topic) {
					if ($id !== $ret['toId']) {
						$commentslib->set_parent($id, $ret['toId']);
					}
				}
			}
			$ret['title'] = tr('Topic merge feedback');
			$ret['action'] = 'success';
			unset($ret['toId']);
			return [
				'FORWARD' => $ret
			];
		} else {
			$ret = $this->setVars($input, 'merge');
			$ret['title'] = tr('Merge selected topics with another topic');
			return $ret;
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
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['toId'])) {
			$ret = $this->setVars($input, 'move', 2);
			if (!empty($ret['selectedTopics'])) {
				$commentslib = TikiLib::lib('comments');
				foreach ($ret['selectedTopics'] as $id => $topic) {
					check_ticket('view-forum');
					// To move a topic you just have to change the object
					$obj = 'forum:' . $ret['toId'];
					$commentslib->set_comment_object($id, $obj);
					// update the stats for the source and destination forums
					$commentslib->forum_prune($ret['forumId']);
					$commentslib->forum_prune($ret['toId']);
				}
			}
			$ret['title'] = tr('Topic move feedback');
			$ret['action'] = 'success';
			unset($ret['toId'], $ret['forumId']);
			return [
				'FORWARD' => $ret
			];
		} else {
			$ret = $this->setVars($input, 'move');
			$ret['title'] = tr('Move selected topics to another forum');
			return $ret;
		}
	}

	/**
	 * Used by action_merge_topic and action_move_topic functions to give success feedback
	 * @param $input
	 * @return array
	 */
	function action_success($input)
	{
		$ret = [
			'title' => $input->title->striptags(),
			'action' => 'success',
			'toName' => $input->toName->striptags(),
			'selectedTopics' => $input->selectedTopics->striptags(),
			'type' => $input->type->word(),
		];
		if ($ret['type'] === 'move') {
			$ret['forumName'] = $input->forumName->word();
		}
		return $ret;
	}

	/**
	 * Utility to set return variables common to the functions in this class
	 * @param $input
	 * @param $type
	 * @param int $i
	 * @return array
	 * @throws Exception
	 */
	private function setVars($input, $type, $i = 1)
	{
		$ret = [];
		if ($type === 'move') {
			$toListLabel = 'all_forums';
		} elseif ($type === 'merge') {
			$toListLabel = 'comments_coms';
		}
		if ($i === 1) {
			parse_str($input->form->none(), $form);
			if ($type === 'move') {
				$ret['forumId'] = $form['forumId'];
			}
			if (!in_array($type, ['lock', 'unlock'])) {
				$ret['toList'] = json_decode($form[$toListLabel], true);
			}
			if ($type === 'move') {
				$ret['forumName'] = $ret['toList'][$form['forumId']];
			}
			$commentslib = TikiLib::lib('comments');
			foreach ($form['forumtopic'] as $id) {
				$info = $commentslib->get_comment($id);
				$ret['selectedTopics'][$id] = $info['title'];
			}
			$ret['type'] = $type;
			return $ret;
		} else {
			if ($type === 'move') {
				$ret['forumId'] = $input->forumId->int();
			}
			$toList = json_decode($input->toList->striptags(), true);
			if ($type === 'move') {
				$ret['forumName'] = $toList[$ret['forumId']];
			}
			$ret['selectedTopics'] = json_decode($input->forumtopic->striptags(), true);
			$ret['toId'] = $input->toId->int();
			$ret['toName'] = $toList[$ret['toId']];
			$ret['type'] = $type;
			return $ret;
		}
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
		$ret = $this->setVars($input, $type);
		if (!empty($ret['selectedTopics'])) {
			$commentslib = TikiLib::lib('comments');
			check_ticket('view-forum');
			foreach ($ret['selectedTopics'] as $id => $topic) {
				$commentslib->$fn($id);
			}
		}
		$ret['title'] = tr('Topic %0 feedback', $type);
		$ret['action'] = 'success';
		$ret['modal'] = '1';
		return $ret;
	}

}

