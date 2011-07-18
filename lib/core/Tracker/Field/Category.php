<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Category
 * 
 * Letter key: ~e~
 *
 */
class Tracker_Field_Category extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'e' => array(
				'name' => tr('Category'),
				'description' => tr('Allows for one or multiple categories under the specified main category to be affected to the tracker item.'),
				'params' => array(
					'parentId' => array(
						'name' => tr('Parent Category'),
						'description' => tr('Child categories will be provided as options for the field.'),
						'filter' => 'int',
					),
					'inputtype' => array(
						'name' => tr('Input Type'),
						'description' => tr('User interface control to be used.'),
						'default' => 'd',
						'filter' => 'alpha',
						'options' => array(
							'd' => tr('Drop Down'),
							'radio' => tr('Radio buttons'),
							'm' => tr('List box'),
							'checkbox' => tr('Multiple-selection check-boxes'),
						),
					),
					'selectall' => array(
						'name' => tr('Select All'),
						'description' => tr('Includes a control to select all available options for multi-selection controls.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No controls'),
							1 => tr('Include controls'),
						),
					),
					'descendants' => array(
						'name' => tr('All descendants'),
						'description' => tr('Display all descendants instead of only first-level ones'),
						'filter' => 'int',
						'options' => array(
							0 => tr('First level only'),
							1 => tr('All descendants'),
						),
					),
					'help' => array(
						'name' => tr('Help'),
						'description' => tr('Displays the field description in a help tooltip.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No help'),
							1 => tr('Tooltip'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$key = 'ins_' . $this->getConfiguration('fieldId');
		$parentId = $this->getOption(0);

		if (isset($requestData[$key]) && is_array($requestData[$key])) {
			$selected = $requestData[$key];
		} elseif (!empty($requestData)) {
			$selected = array();		
		} else {
			$selected = $this->getCategories();
		}

		$categories = $this->getApplicableCategories();
		$selected = array_intersect($selected, $this->getIds($categories));

		$data = array(
			'value' => implode(',', $selected),
			'selected_categories' => $selected,
			$parentId => $categories,	// TODO kil?
			'list' => $categories,
			'cat' => array(),
			'categs' => array(),
		);

		foreach($data[$parentId] as $category) {
			$id = $category['categId'];
			if (in_array($id, $selected)) {
				$data['cat'][$id] = 'y';
				$data['categs'][] = $category;
			}
		}

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/category.tpl', $context);
	}

	function renderInnerOutput($context = array())
	{
		$selected_categories = $this->getConfiguration('selected_categories');
		$categories = $this->getConfiguration('list');
		$ret = '';
		foreach ($selected_categories as $categId) {
			if (!empty($ret))
				$ret .= '<br />';
			foreach ($categories as $category) {
				if ($category['categId'] == $categId) {
					$ret .= $category['name'];
					break;
				}
			}
		}
		return $ret;
	}

	function handleSave($value, $oldValue)
	{
		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
		$old = array_filter(explode(',', $old));
		$new = array_filter(explode(',', $new));

		$output = $this->getConfiguration('name') . ":\n";

		$new_categs = array_diff($new, $old);
		$del_categs = array_diff($old, $new);
		$remain_categs = array_diff($new, $new_categs);

		if (count($new_categs) > 0) {
			$output .= "  -[Added]-:\n";
			$output .= $this->describeCategoryList($new_categs);
		}
		if (count($del_categs) > 0) {
			$output .= "  -[Removed]-:\n";
			$output .= $this->describeCategoryList($del_categs);
		}
		if (count($remain_categs) > 0) {
			$output .= "  -[Remaining]-:\n";
			$output .= $this->describeCategoryList($remain_categs);
		}

		return $output;
	}
	
	private function describeCategoryList($categs) {
	    $categlib = TikiLib::lib('categ');
	    $res = '';
	    foreach ($categs as $cid) {
			$info = $categlib->get_category($cid);
			$res .= '    ' . $info['name'] . "\n";
	    }
	    return $res;
	}

	private function getIds($categories)
	{
		$validIds = array();
		foreach ($categories as $c) {
			$validIds[] = $c['categId'];
		}

		return $validIds;
	}

	private function getApplicableCategories()
	{
		$parentId = $this->getOption(0);
		$descends = $this->getOption(3) == 1;

		return TikiLib::lib('categ')->get_viewable_child_categories($parentId, $descends);
	}

	private function getCategories()
	{
		return TikiLib::lib('categ')->get_object_categories('trackeritem', $this->getItemId());
	}
}

