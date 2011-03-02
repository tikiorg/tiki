<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_PermissionSource implements Search_GlobalSource_Interface
{
	private $perms;
	private $additionalCheck;

	function __construct(Perms $perms, $additionalCheck = null)
	{
		$this->perms = $perms;
		$this->additionalCheck = $additionalCheck;
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

	private function getCheckList($accessor)
	{
		$toCheck = $accessor->getResolver()->applicableGroups();

		if ($this->additionalCheck) {
			$toCheck[] = $this->additionalCheck;
		}

		return $toCheck;
	}
}

