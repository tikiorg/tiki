<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for Auto increment
 * 
 * Letter key: ~q~
 *
 */
class Tracker_Field_AutoIncrement extends Tracker_Field_Abstract implements Tracker_Field_Exportable, Tracker_Field_Filterable
{
	public static function getTypes()
	{
		return array(
			'q' => array(
				'name' => tr('Auto-Increment'),
				'description' => tr('Allows an incrementing value field, or itemId field.'),
				'readonly' => true,
				'help' => 'Auto-Increment Field',				
				'prefs' => array('trackerfield_autoincrement'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'start' => array(
						'name' => tr('Start'),
						'description' => tr('The starting value for the field'),
						'default' => 1,
						'filter' => 'int',
						'legacy_index' => 0,
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text that will be displayed before the field'),
						'filter' => 'text',
						'legacy_index' => 1,
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text that will be displayed after the field'),
						'filter' => 'text',
						'legacy_index' => 2,
					),
					'itemId' => array(
						'name' => tr('Item ID'),
						'description' => tr('If set to "itemId", will set this field to match the value of the actual database itemId field value'),
						'filter' => 'alpha',
						'options' => array(
							'' => '',
							'itemId' => 'itemId',
						),
						'legacy_index' => 3,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$value = isset($requestData[$ins_id]) ? $requestData[$ins_id] : $this->getValue();

		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/autoincrement.tpl', $context);
	}

	protected function renderInnerOutput($context = array())
	{
		$value = $this->getValue();
		$append = $this->getOption('prepend');
		if (!empty($append)) {
			$value = "<span class='formunit'>$append</span>" . $value;
		}
	
		$prepend = $this->getOption('append');
		if (!empty($prepend)) {
			$value .= "<span class='formunit'>$prepend</span>";
		}

		return $value;
	}

	function handleSave($value, $oldValue)
	{
		$value = false;
		if ($this->getOption('itemId') == 'itemId') {
			$value = $this->getItemId();
		} elseif (is_null($oldValue)) {
			$value = TikiLib::lib('trk')->get_maximum_value($this->getConfiguration('fieldId'));
			if (! $value) {
				$value = $this->getOption('start', 1);
			} else {
				$value += 1;
			}
		}

		return array(
			'value' => $value,
		);
	}

	function getTabularSchema()
	{
		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$permName = $this->getConfiguration('permName');
		$prepend = $this->getOption('prepend');
		$append = $this->getOption('append');

		$schema->addNew($permName, 'default')
			->setLabel($this->getConfiguration('name'))
			->setRenderTransform(function ($value) {
				return $value;
			})
			;
		$schema->addNew($permName, 'formatted')
			->setLabel($this->getConfiguration('name'))
			->addIncompatibility($permName, 'default')
			->setRenderTransform(function ($value) use ($prepend, $append) {
				return $prepend . $value . $append;
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


		$filters->addNew($permName, 'lookup')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\TextField("tf_{$permName}_lookup"))
			->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
				$value = $control->getValue();

				if ($value) {
					$query->filterIdentifier($value, $baseKey);
				}
			})
			;

		return $filters;
	}
}

