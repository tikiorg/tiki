<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_Checkbox extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
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

		if (isset($requestData[$ins_id])) {
			if ($requestData[$ins_id] == 'on') {
				$val = 'y';
			} else {
				$val = 'n';
			}
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

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		$checked = $this->getValue() === 'y';

		return array(
			$baseKey => $typeFactory->identifier($checked ? 'y' : 'n'),
		);
	}
}

