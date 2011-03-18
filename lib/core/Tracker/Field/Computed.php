<?php

/**
 * Handler class for Computed
 * 
 * Letter key: ~C~
 *
 */
class Tracker_Field_Computed extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		$data = array();
		
		if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];
		} else if ($this->getItemId()) {
			$fields = $this->getTrackerDefinition()->getFields();
			$values = $this->getItemData();
			$option = $this->getOption(0);
			
			if ($option) {
				$calc = preg_replace('/#([0-9]+)/', '$values[\1]', $option);
				// FIXME: kill eval()
				eval('$computed = ' . $calc . ';');
				$value = $computed;
				
				$trklib = TikiLib::lib('trk');
				
				$infoComputed = $trklib->get_computed_info(
					$this->getOption(0),
					$this->getTrackerDefinition()->getConfiguration('trackerId'),
					$fields
				);
				
				if ($infoComputed) {
					$data = array_merge($data, $infoComputed);
				}
			}
		}
		
		$data['value'] = $value;

		return $data;
	}
	
	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/computed.tpl', $context);
	}
	
	function renderInput($context = array())
	{
		return $this->renderOutput($context);
	}
}