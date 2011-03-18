<?php

/**
 * Handler class for ItemsList
 * 
 * Letter key: ~l~
 *
 */
class Tracker_Field_ItemsList extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();

		$data = array(
			'value' => isset($requestData[$ins_id])
				? $requestData[$filter_id]
				: $this->getValue(),
		);

		if (isset($requestData['trackerId'], $requestData['itemId'])) {
			if ($this->getOption(3)) {
				$l = explode(':', $this->getOption(1));
				$finalFields = explode('|', $this->getOption(3));
				$data['links'] = TikiLib::lib('trk')->get_join_values(
						$requestData['trackerId'], $requestData['itemId'],
						array_merge( array($this->getOption(2)), $l, array($this->getOption(3))),
						$this->getOption(0), $finalFields,  ' ', $this->getOption(5)
				);
				if (count($data['links']) == 1) {
					foreach($data['links'] as $linkItemId => $linkValue) {
						if (is_numeric($data['links'][$linkItemId])) { // if later a computed field use this field
							$info[$this->getConfiguration('fieldId')] = $linkValue;
						}
					}
				}
				$data['trackerId'] = $this->getOption(0);
				$data['tracker_options'] = TikiLib::lib('trk')->get_tracker_options($this->getOption(0));
			}
		}
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/itemslist.tpl', $context);
	}
}

