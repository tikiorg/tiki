<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Checkboxes
 * 
 * Letter key: ~c~
 *
 */
class Tracker_Field_Checkbox extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable, Tracker_Field_Filterable
{
	public static function getTypes()
	{
		return array(
			'c' => array(
				'name' => tr('Checkbox'),
				'description' => tr('Provides a checkbox field for yes/no, on/off input.'),
				'help' => 'Checkbox Tracker Field',					
				'prefs' => array('trackerfield_checkbox'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		if (!empty($requestData[$ins_id])) {
			$val = 'y';
		} elseif (!empty($requestData[$ins_id . '_old'])) {
			$val = 'n';
		} else {
			$val = $this->getValue();
			if (empty($val)) {
				$val = 'n';
			}
		}
		return array(
			'value' => $val,
		);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/checkbox.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		if ($value == 'on') {
			$value = 'y';
		}

		return array(
			'value' => $value,
		);
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

	function renderOutput($context = array())
	{
		$fieldData = $this->getFieldData();
		if ($fieldData['value'] == 'y' && $context['list_mode'] !== 'csv') {
			return tra('Yes');
		} elseif ($fieldData['value'] == 'n' && $context['list_mode'] !== 'csv') {
			return tra('No');
		} else {
			return $fieldData['value']; 
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		$checked = $this->getValue() === 'y';

		return array(
			$baseKey => $typeFactory->identifier($checked ? 'y' : 'n'),
		);
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_icon');

		$schema->addNew($permName, 'y/n')
			->setLabel($name)
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			})
			;
		$schema->addNew($permName, 'X')
			->setLabel($name)
			->addIncompatibility($permName, 'y/n')
			->setRenderTransform(function ($value) {
				return ('y' === $value) ? 'X' : '';
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$value = trim($value);
				$info['fields'][$permName] = empty($value) ? 'n' : 'y';
			})
			;
		$schema->addNew($permName, 'icon')
			->setLabel($name)
			->setPlainReplacement('X')
			->setRenderTransform(function ($value) use ($smarty) {
				return ('y' === $value) ? smarty_function_icon(['name' => 'success'], $smarty) : '';
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

		$possibilities = [
			'selected' => tr('Yes'),
			'unselected' => tr('No'),
		];

		$filters->addNew($permName, 'dropdown')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\DropDown("tf_{$permName}_ck", $possibilities))
			->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
				$value = $control->getValue();

				if ($value == 'selected') {
					$query->filterContent('y', $baseKey);
				} elseif ($value == 'unselected') {
					$query->filterContent('NOT y', $baseKey);
				}
			});

		return $filters;
	}
}
