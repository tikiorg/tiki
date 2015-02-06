<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_PermissionSource implements Search_GlobalSource_Interface
{
	private $perms;

	function __construct(Perms $perms)
	{
		$this->perms = $perms;
	}

	function getProvidedFields()
	{
		return array('allowed_groups', 'allowed_users');
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{

		if (!empty($data['_extra_users'])) {
			$allowed_users = $data['_extra_users'];
		} else {
			$allowed_users = array();
		}

		if (isset($data['allowed_groups'])) {
			return array('allowed_users' => $typeFactory->multivalue(array_unique($allowed_users)));
		}

		$groups = array();

		if (isset($data['view_permission'])) {
			$viewPermission = is_object($data['view_permission']) ? $data['view_permission']->getValue() : $data['view_permission'];

			if (isset($data['_permission_accessor'])) {
				$accessor = $data['_permission_accessor'];
			} else {
				$accessor = $this->perms->getAccessor(
					array(
						'type' => $objectType,
						'object' => $objectId,
					)
				);
			}

			$groups = array_merge($groups, $this->getAllowedGroups($accessor, $viewPermission));
		}

		if (isset($data['parent_view_permission'], $data['parent_object_id'], $data['parent_object_type'])) {
			$viewPermission = is_object($data['parent_view_permission']) ? $data['parent_view_permission']->getValue() : $data['parent_view_permission'];
			$accessor = $this->perms->getAccessor(
				array(
					'type' => $data['parent_object_type']->getValue(),
					'object' => $data['parent_object_id']->getValue(),
				)
			);

			$groups = array_merge($groups, $this->getAllowedGroups($accessor, $viewPermission));
		}

		// Used for comments - must see the parent view permission in addition to a global permission to view comments
		if (isset($data['global_view_permission'])) {
			$globalPermission = $data['global_view_permission'];
			$globalPermission = $globalPermission->getValue();
			$groups = $this->getGroupExpansion($groups);
			$groups = $this->filterWithGlobalPermission($groups, $globalPermission);
		}

		if (! empty($data['_extra_groups'])) {
			$groups = array_merge($groups, $data['_extra_groups']);
		}

		return array(
			'allowed_groups' => $typeFactory->multivalue(array_unique($groups)),
			'allowed_users' => $typeFactory->multivalue(array_unique($allowed_users)),
		);
	}

	private function getAllowedGroups($accessor, $viewPermission)
	{
		$groups = array();
		foreach ($this->getCheckList($accessor) as $groupName) {
			$accessor->setGroups(array($groupName));

			if ($accessor->$viewPermission) {
				$groups[] = $groupName;
			}
		}

		return $groups;
	}

	private function filterWithGlobalPermission($groups, $permission)
	{
		$out = array();
		$accessor = $this->perms->getAccessor();

		foreach ($groups as $group) {
			$accessor->setGroups(array($group));

			if ($accessor->$permission) {
				$out[] = $group;
			}
		}

		return $out;
	}

	private function getCheckList($accessor)
	{
		$toCheck = $accessor->applicableGroups();

		return $toCheck;
	}

	private function getGroupExpansion($groups)
	{
		static $expansions = array();
		$tikilib = TikiLib::lib('tiki');

		$out = $groups;

		foreach ($groups as $group) {
			if (! isset($expansions[$group])) {
				$expansions[$group] = $tikilib->get_groups_all($group);
			}

			$out = array_merge($out, $expansions[$group]);
		}

		return $out;
	}
}

