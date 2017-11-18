<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Yaml\Yaml;

class Services_Workspace_ProfileBuilder
{
	private $objects = [];
	private $groups = [];
	private $autojoin = [];
	private $permissions = [];
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
		if (isset($data['categories'])) {
			$list = (array) $data['categories'];
			unset($data['categories']);

			$this->objects[] = [
				'type' => 'categorize',
				'data' => [
					'type' => $type,
					'object' => $this->ref($ref),
					'categories' => $list,
				],
			];
		}

		$this->objects[] = [
			'type' => $type,
			'ref' => $ref,
			'data' => $data,
		];
	}

	function addGroup($internalName, $fullName, $autojoin = false)
	{
		$this->groups[$internalName] = $fullName;
		$this->autojoin[$internalName] = $autojoin;
	}

	function setPermissions($internalName, $type, $objectId, array $permissionList)
	{
		if (! isset($this->permissions[$internalName])) {
			$this->permissions[$internalName] = ['objects' => []];
		}

		$this->permissions[$internalName]['objects'][] = [
			'type' => $type,
			'id' => $objectId,
			'allow' => $permissionList
		];
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
					$builder->setPermissions($group, 'group', $peer, ['group_view', 'group_view_members', 'group_add_member', 'group_remove_member']);
				} else {
					$builder->setPermissions($group, 'group', $peer, ['group_view', 'group_view_members']);
				}
			}
		}

		$data = [];

		if (count($builder->objects)) {
			$data['objects'] = $builder->objects;
		}

		if ($builder->groups) {
			$data['mappings'] = [];
			$data['permissions'] = [];
			foreach ($builder->groups as $internal => $full) {
				$groupDefinition = [
					'description' => $full,
				];

				if ($this->autojoin[$internal]) {
					$groupDefinition['autojoin'] = 'y';
				}

				if (isset($builder->permissions[$internal])) {
					$groupDefinition['objects'] = $builder->permissions[$internal]['objects'];
				}

				$data['mappings'][$internal] = $full;
				$data['permissions'][$internal] = $groupDefinition;
			}
		}

		$self = $this;
		array_walk_recursive(
			$data,
			function (& $entry) use ($self) {
				if (is_string($entry)) {
					$entry = preg_replace_callback(
						'/\{(\w+)\}/',
						function ($matches) use ($self) {
							return $self->user($matches[1]);
						},
						$entry
					);
				}
			}
		);

		$yaml = Yaml::dump($data, 20, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
		return <<<SYNTAX

^The following profile was auto-generated. It may hurt your eyes when you try reading it.^
{CODE(caption="YAML")}
$yaml
{CODE}

SYNTAX;
	}
}
