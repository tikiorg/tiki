<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_CategorySource implements Search_GlobalSource_Interface, Tiki_Profile_Writer_ReferenceProvider
{
	private $categlib;
	private $parentCategories = array();

	function __construct()
	{
		$this->categlib = TikiLib::lib('categ');
	}

	function getReferenceMap()
	{
		return array(
			'categories' => 'category',
			'deep_categories' => 'category',
		);
	}

	function getProvidedFields()
	{
		return array('categories', 'deep_categories');
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$categories = $this->categlib->get_object_categories($objectType, $objectId, -1, false);

		// For forum posts, and 
		if (isset($data['parent_object_id'], $data['parent_object_type'])) {
			$objectType = is_object($data['parent_object_type']) ? $data['parent_object_type']->getValue() : $data['parent_object_type'];
			$objectId = is_object($data['parent_object_id']) ? $data['parent_object_id']->getValue() : $data['parent_object_id'];

			$parentCategories = $this->categlib->get_object_categories($objectType, $objectId, -1, false);
			$categories = array_unique(array_merge($categories, $parentCategories));
		}

		if (empty($categories)) {
			$categories[] = 'orphan';
			$deepcategories = $categories;
		} else {
			$deepcategories = $this->getWithParent($categories);
		}

		return array(
			'categories' => $typeFactory->multivalue($categories),
			'deep_categories' => $typeFactory->multivalue($deepcategories),
		);
	}

	private function getWithParent($categories)
	{
		$full = array();

		foreach ($categories as $category) {
			$full = array_merge($full, $this->getParents($category));
		}

		return array_unique($full);
	}

	private function getParents($categId)
	{
		if (! isset($this->parentCategories[$categId])) {
			$category = $this->categlib->get_category($categId);
			$this->parentCategories[$categId] = array_keys($category['tepath']);
		}

		return $this->parentCategories[$categId];
	}
}

