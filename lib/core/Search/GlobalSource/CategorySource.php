<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_CategorySource implements Search_GlobalSource_Interface, Tiki_Profile_Writer_ReferenceProvider, Search_FacetProvider_Interface
{
	private $categlib;

	function __construct()
	{
		$this->categlib = TikiLib::lib('categ');
	}

	function getFacets()
	{
		$facets = array(
			Search_Query_Facet_Term::fromField('deep_categories')
				->setLabel(tr('Category Tree'))
				->setRenderCallback(array($this->categlib, 'get_category_name')),
			Search_Query_Facet_Term::fromField('categories')
				->setLabel(tr('Categories'))
				->setRenderCallback(array($this->categlib, 'get_category_name')),
		);

		foreach ($this->categlib->getCustomFacets() as $categId) {
			$facets[] = Search_Query_Facet_Term::fromField("categories_under_{$categId}")
				->setLabel($this->categlib->get_category_name($categId))
				->setRenderCallback(array($this->categlib, 'get_category_name'));
			$facets[] = Search_Query_Facet_Term::fromField("deep_categories_under_{$categId}")
				->setLabel(tr('%0 (Tree)', $this->categlib->get_category_name($categId)))
				->setRenderCallback(array($this->categlib, 'get_category_name'));
		}

		return $facets;
	}

	function getReferenceMap()
	{
		$list = array(
			'categories' => 'category',
			'deep_categories' => 'category',
		);
		foreach ($this->categlib->getCustomFacets() as $categId) {
			$list["categories_under_{$categId}"] = 'category';
			$list["deep_categories_under_{$categId}"] = 'category';
		}

		return $list;
	}

	function getProvidedFields()
	{
		$list = array('categories', 'deep_categories');
		foreach ($this->categlib->getCustomFacets() as $categId) {
			$list[] = "categories_under_{$categId}";
			$list[] = "deep_categories_under_{$categId}";
		}

		return $list;
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		if (isset($data['categories']) || isset($data['deep_categories'])) {
			return array();
		}

		$categories = $this->categlib->get_object_categories($objectType, $objectId, -1, false);

		// For forum posts, and
		if (isset($data['parent_object_id'], $data['parent_object_type'])) {
			$objectType = is_object($data['parent_object_type']) ? $data['parent_object_type']->getValue() : $data['parent_object_type'];
			$objectId = is_object($data['parent_object_id']) ? $data['parent_object_id']->getValue() : $data['parent_object_id'];

			$parentCategories = $this->categlib->get_object_categories($objectType, $objectId, -1, false);
			$categories = array_unique(array_merge($categories, $parentCategories));
		}


		if ($objectType === 'category') {
			$parentId = $objectId;
			$deepcategories = [];
			while ($parentId = $this->categlib->get_category_parent($parentId)) {
				$deepcategories[] = $parentId;
			}
			if ($deepcategories) {
				$categories[] = $deepcategories[0];
			}

		} else if (empty($categories)) {
			$categories[] = 'orphan';
			$deepcategories = $categories;
		} else {
			$deepcategories = $this->getWithParent($categories);
		}

		$out = array(
			'categories' => $typeFactory->multivalue($categories),
			'deep_categories' => $typeFactory->multivalue($deepcategories),
		);

		foreach ($this->categlib->getCustomFacets() as $rootId) {
			$filtered = array_filter(
				$categories,
				function ($category) use ($rootId) {
					return $this->categlib->get_category_parent($category) == $rootId;
				}
			);
			$deepfiltered = array_filter(
				$deepcategories,
				function ($category) use ($rootId) {
					return $category != $rootId && $this->hasParent($category, $rootId);
				}
			);

			$out["categories_under_{$rootId}"] = $typeFactory->multivalue($filtered);
			$out["deep_categories_under_{$rootId}"] = $typeFactory->multivalue($deepfiltered);
		}

		return $out;
	}

	private function getWithParent($categories)
	{
		return $this->categlib->get_with_parents($categories);
	}

	private function hasParent($category, $parent)
	{
		if ($category == 'orphan') {
			return false;
		}

		$parents = $this->categlib->get_parents($category);
		return in_array($parent, $parents);
	}
}

