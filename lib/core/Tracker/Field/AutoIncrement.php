<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_AutoIncrement extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'q' => array(
				'name' => tr('Auto-Increment'),
				'description' => tr('Allows an incrementing value field, or itemId field.'),
				'readonly' => true,
				'params' => array(
					'start' => array(
						'name' => tr('Start'),
						'description' => tr('The starting value for the field'),
						'default' => 1,
						'filter' => 'int',
					),
					'prepend' => array(
						'name' => tr('Prepend'),
						'description' => tr('Text that will be displayed before the field'),
						'filter' => 'text',
					),
					'append' => array(
						'name' => tr('Append'),
						'description' => tr('Text that will be displayed after the field'),
						'filter' => 'text',
					),
					'itemId' => array(
						'name' => tr('Item ID'),
						'description' => tr('If set to "itemId", will set this field to match the value of the actual database itemId field value'),
						'filter' => 'alpha',
						'options' => array(
							'' => '',
							'itemId' => 'itemId',
						),
					),
				),
			),
		);
	}

	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		return new self($fieldInfo, $itemData, $trackerDefinition);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$value = isset($requestData[$ins_id]) ? $requestData[$ins_id] : $this->getValue();

		$append = $this->getOption(1);
		if (!empty($append)) {
			$value = "<span class='formunit'>$append</span>" . $value;
		}
	
		$prepend = $this->getOption(2);
		if (!empty($prepend)) {
			$value .= "<span class='formunit'>$prepend</span>";
		}
			
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/autoincrement.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		$value = false;
		if ($this->getOption(3) == 'itemId') {
			$value = $this->getItemId();
		} elseif (is_null($oldValue)) {
			$value = TikiLib::lib('trk')->get_maximum_value($this->getConfiguration('fieldId'));
			if (! $value) {
				$value = $this->getOption(0, 1);
			} else {
				$value += 1;
			}
		}

		return array(
			'value' => $value,
		);
	}
}

