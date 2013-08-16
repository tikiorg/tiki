<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
			TikiLib::events()->trigger('tiki.user.follow.add', array(
				'type' => 'user',
				'object' => $user,
				'user' => $user,
				'follow_id' => $newFriend,
				'aggregate' => $hash,
			));
			TikiLib::events()->trigger('tiki.user.follow.incoming', array(
				'type' => 'user',
				'object' => $newFriend,
				'user' => $newFriend,
				'follow_id' => $user,
				'aggregate' => $hash,
			));
		} elseif($this->networkType == 'follow_approval' || $this->networkType == 'friend') {
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
				TikiLib::events()->trigger($event, array(
					'type' => 'user',
					'object' => $user,
					'user' => $user,
					'follow_id' => $newFriend,
					'aggregate' => $hash,
				));
				TikiLib::events()->trigger($event, array(
					'type' => 'user',
					'object' => $newFriend,
					'user' => $newFriend,
					'follow_id' => $user,
					'aggregate' => $hash,
				));
			} else {
				// New request
				$this->addRelation('request', $user, $newFriend);
			}
		}

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

			TikiLib::events()->trigger('tiki.user.follow.add', array(
				'type' => 'user',
				'object' => $newFriend,
				'user' => $newFriend,
				'follow_id' => $user,
			));
			TikiLib::events()->trigger('tiki.user.follow.incoming', array(
				'type' => 'user',
				'object' => $user,
				'user' => $user,
				'follow_id' => $newFriend,
			));

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
			return true;
		} elseif ($request || $requestInvert) {
			$this->relationlib->remove_relation($request);
			$this->relationlib->remove_relation($requestInvert);
			return true;
		} else {
			return false;
		}
	}

	private function getRelations($type, $from)
	{
		$relations = $this->relationlib->get_relations_from('user', $from, 'tiki.friend.' . $type);
		
		return array_map(function ($relation) {
			return array(
				'user' => $relation['itemId'],
			);
		}, $relations);
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
}

