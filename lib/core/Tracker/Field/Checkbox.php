<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_Checkbox extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'c' => array(
				'name' => tr('Checkbox'),
				'description' => tr('Provides a cehckbox field for yes/no, on/off input.'),
				'params' => array(
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		if (isset($requestData[$ins_id]) && $requestData[$ins_id] == 'on') {
			$val = 'y';
		} elseif (!empty($requestData)) {
			$val = 'n';
		} else {
			$val = $this->getValue();
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
}

