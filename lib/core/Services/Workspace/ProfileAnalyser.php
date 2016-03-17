<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Workspace_ProfileAnalyser
{
	private $profile;
	private $builder;

	function __construct(Tiki_Profile $profile)
	{
		$this->profile = $profile;
		$this->builder = new Services_Workspace_ProfileBuilder;
	}

	function ref($name)
	{
		return $this->builder->ref($name);
	}

	function user($name)
	{
		return $this->builder->user($name);
	}

	function contains(array $conditions)
	{
		$objects = $this->profile->getObjects();

		if (isset($conditions['type'])) {
			$objects = array_filter(
				$objects,
				function ($object) use ($conditions)
				{
					return $conditions['type'] == $object->getType();
				}
			);
			unset($conditions['type']);
		}

		if (isset($conditions['ref'])) {
			$objects = array_filter(
				$objects,
				function ($object) use ($conditions)
				{
					return $conditions['ref'] === $object->getRef();
				}
			);
			unset($conditions['ref']);
		}

		foreach ($conditions as $condition => $value) {
			$objects = array_filter(
				$objects,
				function ($object) use ($condition, $value)
				{
					$data = $object->getData();
					return isset($data[$condition]) && $data[$condition] === $value;
				}
			);
		}

		return count($objects) > 0;
	}

	/**
	 * Provides group information using the permission details from a specific object.
	 */
	function getGroups($type, $object)
	{
		$out = array();

		$groupMap = $this->profile->getGroupMap();
		$permissions =  $this->profile->getPermissions();

		foreach ($groupMap as $key => $name) {
			$out[$key] = array(
				'name' => $name,
				'managing' => false,
				'autojoin' => true,
				'permissions' => array(),
			);

			if (isset($permissions[$key])) {
				$related = $permissions[$key];
				$out[$key]['managing'] = $this->isManagingGroup($related['objects']);
				$out[$key]['autojoin'] = $this->isAutojoin($related['general']);
				$out[$key]['permissions'] = $this->getObjectPermissions($related['objects'], $type, $object);
			}
		}

		return $this->simplify($out);
	}

	function getObjects($type, $default = null)
	{
		$out = array();

		foreach ($this->profile->getObjects() as $object) {
			if ($object->getType() == $type) {
				$out[] = $object->getData();
			}
		}

		if (! count($out) && is_array($default)) {
			$out[] = $default;
		}

		return $this->simplify($out);
	}

	private function simplify($data)
	{
		array_walk_recursive(
			$data,
			function (& $entry)
			{
				if (is_string($entry)) {
					$entry = preg_replace('/\$profilerequest:(\w+)\$[^\$]*\$/', '{$1}', $entry);
				}
			}
		);

		return $data;
	}

	private function isManagingGroup($objects)
	{
		foreach ($objects as $o) {
			if ($o['type'] == 'group' && in_array('group_add_member', $o['allow'])) {
				return true;
			}
		}

		return false;
	}

	private function isAutojoin($general)
	{
		if (! isset($general['autojoin'])) {
			return false;
		}

		return $general['autojoin'] === true || $general['autojoin'] === 'y';
	}

	private function getObjectPermissions($objects, $type, $object)
	{
		foreach ($objects as $o) {
			if ($o['type'] == $type && $o['id'] == $object) {
				return $o['allow'];
			}
		}

		return array();
	}
}

