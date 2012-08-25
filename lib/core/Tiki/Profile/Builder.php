<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Builder
{
	private $objects = array();
	private $groups = array();
	private $permissions = array();
	private $managingGroup;

	function ref($name)
	{
		return '$' . $name;
	}

	function user($name)
	{
		return '$profilerequest:' . $name . '$undefined$';
	}
	
	function addObject($type, $ref, array $data)
	{
		$this->objects[] = array(
			'type' => $type,
			'ref' => $ref,
			'data' => $data,
		);
	}

	function addGroup($internalName, $fullName)
	{
		$this->groups[$internalName] = $fullName;
	}

	function setPermissions($internalName, $type, $objectId, array $permissionList)
	{
		if (! isset($this->permissions[$internalName])) {
			$this->permissions[$internalName] = array('objects' => array());
		}

		$this->permissions[$internalName]['objects'][] = array(
			'type' => $type,
			'id' => $objectId,
			'allow' => $permissionList
		);
	}

	function setManagingGroup($group)
	{
		$this->managingGroup = $group;
	}

	function getContent()
	{
		$builder = clone $this;

		foreach (array_keys($this->groups) as $group) {
			foreach (array_keys($this->groups) as $peer) {
				if ($group == $this->managingGroup) {
					$builder->setPermissions($group, 'group', $peer, array('group_view', 'group_view_members', 'group_add_member', 'group_remove_member'));
				} else {
					$builder->setPermissions($group, 'group', $peer, array('group_view', 'group_view_members'));
				}
			}
		}

		$data = array();
		
		if (count($builder->objects)) {
			$data['objects'] = $builder->objects;
		}

		if ($builder->groups) {
			$data['mappings'] = array();
			$data['permissions'] = array();
			foreach ($builder->groups as $internal => $full) {
				$groupDefinition = array(
					'description' => $full,
				);

				if (isset($builder->permissions[$internal])) {
					$groupDefinition['objects'] = $builder->permissions[$internal]['objects'];
				}

				$data['mappings'][$internal] = $full;
				$data['permissions'][$internal] = $groupDefinition;
			}
		}

		$yaml = Horde_Yaml::dump($data);
		return <<<SYNTAX

^The following profile was auto-generated. It may hurt your eyes when you try reading it.^
{CODE(caption="YAML")}
$yaml
{CODE}

SYNTAX;
	}
}

