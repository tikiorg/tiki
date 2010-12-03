<?php

class Search_GlobalSource_CategorySource implements Search_GlobalSource_Interface
{
	private $categlib;
	private $parentCategories = array();

	function __construct()
	{
		global $categlib; require_once 'lib/categories/categlib.php';
		$this->categlib = $categlib;
	}

	function getProvidedFields()
	{
		return array('categories', 'deep_categories');
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$categories = $this->categlib->get_object_categories($objectType, $objectId, -1, false);

		// For forum posts, and 
		if (isset($data['parent_object_id'], $data['parent_object_type'])) {
			$objectType = $data['parent_object_type']->getValue();
			$objectId = $data['parent_object_id']->getValue();

			$parentCategories = $this->categlib->get_object_categories($objectType, $objectId, -1, false);
			$categories = array_unique(array_merge($categories, $parentCategories));
		}

		if (empty($categories)) {
			$categories[] = 'orphan';
		}

		return array(
			'categories' => $typeFactory->multivalue($categories),
			'deep_categories' => $typeFactory->multivalue($this->getWithParent($categories)),
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
			$path = $this->categlib->get_category_path($categId);

			$categories = array();
			foreach($path as $categ) {
				$categories[] = $categ['categId'];
			}

			$this->parentCategories[$categId] = $categories;
		}

		return $this->parentCategories[$categId];
	}
}

