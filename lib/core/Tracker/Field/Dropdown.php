<?php

/**
 * Handler class for dropdown
 * 
 * Letter key: ~d~ ~D~
 *
 */
class Tracker_Field_Dropdown extends Tracker_Field_Abstract
{
	private $type;
	
	function __construct($fieldInfo, $itemData, $trackerDefinition, $type)
	{
		$this->type = $type;
		parent::__construct($fieldInfo, $itemData, $trackerDefinition);
	}

	function getFieldData(array $requestData = array())
	{
		
		$ins_id = $this->getInsertId();

		if (!empty($requestData['other_'.$this->getInsertId()])) {
			$value = $requestData['other_'.$this->getInsertId()];
		} elseif (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue();
		}
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/dropdown.tpl', $context);
	}
}

