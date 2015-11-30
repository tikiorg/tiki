<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Category
 *
 * Letter key: ~e~
 *
 * N.B. Implements Tracker_Field_Indexable so items can be recategorised when indexing
 */
class Tracker_Field_Category extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable, Tracker_Field_Filterable, Tracker_Field_Indexable
{
	public static function getTypes()
	{
		return array(
			'e' => array(
				'name' => tr('Category'),
				'description' => tr('Allows the tracker item to be categorized in one or more categories under the specified main category.'),
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
							'd' => tr('Dropdown'),
							'radio' => tr('Radio buttons'),
							'm' => tr('List box'),
							'checkbox' => tr('Multiple-selection checkboxes'),
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
						'description' => tr('Display all descendants instead of only first-level descendants'),
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
						'description' => tr(''),
						'filter' => 'word',
						'options' => array(
							'' => tr('Plain list with items separated by line breaks (default)'),
							'links' => tr('Links separated by line breaks'),
							'ul' => tr('Unordered list of labels'),
							'ulinks' => tr('Unordered list of links'),
						),
					),
					'doNotInheritCategories' => array(
						'name' => tr('Do not Inherit Categories'),
						'description' => tr("Tracker items will inherit their tracker's categories unless this option is set."),
						'filter' => 'int',
						'options' => array(
							0 => tr('Inherit (default)'),
							1 => tr('Do not inherit'),
						),
					),
					'recategorize' => array(
						'name' => tr('Recategorization event'),
						'type' => 'list',
						'description' => tr('Set this to "Indexing" to recategorize the items during reindexing as well as when saving.'),
						'filter' => 'word',
						'options' => array(
							'save' => tr('Save'),
							'index' => tr('Indexing'),
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

		if (isset($requestData[$key])) {
			if (!is_array($requestData[$key])) {
				$selected = array($requestData[$key]);
			} else {
				$selected = $requestData[$key];
			}
		} else if (isset($requestData['cat_managed'])) {
			$selected = array();
		} elseif ($this->getItemId() && !isset($requestData[$key])) {
			// only show existing category of not receiving request, otherwise might be uncategorization in progress
			$selected = $this->getCategories($this->getItemId());
		} else {
			$selected = TikiLib::lib('categ')->get_default_categories();
		}

		$tracker_categories = array();

		if (! $this->getOption('doNotInheritCategories')) {
			// use the parent tracker categories by default for new items
			$tracker_categories = TikiLib::lib('categ')->get_object_categories('tracker', $this->getConfiguration('trackerId'));
			// for now just merge these, category jail will get enforced later
			$selected = array_unique(array_merge($selected, $tracker_categories));
		}

		$categories = $this->getApplicableCategories();
		$selected = array_intersect($selected, $this->getIds($categories));

		$data = array(
			'value' => implode(',', $selected),
			'selected_categories' => $selected,
			'tracker_categories' => $tracker_categories,
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
			$tracker_categories = $this->getConfiguration('tracker_categories');
			if (is_array($tracker_categories) && !empty($tracker_categories)) {
				foreach ($categories as & $cat) {
					$cat['canchange'] = ! in_array($cat['categId'], $tracker_categories);
				}
				$changeall = false;
			} else {
				$changeall = true;
			}
			$selected_categories = $this->getConfiguration('selected_categories');
			$smarty->assign_by_ref('categories', $categories);
			$cat_tree = TikiLib::lib('categ')->generate_cat_tree($categories, $changeall, $selected_categories);
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
		if (is_array($value) && isset($value['incremental'])) {
			// List of updates coming from import (see addCheckboxColumn)
			$list = $value['incremental'];
			$value = $this->getCategories($this->getItemId());

			$value = array_diff($value, $list['-']);
			$value = array_merge($value, $list['+']);
			$value = array_unique($value);
			$value = implode(',', $value);
		}

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
			static $lastId, $categories;

			if ($lastId == $extra['itemId']) {
				return $categories;
			}

			if (isset($extra['categories'])) {
				// Directly from search results
				$categories = array_intersect($extra['categories'], $applicable);
			} elseif (isset($extra['itemId'])) {
				// Not loaded, fetch list
				$categories = $this->getCategories($extra['itemId']);
				$categories = array_intersect($categories, $applicable);
			} else {
				$categories = [];
			}

			$lastId = $extra['itemId'];
			return $categories;
		};

		if ($type == 'd' || $type == 'radio') {

			// Works for single selection only
			$schema->addNew($permName, 'id')
				->setLabel($name)
				->addQuerySource('itemId', 'object_id')
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
				->addIncompatibility($permName, 'id')
				->addQuerySource('itemId', 'object_id')
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
				->addQuerySource('itemId', 'object_id')
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
				->addIncompatibility($permName, 'multi-id')
				->addQuerySource('itemId', 'object_id')
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

		foreach ($sourceCategories as $cat) {
			$this->addCheckboxColumn($schema, $matching, $permName, $cat['categId'], $cat['name']);
		}

		return $schema;
	}

	private function addCheckboxColumn($schema, $matching, $permName, $categId, $categName)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_icon');

		$schema->addNew($permName, 'icon-' . $categId)
			->setLabel($categName)
			->addQuerySource('itemId', 'object_id')
			->addQuerySource('categories', 'categories')
			->setPlainReplacement('check-' . $categId)
			->setRenderTransform(function ($value, $extra) use ($smarty, $matching, $categId) {
				$categories = $matching($extra);

				return in_array($categId, $categories) ? smarty_function_icon(['name' => 'success'], $smarty) : '';
			})
			;
		$schema->addNew($permName, 'check-' . $categId)
			->setLabel($categName)
			->addIncompatibility($permName, 'id')
			->addIncompatibility($permName, 'name')
			->addIncompatibility($permName, 'multi-id')
			->addIncompatibility($permName, 'multi-name')
			->addQuerySource('itemId', 'object_id')
			->addQuerySource('categories', 'categories')
			->setRenderTransform(function ($value, $extra) use ($matching, $categId) {
				$categories = $matching($extra);

				return in_array($categId, $categories) ? 'X' : '';
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName, $categId) {
				if (isset($info['fields'][$permName]) && ! isset($info['fields'][$permName]['incremental'])) {
					// Looks like an other field took this over
					// Do nothing
					return;
				}

				// Queue updates to be handled by handleSave as we do not know
				// which item we are operating on at this stage
				if (! isset($info['fields'][$permName])) {
					$info['fields'][$permName]['incremental'] = [
						'+' => [],
						'-' => [],
					];
				}

				$value = trim($value);
				if ($value == 'X' || $value == 'x') {
					$info['fields'][$permName]['incremental']['+'][] = $categId;
				} else {
					$info['fields'][$permName]['incremental']['-'][] = $categId;
				}
			});
			;
	}

	function getFilterCollection()
	{
		$collection = new Tracker\Filter\Collection($this->getTrackerDefinition());
		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');
		$baseKey = $this->getBaseKey();

		$sourceCategories = $this->getApplicableCategories();
		$options = array_map(function ($i) {
			return $i['relativePathString'];
		}, $sourceCategories);

		$collection->addNew($permName, 'dropdown')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\DropDown("tf_{$permName}_dd", $options))
			->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
				$value = $control->getValue();

				if ($value) {
					$query->filterCategory((string) $value);
				}
			})
			;

		$controls = [
			'any-of' => new Tracker\Filter\Control\MultiSelect("tf_{$permName}_anyd", $options),
			'any-of-checkboxes' => new Tracker\Filter\Control\InlineCheckboxes("tf_{$permName}_anyc", $options),
		];
		foreach ($controls as $key => $control) {
			$collection->addNew($permName, $key)
				->setLabel(tr('%0 (any of)', $name))
				->setControl($control)
				->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
					$values = $control->getValues();

					if (! empty($values)) {
						$query->filterCategory(implode(' OR ', $values));
					}
				})
				;
		}

		$type = $this->getOption('inputtype');

		if ($type == 'm' || $type == 'checkbox') {
			$controls = [
				'all-of' => new Tracker\Filter\Control\MultiSelect("tf_{$permName}_alld", $options),
				'all-of-checkboxes' => new Tracker\Filter\Control\InlineCheckboxes("tf_{$permName}_allc", $options),
			];
			foreach ($controls as $key => $control) {
				$collection->addNew($permName, $key)
					->setLabel(tr('%0 (all of)', $name))
					->setControl($control)
					->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
						$values = $control->getValues();

						if (! empty($values)) {
							$query->filterCategory(implode(' AND ', $values));
						}
					})
					;
			}
		}

		return $collection;
	}

	/**
	 * This updates and recategorise the item when being reindexed, which allows you to recategorise all a tracker's items
	 * if the parent tracker's categories have been changed (or following an upgrade for instance)
	 *
	 * Category fields don't actually need to be indexed as category objects are indexed separately.
	 *
	 * @param Search_Type_Factory_Interface $typeFactory
	 * @return array
	 * @throws Exception
	 */

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$value = array_filter(explode(',', $this->getValue()));

		if ($this->getOption('recategorize') === 'index') {

			// if using inherit this will get the tracker's categories too even if not saved
			$newValue = $this->getFieldData();
			$newValue = $newValue['selected_categories'];

			$diff = array_diff($newValue, $value);

			if ($diff) {		// unsaved categs found

				$categlib = TikiLib::lib('categ');
				$itemId = $this->getItemId();

				// update value
				TikiLib::lib('trk')->modify_field($itemId, $this->getConfiguration('fieldId'), implode(',', $newValue));

				// check current categs
				$categories = $categlib->get_object_categories('trackeritem', $itemId);
				$missingCategories = array_diff($diff, $categories);

				if ($missingCategories) {
					// temporarily prevent incremental index update which happens in categlib causing an infinite loop
					global $prefs;
					$incPref = $prefs['unified_incremental_update'];
					$prefs['unified_incremental_update'] = 'n';

					$categlib->categorize_any('trackeritem', $itemId, $missingCategories);

					$prefs['unified_incremental_update'] = $incPref;
				}
			}
		}
		// Indexing the value of the field anyway as it could be different from the 'categories' field if
		// you need to be more specific for filtering under a certain parent only.
		//
		// Warning to upgraders: older Tikis had no getDocumentPart so the comma-separated string was indexed
		// in the past. It now indexes an array. Use {$baseKey}_text instead for a space delimited string.
		$baseKey = $this->getBaseKey();
		return array(
			$baseKey => $typeFactory->multivalue($value),
			"{$baseKey}_text" => $typeFactory->plaintext(implode(' ', $value)),
		);
	}

	static public function syncCategoryFields($args)
	{
		if ($args['type'] == 'trackeritem') {
			$itemId = $args['object'];
			$trackerId = TikiLib::lib('trk')->get_tracker_for_item($itemId);
			$definition = Tracker_Definition::get($trackerId);
			if ($fieldIds = $definition->getCategorizedFields()) {
				foreach ($fieldIds as $fieldId) {
					$field = $definition->getField($fieldId);
					$handler = TikiLib::lib('trk')->get_field_handler($field);
					$data = $handler->getFieldData();
					$applicable = array_keys($data['list']);
					$value = $old_value = explode(",", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
					foreach ($args['added'] as $added) {
						if (!in_array($added, $applicable)) {
							continue;
						}
						if (!in_array($added, $value)) {
							$value[] = $added;
						}
					}
					foreach ($args['removed'] as $removed) {
						if (!in_array($removed, $applicable)) {
							continue;
						}
						if (in_array($removed, $value)) {
							$value = array_diff($value, [$removed]);
						}
					}
					if ($value != $old_value) {
						$value = implode(",", $value);
						TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
					}
				}
			}
		}
	}
}

