<?php

/**
 * Handler class for Static text
 * 
 * Letter key: ~S~
 *
 */
class Tracker_Field_StaticText extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		global $tikilib;
		
		$value = $this->getConfiguration('description');

		if ($this->getOption(0) == 1) {
			$value = $tikilib->parse_data($value);
		}
		
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/statictext.tpl', $context);
	}
}

