<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Category_Manipulator
{
	private $objectType;
	private $objectId;

	private $current = array();
	private $managed = array();
	private $unmanaged = array();
	private $constraints = array(
		'required' => array(),
	);
	private $new = array();

	private $prepared = false;
	private $overrides = array();
	private $overrideAll = false;

	function __construct($objectType, $objectId)
	{
		$this->objectType = $objectType;
		$this->objectId = $objectId;
	}

	function addRequiredSet(array $categories, $default, $filter=null, $type=null)
	{
		$categories = array_unique($categories);
		$this->constraints['required'][] = array(
			'set' => $categories,
			'default' => $default,
			'filter' => $filter,
			'type' => $type
		);
	}

	function overrideChecks()
	{
		$this->overrideAll = true;
	}

	function setCurrentCategories(array $categories)
	{
		$this->current = $categories;
	}

	function setManagedCategories(array $categories)
	{
		$this->managed = $categories;
	}

	function setUnmanagedCategories(array $categories)
	{
		$this->unmanaged = $categories;
	}

	function setNewCategories(array $categories)
	{
		$this->new = $categories;
	}

	function getAddedCategories()
	{
		$this->prepare();

		$attempt = array_diff($this->new, $this->current);
		return $this->filter($attempt, 'add_object');
	}

	function getRemovedCategories()
	{
		$this->prepare();

		$attempt = array_diff($this->current, $this->new);
		return $this->filter($attempt, 'remove_object');
	}

	
	/*
	 * Check wether the given permission is allowed for the given categories.
	 * Note: The group in question requires also the _global_ permission 'modify_object_categories'.
	 * @param array $categories - requested categories
	 * @param string  $permission - required permission for that category. Ie. 'add_category'
	 * @return array $authorizedCategories - filterd list of given $categories that have proper permissions set.
	 */
	private function filter( $categories, $permission )
	{
		$objectperms = Perms::get(array('type' => $this->objectType, 'object' => $this->objectId));
		$canModifyObject = $objectperms->modify_object_categories;
		
		$out = array();
		foreach ($categories as $categ) {
			$perms = Perms::get(array('type' => 'category', 'object' => $categ));
			$hasCategoryPermission = $perms->$permission;

			if ($this->overrideAll || ($canModifyObject && $hasCategoryPermission) || in_array($categ, $this->overrides)) {
				$out[] = $categ;
			}
		}

		return $out;
	}


	private function prepare()
	{
		if ($this->prepared) {
			return;
		}

		$categories = $this->managed;
		Perms::bulk(array('type' => 'category'), 'object', $categories);

		if (count($this->managed)) {
			$base = array_diff($this->current, $this->managed);
			$managed = array_intersect($this->new, $this->managed);
			$this->new = array_merge($base, $managed);
		}
		
		if (count($this->unmanaged)) {
			$base = array_intersect($this->current, $this->unmanaged);
			$managed = array_diff($this->new, $this->unmanaged);
			$this->new = array_merge($base, $managed);
		}

		$this->applyConstraints();

		$this->prepared = true;
	}

	private function applyConstraints()
	{
		foreach ($this->constraints['required'] as $constraint) {
			$set = $constraint['set'];
			$default = $constraint['default'];
			$filter = $constraint['filter'];
			$type = $constraint['type'];

			$interim = array_intersect($this->new, $set);

			if (!empty($type) && $type != $this->objectType) {
				return;
			}
				
			if (!empty($filter)) {
				$objectlib = TikiLib::lib('object');
				$info = $objectlib->get_info($this->objectType, $this->objectId);
				if (!preg_match($filter, $info['title'])) {
					return;
				}
			}

			if (count($interim) == 0 && ! in_array($default, $this->new)) {
				$this->new[] = $default;
				$this->overrides[] = $default;
			}
		}
	}
}
