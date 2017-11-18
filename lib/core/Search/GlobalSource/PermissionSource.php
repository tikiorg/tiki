<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		return ['allowed_groups', 'allowed_users'];
	}

	function getGlobalFields()
	{
		return [];
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = [])
	{

		if (! empty($data['_extra_users'])) {
			$allowed_users = $data['_extra_users'];
		} else {
			$allowed_users = [];
		}

		if (isset($data['allowed_groups'])) {
			return ['allowed_users' => $typeFactory->multivalue(array_unique($allowed_users))];
		}

		$groups = [];

		if (isset($data['view_permission'])) {
			$viewPermission = is_object($data['view_permission']) ? $data['view_permission']->getValue() : $data['view_permission'];

			if (isset($data['_permission_accessor'])) {
				$accessor = $data['_permission_accessor'];
			} else {
				$accessor = $this->perms->getAccessor(
					[
						'type' => $objectType,
						'object' => $objectId,
					]
				);
			}

			$groups = array_merge($groups, $this->getAllowedGroups($accessor, $viewPermission));
		}

		if (isset($data['parent_view_permission'], $data['parent_object_id'], $data['parent_object_type'])) {
			$viewPermission = is_object($data['parent_view_permission']) ? $data['parent_view_permission']->getValue() : $data['parent_view_permission'];
			$accessor = $this->perms->getAccessor(
				[
					'type' => $data['parent_object_type']->getValue(),
					'object' => $data['parent_object_id']->getValue(),
				]
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

		return [
			'allowed_groups' => $typeFactory->multivalue(array_unique($groups)),
			'allowed_users' => $typeFactory->multivalue(array_unique($allowed_users)),
		];
	}

	private function getAllowedGroups($accessor, $viewPermission)
	{
		$groups = [];
		foreach ($this->getCheckList($accessor) as $groupName) {
			$accessor->setGroups([$groupName]);

			if ($accessor->$viewPermission) {
				$groups[] = $groupName;
			}
		}

		return $groups;
	}

	private function filterWithGlobalPermission($groups, $permission)
	{
		$out = [];
		$accessor = $this->perms->getAccessor();

		foreach ($groups as $group) {
			$accessor->setGroups([$group]);

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
		static $expansions = [];
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
