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
		global $user;
		Services_Exception_Disabled::check('feature_friends');

		if (! $user) {
			throw new Services_Exception_Denied(tr('Must be registered'));
		}

		$this->lib = TikiLib::lib('social');
	}

	function action_list_friends($input)
	{
		global $user;
		$friends = $this->lib->listFriends($user);
		$incoming = $this->lib->listIncomingRequests($user);
		$outgoing = $this->lib->listOutgoingRequests($user);

		return array(
			'friends' => $friends,
			'incoming' => $incoming,
			'outgoing' => $outgoing,
		);
	}

	function action_add_friend($input)
	{
		global $user;

		$username = $input->username->email();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($username) {
				if (! $this->lib->addFriend($user, $username)) {
					throw new Services_Exception_FieldError('username', tr('User not found.'));
				}
			}
		}

		return array(
			'title' => tr('Add Friend'),
			'username' => $username,
		);
	}

	function action_approve_friend($input)
	{
		global $user;

		$username = $input->friend->email();
		$status = null;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($username) {
				$this->lib->approveFriend($user, $username);
				$status = 'DONE';
			}
		}

		return array(
			'title' => tr('Approve Friend'),
			'username' => $username,
			'status' => $status,
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

