<?php

/**
 * Handler class for CountrySelector
 * 
 * Letter key: ~y~
 *
 */
class Tracker_Field_CountrySelector extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array(
			'value' => isset($requestData[$ins_id])
				? $requestData[$ins_id]
				: $this->getValue(),
			'flags' => TikiLib::lib('trk')->get_flags(true, true, ($this->getOption(1) != 1)),
			'defaultvalue' => 'None',
		);
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/countryselector.tpl', $context);
	}
}

