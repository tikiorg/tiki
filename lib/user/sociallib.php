<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class SocialLib
{
	private $relationlib;
	private $networkType;

	function __construct()
	{
		global $prefs;
		$this->relationlib = TikiLib::lib('relation');
		$this->networkType = $prefs['social_network_type'] ?: 'follow';
	}

	function listFriends($user)
	{
		return $this->getRelations('follow', $user);
	}

	function listFollowers($user)
	{
		return $this->getRelations('follow.invert', $user);
	}

	function listIncomingRequests($user)
	{
		return $this->getRelations('request.invert', $user);
	}

	function listOutgoingRequests($user)
	{
		return $this->getRelations('request', $user);
	}

	function addFriend($user, $newFriend)
	{
		if ($user == $newFriend) {
			return false;
		}

		$userlib = TikiLib::lib('user');
		if (! $userlib->user_exists($user) || ! $userlib->user_exists($newFriend)) {
			return false;
		}

		$tx = TikiDb::get()->begin();

		$hash = $this->createHash($user, $newFriend);

		if ($this->networkType == 'follow') {
			$this->addRelation('follow', $user, $newFriend);
			TikiLib::events()->trigger(
				'tiki.user.follow.add',
				array(
					'type' => 'user',
					'object' => $user,
					'user' => $user,
					'follow_id' => $newFriend,
					'aggregate' => $hash,
				)
			);
			TikiLib::events()->trigger(
				'tiki.user.follow.incoming',
				array(
					'type' => 'user',
					'object' => $newFriend,
					'user' => $newFriend,
					'follow_id' => $user,
					'aggregate' => $hash,
				)
			);
		} elseif ($this->networkType == 'follow_approval' || $this->networkType == 'friend') {
			$request = $this->getRelation('request.invert', $user, $newFriend);
			$follow = $this->getRelation('follow.invert', $user, $newFriend);

			if ($request || $follow) {
				// If there was a pending request by the other side (or pre-approved), remove the request
				// and approve both directions.

				// Re-add or empty-delete are not an issue
				$this->relationlib->remove_relation($request);
				$this->addRelation('follow', $user, $newFriend);
				$this->addRelation('follow.invert', $user, $newFriend);

				$event = ($this->networkType == 'friend') ? 'tiki.user.friend.add' : 'tiki.user.follow.add';
				TikiLib::events()->trigger(
					$event,
					array(
						'type' => 'user',
						'object' => $user,
						'user' => $user,
						'follow_id' => $newFriend,
						'aggregate' => $hash,
					)
				);
				TikiLib::events()->trigger(
					$event,
					array(
						'type' => 'user',
						'object' => $newFriend,
						'user' => $newFriend,
						'follow_id' => $user,
						'aggregate' => $hash,
					)
				);
			} else {
				// New request
				$this->addRelation('request', $user, $newFriend);
			}
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('user', $user);
		refresh_index('user', $newFriend);

		$tx->commit();

		return true;
	}

	function approveFriend($user, $newFriend)
	{
		if ($this->networkType != 'follow_approval') {
			return false;
		}

		$request = $this->getRelation('request.invert', $user, $newFriend);

		if ($request) {
			$tx = TikiDb::get()->begin();

			// If there was a pending request by the other side, remove the request
			// and add them as follower
			$this->relationlib->remove_relation($request);
			$this->addRelation('follow.invert', $user, $newFriend);

			TikiLib::events()->trigger(
				'tiki.user.follow.add',
				array(
					'type' => 'user',
					'object' => $newFriend,
					'user' => $newFriend,
					'follow_id' => $user,
				)
			);
			TikiLib::events()->trigger(
				'tiki.user.follow.incoming',
				array(
					'type' => 'user',
					'object' => $user,
					'user' => $user,
					'follow_id' => $newFriend,
				)
			);

			$tx->commit();

			return true;
		}

		return false;
	}

	function removeFriend($user, $oldFriend)
	{
		$follow = $this->getRelation('follow', $user, $oldFriend);
		$followInvert = $this->getRelation('follow.invert', $user, $oldFriend);
		$request = $this->getRelation('request', $user, $oldFriend);
		$requestInvert = $this->getRelation('request.invert', $user, $oldFriend);

		if ($follow) {
			$this->relationlib->remove_relation($follow);

			if ($this->networkType == 'friend') {
				// Friendship breakups are bidirectional, not follow ones
				$this->relationlib->remove_relation($followInvert);
			}
			require_once('lib/search/refresh-functions.php');
			refresh_index('user', $user);
			refresh_index('user', $oldFriend);
			return true;
		} elseif ($request || $requestInvert) {
			$this->relationlib->remove_relation($request);
			$this->relationlib->remove_relation($requestInvert);
			require_once('lib/search/refresh-functions.php');
			refresh_index('user', $user);
			refresh_index('user', $oldFriend);
			return true;
		} else {
			return false;
		}
	}

	private function getRelations($type, $from)
	{
		$relations = $this->relationlib->get_relations_from('user', $from, 'tiki.friend.' . $type);

		return array_map(
			function ($relation) {
				return array(
					'user' => $relation['itemId'],
				);
			}, $relations
		);
	}

	private function addRelation($type, $from, $to)
	{
		return $this->relationlib->add_relation('tiki.friend.' . $type, 'user', $from, 'user', $to);
	}

	private function getRelation($type, $from, $to)
	{
		return $this->relationlib->get_relation_id('tiki.friend.' . $type, 'user', $from, 'user', $to);
	}

	private function createHash($a, $b)
	{
		// Hashing needs constant user ordering, so sort
		if ($a > $b) {
			$t = $b;
			$b = $a;
			$a = $t;
		}

		return sha1("friendrelation/$a/$b");
	}

	function addLike($user, $type, $id)
	{
		$like = $this->getLike($user, $type, $id);

		if (! $like) {
			$this->relationlib->add_relation('tiki.social.like', 'user', $user, $type, $id);
			TikiLib::events()->trigger(
				'tiki.social.like.add',
				array(
					'type' => $type,
					'object' => $id,
					'user' => $user,
				)
			);
			return true;
		}

		return false;
	}

	function removeLike($user, $type, $id)
	{
		$like = $this->getLike($user, $type, $id);

		if ($like) {
			$this->relationlib->remove_relation($like);
			TikiLib::events()->trigger(
				'tiki.social.like.remove',
				array(
					'type' => $type,
					'object' => $id,
					'user' => $user,
				)
			);
			return true;
		}

		return false;
	}

	function getLikes($type, $id)
	{
		$relations = $this->relationlib->get_relations_to($type, $id, 'tiki.social.like');

		return array_map(
			function ($relation) {
				return  $relation['itemId'];
			},
			$relations
		);
	}

	private function getLike($user, $type, $id)
	{
		return $this->relationlib->get_relation_id('tiki.social.like', 'user', $user, $type, $id);
	}
}

