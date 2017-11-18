<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for dropdown
 *
 * Letter key: ~d~ ~D~ ~R~ ~M~
 *
 */
class Tracker_Field_Dropdown extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Search_FacetProvider_Interface, Tracker_Field_Exportable, Tracker_Field_Filterable
{
	public static function getTypes()
	{
		return [
			'd' => [
				'name' => tr('Dropdown'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'help' => 'Drop Down - Radio Tracker Field',
				'prefs' => ['trackerfield_dropdown'],
				'tags' => ['basic'],
				'default' => 'y',
				'supported_changes' => ['d', 'D', 'R', 'M', 'm', 't', 'a', 'L'],
				'params' => [
					'options' => [
						'name' => tr('Option'),
						'description' => tr('If an option contains an equal sign, the part before the equal sign will be used as the value, and the second part as the label'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					],
				],
			],
			'D' => [
				'name' => tr('Dropdown selector with "Other" field'),
				'description' => tr('Allows users to select from a specified set of options or to enter an alternate option'),
				'help' => 'Drop Down - Radio Tracker Field',
				'prefs' => ['trackerfield_dropdownother'],
				'tags' => ['basic'],
				'default' => 'n',
				'supported_changes' => ['d', 'D', 'R', 'M', 'm', 't', 'a', 'L'],
				'params' => [
					'options' => [
						'name' => tr('Option'),
						'description' => tr('If an option contains an equal sign, the part before the equal sign will be used as the value, and the second part as the label.') . ' ' . tr('To change the label of the "Other" option, use "other=Label".'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					],
				],
			],
			'R' => [
				'name' => tr('Radio Buttons'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'help' => 'Drop Down - Radio Tracker Field',
				'prefs' => ['trackerfield_radio'],
				'tags' => ['basic'],
				'default' => 'y',
				'supported_changes' => ['d', 'D', 'R', 'M', 'm', 't', 'a', 'L'],
				'params' => [
					'options' => [
						'name' => tr('Option'),
						'description' => tr('If an option contains an equal sign, the part before the equal sign will be used as the value, and the second part as the label'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					],
				],
			],
			'M' => [
				'name' => tr('Multiselect'),
				'description' => tr('Allows a user to select multiple values from a specified set of options'),
				'help' => 'Multiselect Tracker Field',
				'prefs' => ['trackerfield_multiselect'],
				'tags' => ['basic'],
				'default' => 'y',
				'supported_changes' => ['M', 'm', 't', 'a', 'L'],
				'params' => [
					'options' => [
						'name' => tr('Option'),
						'description' => tr('If an option contains an equal sign, the part before the equal sign will be used as the value, and the second part as the label'),
						'filter' => 'text',
						'count' => '*',
						'legacy_index' => 0,
					],
					'inputtype' => [
						'name' => tr('Input Type'),
						'description' => tr('User interface control to be used.'),
						'default' => '',
						'filter' => 'alpha',
						'options' => [
							'' => tr('Multiple-selection checkboxes'),
							'm' => tr('List box'),
						],
					],
				],
			],
		];
	}

	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		return new Tracker_Field_Dropdown($fieldInfo, $itemData, $trackerDefinition);
	}

	function getFieldData(array $requestData = [])
	{

		$ins_id = $this->getInsertId();

		if (! empty($requestData['other_' . $ins_id])) {
			$value = $requestData['other_' . $ins_id];
		} elseif (isset($requestData[$ins_id])) {
			$value = implode(',', (array) $requestData[$ins_id]);
		} elseif (isset($requestData[$ins_id . '_old'])) {
			$value = '';
		} else {
			$value = $this->getValue($this->getDefaultValue());
		}

		return [
			'value' => $value,
			'selected' => $value === '' ? [] : explode(',', $value),
			'possibilities' => $this->getPossibilities(),
		];
	}

	function renderInput($context = [])
	{
		return $this->renderTemplate('trackerinput/dropdown.tpl', $context);
	}

	function renderInnerOutput($context = [])
	{
		if (! empty($context['list_mode']) && $context['list_mode'] === 'csv') {
			return implode(', ', $this->getConfiguration('selected'));
		} else {
			$labels = array_map([$this, 'getValueLabel'], $this->getConfiguration('selected'));
			return implode(', ', $labels);
		}
	}

	private function getValueLabel($value)
	{
		$possibilities = $this->getPossibilities();
		if (isset($possibilities[$value])) {
			return $possibilities[$value];
		} else {
			return $value;
		}
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}

	private function getPossibilities()
	{
		static $localCache = [];

		$string = $this->getConfiguration('options');
		if (! isset($localCache[$string])) {
			$options = $this->getOption('options');

			if (empty($options)) {
				return [];
			}

			$out = [];
			foreach ($options as $value) {
				$out[$this->getValuePortion($value)] = $this->getLabelPortion($value);
			}

			$localCache[$string] = $out;
		}

		return $localCache[$string];
	}

	private function getDefaultValue()
	{
		$options = $this->getOption('options');

		$parts = [];
		$last = false;
		foreach ($options as $opt) {
			if ($last === $opt) {
				$parts[] = $this->getValuePortion($opt);
			}

			$last = $opt;
		}

		return implode(',', $parts);
	}

	private function getValuePortion($value)
	{
		if (false === $pos = strpos($value, '=')) {
			return $value;
		} else {
			return substr($value, 0, $pos);
		}
	}

	private function getLabelPortion($value)
	{
		if (false === $pos = strpos($value, '=')) {
			return $value;
		} else {
			return substr($value, $pos + 1);
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$value = $this->getValue();
		$label = $this->getValueLabel($value);
		$baseKey = $this->getBaseKey();

		return [
			$baseKey => $typeFactory->identifier($value),
			"{$baseKey}_text" => $typeFactory->sortable($label),
		];
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return [$baseKey, $baseKey . '_text'];
	}

	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();
		return ["{$baseKey}_text" => true];
	}

	function getFacets()
	{
		$baseKey = $this->getBaseKey();
		return [
			Search_Query_Facet_Term::fromField($baseKey)
				->setLabel($this->getConfiguration('name'))
				->setRenderMap($this->getPossibilities())
		];
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');

		$possibilities = $this->getPossibilities();
		$invert = array_flip($possibilities);

		$schema->addNew($permName, 'code')
			->setLabel($name)
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			})
			;

		$schema->addNew($permName, 'text')
			->setLabel($name)
			->addIncompatibility($permName, 'code')
			->addQuerySource('text', "tracker_field_{$permName}_text")
			->setRenderTransform(function ($value, $extra) use ($possibilities) {
				if (isset($possibilities[$value])) {
					return $possibilities[$value];
				}
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName, $invert) {
				if (isset($invert[$value])) {
					$info['fields'][$permName] = $invert[$value];
				}
			})
			;

		return $schema;
	}

	function getFilterCollection()
	{
		$filters = new Tracker\Filter\Collection($this->getTrackerDefinition());
		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');
		$baseKey = $this->getBaseKey();

		$possibilities = $this->getPossibilities();
		$possibilities['-Blank (no data)-'] = tr('-Blank (no data)-');

		$filters->addNew($permName, 'dropdown')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\DropDown("tf_{$permName}_dd", $possibilities))
			->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
				$value = $control->getValue();

				if ($value === '-Blank (no data)-') {
					$query->filterIdentifier('', $baseKey . '_text');
				} elseif ($value) {
					$query->filterIdentifier($value, $baseKey);
				}
			});

		$filters->addNew($permName, 'multiselect')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\MultiSelect("tf_{$permName}_ms", $possibilities))
			->setApplyCondition(function ($control, Search_Query $query) use ($permName, $baseKey) {
				$values = $control->getValues();

				if (! empty($values)) {
					$sub = $query->getSubQuery("ms_$permName");

					foreach ($values as $v) {
						if ($v === '-Blank (no data)-') {
							$sub->filterIdentifier('', $baseKey . '_text');
						} elseif ($v) {
							$sub->filterContent((string) $v, $baseKey);
						}
					}
				}
			});

		return $filters;
	}
}
