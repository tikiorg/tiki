<?php

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
}

