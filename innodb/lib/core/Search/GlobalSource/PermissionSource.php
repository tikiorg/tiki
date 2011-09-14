<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		return array('allowed_groups');
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$groups = array();

		if (isset($data['view_permission'])) {
			$viewPermission = is_object($data['view_permission']) ? $data['view_permission']->getValue() : $data['view_permission'];

			$groups = array_merge($groups, $this->getAllowedGroups($objectType, $objectId, $viewPermission));
		}
		
		if (isset($data['parent_view_permission'], $data['parent_object_id'], $data['parent_object_type'])) {
			$viewPermission = is_object($data['parent_view_permission']) ? $data['parent_view_permission']->getValue() : $data['parent_view_permission'];
			$objectType = is_object($data['parent_object_type']) ? $data['parent_object_type']->getValue() : $data['parent_object_type'];
			$objectId = is_object($data['parent_object_id']) ? $data['parent_object_id']->getValue() : $data['parent_object_id'];

			$groups = array_merge($groups, $this->getAllowedGroups($objectType, $objectId, $viewPermission));
		}

		// Used for comments - must see the parent view permission in addition to a global permission to view comments
		if (isset($data['global_view_permission'])) {
			$globalPermission = $data['global_view_permission'];
			$globalPermission = is_object($globalPermission) ? $globalPermission->getValue() : $globalPermission;
			$groups = $this->getGroupExpansion($groups);
			$groups = $this->filterWithGlobalPermission($groups, $globalPermission);
		}

		return array(
			'allowed_groups' => $typeFactory->multivalue(array_unique($groups)),
		);
	}
	
	private function getAllowedGroups($objectType, $objectId, $viewPermission)
	{
		$accessor = $this->perms->getAccessor(array(
			'type' => $objectType,
			'object' => $objectId,
		));

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

