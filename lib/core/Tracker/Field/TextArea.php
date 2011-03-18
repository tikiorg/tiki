<?php

/**
 * Handler class for TextArea
 * 
 * Letter key: ~a~
 *
 */
class Tracker_Field_TextArea extends Tracker_Field_Text
{
	function getFieldData(array $requestData = array())
	{
		$data = $this->processMultilingual($requestData, $this->getInsertId());

		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/textarea.tpl', $context);
	}
}

