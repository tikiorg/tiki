<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		foreach ($this->getUsers($data, $objectType, $objectId) as $user) {
			$groups = array_merge($groups, $this->userlib->get_user_groups_inclusion($user));
			$userfollowers = $this->getFollowers($user);
			if (is_array($userfollowers)) {
				$followers = array_merge($followers, $userfollowers);	
			}
		}

		unset($groups['Anonymous'], $groups['Registered']);

		return array(
			'user_groups' => $typeFactory->multivalue(array_keys($groups)),
			'user_followers' => $typeFactory->multivalue(array_unique($followers)),
		);
	}

	private function getUsers($data, $objectType, $objectId)
	{
		$users = array();
		if (isset($data['contributors'])) {
			$users = $this->read($data['contributors']);
		}

		if (isset($data['user'])) {
			$users[] = $this->read($data['user']);
		}

		if ($objectType == 'user') {
			$users[] = $objectId;
		}

		return $users;
	}

	private function read($value)
	{
		if (! $value instanceof Search_Type_Interface) {
			return $value;
		} elseif ($value instanceof Search_Type_MultivalueText) {
			return $value->getRawValue();
		} else {
			return $value->getValue();
		}
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

