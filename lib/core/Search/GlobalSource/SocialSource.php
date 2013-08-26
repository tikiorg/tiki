<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_SocialSource implements Search_GlobalSource_Interface
{
	private $sociallib;
	private $userlib;

	function __construct()
	{
		$this->sociallib = TikiLib::lib('social');
		$this->userlib = TikiLib::lib('user');
	}

	function getProvidedFields()
	{
		return array(
			'user_groups',
			'user_followers',
		);
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$groups = array();
		$followers = array();

		foreach ($this->getUsers($data) as $user) {
			$groups = array_merge($groups, $this->userlib->get_user_groups_inclusion($user));
			$userfollowers = $this->getFollowers($user);
			if (is_array($userfollowers)) {
				$followers += $this->getFollowers($user);
			}
		}

		unset($groups['Anonymous'], $groups['Registered']);

		return array(
			'user_groups' => $typeFactory->multivalue(array_keys($groups)),
			'user_followers' => $typeFactory->multivalue(array_unique($followers)),
		);
	}

	private function getUsers($data)
	{
		$users = array();
		if (isset($data['contributors'])) {
			if ($data['contributors'] instanceof Search_Type_MultivalueText) {
				$users = $data['contributors']->getRawValue();
			} else {
				$users = $data['contributors']->getValue();
			}
		}

		if (isset($data['user'])) {
			$users[] = $data['user']->getValue();
		}

		return $users;
	}

	private function getFollowers($user)
	{
		static $localCache = array();

		if (! isset($localCache[$user])) {
			$list = $this->sociallib->listFollowers($user);
			$localCache[$user] = array_map(
				function ($entry) {
					return $entry['user'];
				},
				$list
			);
		}

		return $localCache[$user];
	}
}

