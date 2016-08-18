<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_Builder
{
	private $prefix = 'tiki_p_';

	private $categories = false;
	private $definitions = array();
	private $adminPermissionMap = array();
	private $globalOnlyPermissions = array();

	function build()
	{
		$alternateCheck = new Perms_Check_Alternate('admin');
		$fixedCheck = new Perms_Check_Fixed($this->globalOnlyPermissions);

		$perms = new Perms;
		$perms->setPrefix($this->prefix);
		$perms->setCheckSequence($this->getSequence($alternateCheck, $fixedCheck));
		$perms->setResolverFactories($this->getFactories());

		$accessor = $perms->getAccessor(array());
		$alternateCheck->setResolver($accessor->getResolver());
		$fixedCheck->setResolver($accessor->getResolver());

		return $perms;
	}

	function withCategories($with = true)
	{
		$this->categories = (bool) $with;
		return $this;
	}

	function withDefinitions($definitions = array())
	{
		$this->definitions = $definitions;

		$adminPermissions = array();

		foreach ($definitions as $row) {
			$permName = $row['name'];
			if ($row['admin']) {
				$adminPermissions[ $row['type'] ] = substr($permName, strlen($this->prefix));
			}
		}

		// Create a map from the permission to the admin permission
		foreach ($definitions as $row) {
			$permName = $row['name'];
			$type = $row['type'];
			if (isset($adminPermissions[$type]) && ! $row['admin']) {
				$permName = substr($permName, strlen($this->prefix));
				$this->adminPermissionMap[$permName] = $adminPermissions[$type];
			}
		}

		foreach ($definitions as $row) {
			if ($row['scope'] == 'global') {
				$this->globalOnlyPermissions[] = substr($row['name'], strlen($this->prefix));
			}
		}

		return $this;
	}

	private function getSequence($alternate, $fixed)
	{
		$args = func_get_args();

		$args[] = new Perms_Check_Direct;
		$args[] = new Perms_Check_Indirect($this->adminPermissionMap);

		return $args;
	}

	private function getFactories()
	{
		$factories = array(
			new Perms_ResolverFactory_ObjectFactory,
		);

		if ($this->categories) {
			$factories[] = new Perms_ResolverFactory_CategoryFactory;
		}

		$factories[] = new Perms_ResolverFactory_GlobalFactory;

		return $factories;
	}
}

