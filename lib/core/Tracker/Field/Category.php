<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_Category extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable
{
	public static function getTypes()
	{
		return array(
			'e' => array(
				'name' => tr('Category'),
				'description' => tr('Allows for one or multiple categories under the specified main category to be affected to the tracker item.'),
				'help' => 'Category Tracker Field',
				'prefs' => array('trackerfield_category', 'feature_categories'),
				'tags' => array('advanced'),
				'default' => 'y',
				'params' => array(
					'parentId' => array(
						'name' => tr('Parent Category'),
						'description' => tr('Child categories will be provided as options for the field.'),
						'filter' => 'int',
						'legacy_index' => 0,
						'profile_reference' => 'category',
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
						'legacy_index' => 1,
					),
					'selectall' => array(
						'name' => tr('Select All'),
						'description' => tr('Includes a control to select all available options for multi-selection controls.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No controls'),
							1 => tr('Include controls'),
						),
						'legacy_index' => 2,
					),
					'descendants' => array(
						'name' => tr('All descendants'),
						'description' => tr('Display all descendants instead of only first-level ones'),
						'filter' => 'int',
						'options' => array(
							0 => tr('First level only'),
							1 => tr('All descendants'),
							2 => tr('All descendants and display full path'),
						),
						'legacy_index' => 3,
					),
					'help' => array(
						'name' => tr('Help'),
						'description' => tr('Displays the field description in a help tooltip.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No help'),
							1 => tr('Tooltip'),
						),
						'legacy_index' => 4,
					),
					'outputtype' => array(
						'name' => tr('Output Type'),
						'description' => tr('.'),
						'filter' => 'word',
						'options' => array(
							'' => tr('Plain list separate by line breaks (default)'),
							'links' => tr('Links separate by line breaks'),
							'ul' => tr('Unordered list of labels'),
							'ulinks' => tr('Unordered list of links'),
						),
					),
				),
			),
		);
	}

	public function getFieldData(array $requestData = array())
	{
		$key = 'ins_' . $this->getConfiguration('fieldId');
		$parentId = $this->getOption('parentId');

		if (isset($requestData[$key]) && is_array($requestData[$key])) {
			$selected = $requestData[$key];
		} else if (isset($requestData['cat_managed'])) {
			$selected = array();
		} elseif ($this->getItemId() && !isset($requestData[$key])) {
			// only show existing category of not receiving request, otherwise might be uncategorization in progress
			$selected = $this->getCategories($this->getItemId());
		} else {
			$selected = TikiLib::lib('categ')->get_default_categories();
		}

		$categories = $this->getApplicableCategories();
		$selected = array_intersect($selected, $this->getIds($categories));

		$data = array(
			'value' => implode(',', $selected),
			'selected_categories' => $selected,
			'list' => $categories,
		);

		return $data;
	}

	public function renderInput($context = array())
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->assign('cat_tree', array());
		if ($this->getOption('descendants') > 0 && $this->getOption('inputtype') === 'checkbox') {
			$categories = $this->getConfiguration('list');
			$selected_categories = $this->getConfiguration('selected_categories');
			$smarty->assign_by_ref('categories', $categories);
			$cat_tree = TikiLib::lib('categ')->generate_cat_tree($categories, true, $selected_categories);
			$cat_tree = str_replace('name="cat_categories[]"', 'name="' . $this->getInsertId() . '[]"', $cat_tree);
			$smarty->assign('cat_tree', $cat_tree);
		}
		return $this->renderTemplate('trackerinput/category.tpl', $context);
	}

	public function renderInnerOutput($context = array())
	{
		$selected_categories = $this->getConfiguration('selected_categories');
		$categories = $this->getConfiguration('list');
		$ret = array();
		foreach ($selected_categories as $categId) {
			foreach ($categories as $category) {
				if ($category['categId'] == $categId) {
					if ($this->getOption('descendants') == 2) {
						$str = $category['relativePathString'];
					} else {
						$str = $category['name'];
					}
					if (strpos($this->getOption('outputtype'), 'links') !== false) {
						TikiLib::lib('smarty')->loadPlugin('smarty_modifier_sefurl');
						$deep = $this->getOption('descendants') != 0;
						$href = smarty_modifier_sefurl($categId, 'category', $deep, '', 'y', $str);
						if ($deep) {
							$href .= 'deep=on';
						}
						$str = "<a href=\"$href\">$str</a>";
					}
					$ret[] = $str;
					break;
				}
			}
		}
		if (strpos($this->getOption('outputtype'), 'ul') === 0) {
			if (count($ret)) {
				$out = '<ul class="tracker_field_category">';
				foreach($ret as $li) {
					$out .= '<li>' . $li . '</li>';
				}
				$out .= '</ul>';
				return $out;
			} else {
				return '';
			}
		} else {
			return implode('<br/>', $ret);
		}
	}

	public function handleSave($value, $oldValue)
	{
		return array(
			'value' => $value,
		);
	}

	public function watchCompare($old, $new)
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

	private function describeCategoryList($categs)
	{
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
		static $cache = array();
		$fieldId = $this->getConfiguration('fieldId');

		if (! isset($cache[$fieldId])) {
			$parentId = (int) $this->getOption('parentId');
			$descends = $this->getOption('descendants') > 0;
			if ($parentId > 0) {
				$data = TikiLib::lib('categ')->getCategories(array('identifier'=>$parentId, 'type'=>$descends ? 'descendants' : 'children'));
			} else {
				$data = TikiLib::lib('categ')->getCategories(array('type' => $descends ? 'all' : 'roots'));
			}

			$cache[$fieldId] = $data;
		}

		return $cache[$fieldId];
	}

	private function getCategories($itemId)
	{
		return TikiLib::lib('categ')->get_object_categories('trackeritem', $itemId);
	}

	public function importRemote($value)
	{
		return $value;
	}

	public function exportRemote($value)
	{
		return $value;
	}

	public function importRemoteField(array $info, array $syncInfo)
	{
		$sourceOptions = explode(',', $info['options']);
		$parentId = isset($sourceOptions[0]) ? (int) $sourceOptions[0] : 0;
		$fieldType = isset($sourceOptions[1]) ? $sourceOptions[1] : 'd';
		$desc = isset($sourceOptions[3]) ? (int) $sourceOptions[3] : 0;

		$info['options'] = $this->getRemoteCategoriesAsOptions($syncInfo, $parentId, $desc);

		if ($fieldType == 'm' || $fieldType == 'checkbox') {
			$info['type'] = 'M';
		} else {
			$info['type'] = 'd';
		}

		return $info;
	}

	private function getRemoteCategoriesAsOptions($syncInfo, $parentId, $descending)
	{
		$controller = new Services_RemoteController($syncInfo['provider'], 'category');
		$categories = $controller->list_categories(
			array(
				'parentId' => $parentId,
				'descends' => $descending,
			)
		);

		$parts = array();
		foreach ($categories as $categ) {
			$parts[] = $categ['categId'] . '=' . $categ['name'];
		}

		return implode(',', $parts);
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');
		$type = $this->getOption('inputtype');

		$sourceCategories = $this->getApplicableCategories();
		$applicable = $this->getIds($sourceCategories);
		$invert = array_flip(array_map(function ($item) {
			return $item['name'];
		}, $sourceCategories));

		$matching = function ($extra) use ($applicable) {
			if (isset($extra['categories'])) {
				// Directly from search results
				return array_intersect($extra['categories'], $applicable);
			} elseif (isset($extra['itemId'])) {
				// Not loaded, fetch list
				$categories = $this->getCategories($extra['itemId']);
				return array_intersect($categories, $applicable);
			} else {
				return [];
			}
		};

		if ($type == 'd' || $type == 'radio') {

			// Works for single selection only
			$schema->addNew($permName, 'id')
				->setLabel($name)
				->addQuerySource('categories', 'categories')
				->setRenderTransform(function ($value, $extra) use ($matching) {
					$categories = $matching($extra);
					if (count($categories) > 1) {
						return '#invalid';
					} else {
						return reset($categories);
					}
				})
				->setParseIntoTransform(function (& $info, $value) use ($permName) {
					if ($value != '#invalid') {
						$info['fields'][$permName] = $value;
					}
				})
				;

			$schema->addNew($permName, 'name')
				->setLabel($name)
				->addQuerySource('categories', 'categories')
				->setRenderTransform(function ($value, $extra) use ($matching, $sourceCategories) {
					$categories = $matching($extra);
					if (count($categories) > 1) {
						return '#invalid';
					} else {
						$first = reset($categories);
						if (isset($sourceCategories[$first])) {
							return $sourceCategories[$first]['name'];
						}
					}
				})
				->setParseIntoTransform(function (& $info, $value) use ($permName, $invert) {
					if (isset($invert[$value])) {
						$info['fields'][$permName] = $invert[$value];
					}
				})
				;
		} else {

			// Handle multi-selection fields
			$schema->addNew($permName, 'multi-id')
				->setLabel($name)
				->addQuerySource('categories', 'categories')
				->setRenderTransform(function ($value, $extra) use ($matching) {
					$categories = $matching($extra);
					return implode(';', $categories);
				})
				->setParseIntoTransform(function (& $info, $value) use ($permName) {
					$values = explode(';', $value);
					$values = array_map('trim', $values);
					$info['fields'][$permName] = implode(',', array_filter($values));
				})
				;

			$schema->addNew($permName, 'multi-name')
				->setLabel($name)
				->addQuerySource('categories', 'categories')
				->setRenderTransform(function ($value, $extra) use ($matching, $sourceCategories) {
					$categories = $matching($extra);
					$categories = array_map(function ($cat) use ($sourceCategories) {
						if (isset($sourceCategories[$cat])) {
							return $sourceCategories[$cat]['name'];
						}
					}, $categories);
					
					return implode('; ', array_filter($categories));
				})
				->setParseIntoTransform(function (& $info, $value) use ($permName, $invert) {
					$values = explode(';', $value);
					$values = array_map('trim', $values);
					$values = array_map(function ($name) use ($invert) {
						if (isset($invert[$name])) {
							return $invert[$name];
						}
					}, $values);

					$info['fields'][$permName] = implode(',', array_filter($values));
				})
				;
		}

		return $schema;
	}
}

