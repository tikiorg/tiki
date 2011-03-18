<?php

/**
 * Handler class for numeric and currency field
 * 
 * Letter key: 
 *  numeric: ~n~
 *  currency: ~b~
 *
 */
class Tracker_Field_Numeric extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderInnerOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/numeric.tpl', $context);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/numeric.tpl', $context);
	}
}

