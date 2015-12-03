<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_FavoriteController
{
	function setUp()
	{
		global $prefs;

		if ($prefs['user_favorites'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}
	}

	function action_list($input)
	{
		global $user;

		if (! $user) {
			return array();
		}

		$relationlib = TikiLib::lib('relation');
		$favorites = array();
		foreach ($relationlib->get_relations_from('user', $user, 'tiki.user.favorite') as $relation) {
			$favorites[$relation['relationId']] = $relation['type'] . ':' . $relation['itemId'];
		}

		return $favorites;
	}

	function action_toggle($input)
	{
		global $user;

		if (! $user) {
			throw new Services_Exception(tr('Must be authenticated'), 403);
		}

		$type = $input->type->none();
		$object = $input->object->none();
		$target = $input->target->int();

		if (! $type || ! $object) {
			throw new Services_Exception(tr('Invalid input'), 400);
		}

		$relationlib = TikiLib::lib('relation');

		$tx = TikiDb::get()->begin();

		$relations = $this->action_list($input);
		$relationId = $this->getCurrentRelation($relations, $user, $type, $object);

		if ($type == 'trackeritem') {
			$parentobject = TikiLib::lib('trk')->get_tracker_for_item($object);
		} else {
			$parentobject = 'not implemented';
		}

		if ($target) {
			if (! $relationId) {
				$relationId = $relationlib->add_relation('tiki.user.favorite', 'user', $user, $type, $object);
				$relations[$relationId] = "$type:$object";

				$item_user = $this->getItemUser($type, $object);

				TikiLib::events()->trigger(
					'tiki.social.favorite.add',
					array(
						'type' => $type,
						'object' => $object,
						'parentobject' => $parentobject,
						'user' => $user,
						'item_user' => $item_user,
					)
				);
			}
		} else {
			if ($relationId) {
				$relationlib->remove_relation($relationId);
				unset($relations[$relationId]);
				TikiLib::events()->trigger(
					'tiki.social.favorite.remove',
					array(
						'type' => $type,
						'object' => $object,
						'parentobject' => $parentobject,
						'user' => $user,
					)
				);
			}
		}

		$tx->commit();

		return array(
			'list' => $relations,
		);
	}

	private function getCurrentRelation($relations, $user, $type, $object)
	{
		foreach ($relations as $id => $key) {
			if ($key === "$type:$object") {
				return $id;
			}
		}
	}

	private function getItemUser($type, $object)
	{
		global $user;

		$item_user = null;

		if ($type == 'forum post') {
			$commentslib = TikiLib::lib('comments');
			$forum_id = $commentslib->get_comment_forum_id($object);
			$forum_info = $commentslib->get_forum($forum_id);
			$thread_info = $commentslib->get_comment($object, null, $forum_info);
			$item_user = $thread_info['userName'];
		} elseif ($type == 'article') {
			$artlib = TikiLib::lib('art');
			$res = $artlib->get_article($object);
			$item_user = $res['author'];
		}

		return $item_user;
	}
}

