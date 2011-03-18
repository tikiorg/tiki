<?php

/**
 * Handler class for simple fields:
 * 
 * - email key ~m~
 * - ip key ~I~
 * - url key ~L~
 */
class Tracker_Field_Simple extends Tracker_Field_Abstract
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

		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate("trackerinput/{$this->type}.tpl", $context);
	}
}

