<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for CountrySelector
 *
 * Letter key: ~y~
 *
 */
class Tracker_Field_CountrySelector extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable, Tracker_Field_Filterable
{
	public static function getTypes()
	{
		return [
			'y' => [
				'name' => tr('Country Selector'),
				'description' => tr('Allows a selection from a specified list of countries'),
				'help' => 'Country Selector',
				'prefs' => ['trackerfield_countryselector'],
				'tags' => ['basic'],
				'default' => 'y',
				'params' => [
					'name_flag' => [
						'name' => tr('Display'),
						'description' => tr('Specify the rendering type for the field'),
						'filter' => 'int',
						'options' => [
							0 => tr('Name and flag'),
							1 => tr('Name only'),
							2 => tr('Flag only'),
						],
						'legacy_index' => 0,
					],
					'sortorder' => [
						'name' => tr('Sort order'),
						'description' => tr('Determines whether the ordering should be based on the translated name or the English name.'),
						'filter' => 'int',
						'options' => [
							0 => tr('Translated name'),
							1 => tr('English name'),
						],
						'legacy_index' => 1,
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		$ins_id = $this->getInsertId();

		$data = [
			'value' => isset($requestData[$ins_id])
				? $requestData[$ins_id]
				: $this->getValue(),
			'flags' => $this->getPossibilities(),
			'defaultvalue' => 'None',
		];

		return $data;
	}

	private function getPossibilities()
	{
		return TikiLib::lib('trk')->get_flags(true, true, ($this->getOption('sortorder') != 1));
	}

	function renderInnerOutput($context = [])
	{
		$flags = $this->getConfiguration('flags');
		$current = $this->getConfiguration('value');

		if (empty($current)) {
			return '';
		}
		$label = $flags[$current];
		$out = '';

		if ($context['list_mode'] != 'csv') {
			if ($this->getOption('name_flag') != 1) {
				$out .= $this->renderImage($current, $label);
			}
			if ($this->getOption('name_flag') == 0) {
				$out .= ' ';
			}
		}
		if ($this->getOption('name_flag') != 2) {
			$out .= $label;
		}

		return $out;
	}

	private function renderImage($code, $label)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_escape');
		return '<img src="img/flags/' . smarty_modifier_escape($code) . '.png" title="' . smarty_modifier_escape($label) . '" alt="' . smarty_modifier_escape($label) . '" />';
	}

	function renderInput($context = [])
	{
		return $this->renderTemplate('trackerinput/countryselector.tpl', $context);
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

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$possibilities = $this->getPossibilities();
		$value = $this->getValue();
		$label = isset($possibilities[$value]) ? $possibilities[$value] : '';
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
			->setRenderTransform(function ($value) use ($possibilities) {
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

		$schema->addNew($permName, 'flag')
			->setLabel($name)
			->setPlainReplacement('text')
			->setRenderTransform(function ($value) use ($possibilities) {
				if (isset($possibilities[$value])) {
					return $this->renderImage($value, $possibilities[$value]);
				}
			})
			;

		$schema->addNew($permName, 'flag-and-text')
			->setLabel($name)
			->setPlainReplacement('text')
			->setRenderTransform(function ($value) use ($possibilities) {
				if (isset($possibilities[$value])) {
					$label = $possibilities[$value];
					return $this->renderImage($value, $label) . ' ' . smarty_modifier_escape($label);
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
							$sub->filterIdentifier((string) $v, $baseKey);
						}
					}
				}
			});

		return $filters;
	}
}
