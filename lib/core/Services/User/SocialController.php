<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_SocialController
{
	private $lib;

	function setUp()
	{
		Services_Exception_Disabled::check('feature_friends');

		$this->lib = TikiLib::lib('social');
	}

	function action_list_friends($input)
	{
		global $user;
		$friends = $this->lib->listFriends($user);

		return array(
			'friends' => $friends,
		);
	}

	function action_add_friend($input)
	{
		global $user;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($username = $input->username->email()) {
				$this->lib->addFriend($user, $username);
			}
		}

		return array(
			'title' => tr('Add Friend'),
		);
	}

	function action_remove_friend($input)
	{
		global $user;

		$status = null;
		$username = $input->friend->email();

		if (! $username) {
			throw new Services_Exception_MissingValue('friend');
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->lib->removeFriend($user, $username);
			$status = 'DONE';
		}

		return array(
			'title' => tr('Remove Friend'),
			'status' => $status,
			'friend' => $username,
		);
	}
}

