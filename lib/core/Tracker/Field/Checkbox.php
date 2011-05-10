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
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]) && $requestData[$ins_id] == 'on')
				? 'y'
				: $this->getValue('n'),
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

