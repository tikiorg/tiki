<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		// Checks if the username param was passed, if so return that user's friend list
		// otherwise it returns the active user's friend list
		if(empty($input->username->text()) || $input->username->text() == $user) {
			$username = $user;
			$incoming = $this->lib->listIncomingRequests($username);
			$outgoing = $this->lib->listOutgoingRequests($username);
		} else {
			$username = $input->username->text();
		}

		if(empty($input->show_add_friend->text())) {
			$show_add_friend = 'y';
		} else {
			$show_add_friend = $input->show_add_friend->text();
		}

		$friends = $this->lib->listFriends($username);

		return array(
			'title' => tr('Friend List'),
			'friends' => $friends,
			'incoming' => $incoming,
			'outgoing' => $outgoing,
			'showbutton' => $show_add_friend,
			'username' => $username,
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

	function action_like($input)
	{
		global $user;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->lib->addLike($user, $input->type->text(), $input->id->none());
		}

		return array();
	}

	function action_unlike($input)
	{
		global $user;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->lib->removeLike($user, $input->type->text(), $input->id->none());
		}

		return array();
	}
}

