<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class SocialLib
{
	private $relationlib;

	function __construct()
	{
		$this->relationlib = TikiLib::lib('relation');
	}

	function listFriends($user)
	{
		$relations = $this->relationlib->get_relations_from('user', $user, 'tiki.friend.follow');
		
		return array_map(function ($relation) {
			return array(
				'user' => $relation['itemId'],
			);
		}, $relations);
	}

	function addFriend($user, $newFriend)
	{
		$this->relationlib->add_relation('tiki.friend.follow', 'user', $user, 'user', $newFriend);
	}

	function removeFriend($user, $oldFriend)
	{
		$relation = $this->relationlib->get_relation_id('tiki.friend.follow', 'user', $user, 'user', $oldFriend);

		if ($relation) {
			$this->relationlib->remove_relation($relation);
			return true;
		} else {
			return false;
		}
	}
}

