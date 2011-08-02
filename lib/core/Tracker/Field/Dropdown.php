<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for dropdown
 * 
 * Letter key: ~d~ ~D~
 *
 */
class Tracker_Field_Dropdown extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'd' => array(
				'name' => tr('Drop Down'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label'),
						'filter' => 'text',
						'count' => '*',
					),
				),
			),
			'D' => array(
				'name' => tr('Drop Down with Other field'),
				'description' => tr('Allows users to select from a specified set of options or to enter an alternate option'),
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label'),
						'filter' => 'text',
						'count' => '*',
					),
				),
			),
			'R' => array(
				'name' => tr('Radio Buttons'),
				'description' => tr('Allows users to select only from a specified set of options'),
				'params' => array(
					'options' => array(
						'name' => tr('Option'),
						'description' => tr('An option, if containing an equal sign, the prior part will be used as the value while the later as the label'),
						'filter' => 'text',
						'count' => '*',
					),
				),
			),
		);
	}

	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		switch ($type) {
			case 'd':
				return new Tracker_Field_Dropdown($fieldInfo, $itemData, $trackerDefinition);
			case 'D':
				return new Tracker_Field_Dropdown($fieldInfo, $itemData, $trackerDefinition);
			case 'R':
				return new Tracker_Field_Dropdown($fieldInfo, $itemData, $trackerDefinition);
		}
	}
	
	function getFieldData(array $requestData = array())
	{
		
		$ins_id = $this->getInsertId();

		if (!empty($requestData['other_'.$this->getInsertId()])) {
			$value = $requestData['other_'.$this->getInsertId()];
		} elseif (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue($this->getDefaultValue());
		}

		return array(
			'value' => $value,
			'possibilities' => $this->getPossibilities(),
		);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/dropdown.tpl', $context);
	}

	function renderInnerOutput($context)
	{
		$value = $this->getConfiguration('value');
		return $this->getValueLabel($value);
	}

	private function getValueLabel($value)
	{
		$possibilities = $this->getConfiguration('possibilities');
		if (isset($possibilities[$value])) {
			return $possibilities[$value];
		} else {
			return $value;
		}
	}

	function import($value)
	{
		return $value;
	}

	function export($value)
	{
		return $value;
	}

	function importField(array $info, array $syncInfo)
	{
		return $info;
	}

	private function getPossibilities()
	{
		$options = $this->getConfiguration('options_array');
		$out = array();
		foreach ($options as $value) {
			$out[$this->getValuePortion($value)] = $this->getLabelPortion($value);
		}

		return $out;
	}
	
	private function getDefaultValue()
	{
		$options = $this->getConfiguration('options_array');
		
		$last = false;
		foreach ($options as $opt) {
			if ($last === $opt) {
				return $this->getValuePortion($opt);
			} else {
				$last = $opt;
			}
		}

		return null;
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
}

