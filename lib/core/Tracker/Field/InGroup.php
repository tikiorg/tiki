<?php

/**
 * Handler class for InGroup
 * 
 * Letter key: ~N~
 *
 */
class Tracker_Field_InGroup extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		return array();
	}

	function renderInput($context = array())
	{
		$this->renderOutput($context);	// read only
	}
	
	function renderOutput($context = array()) {
		$trklib = TikiLib::lib('trk');
		$itemUser = $trklib->get_item_creator($this->getConfiguration('trackerId'), $this->getItemId());
		
		if (!empty($itemUser)) {
			if (!isset($trklib->tracker_infocache['users_group'][$this->getOption(0)])) {
				$userlib = TikiLib::lib('user');
				$trklib->tracker_infocache['users_group'][$this->getOption(0)] = $userlib->get_users_created_group($this->getOption(0));
			}
			if (isset($trklib->tracker_infocache['users_group'][$this->getOption(0)][$itemUser])) {
				if ($this->getOption(1) == 'date') {
					$value = $trklib->tracker_infocache['users_group'][$this->getOption(0)][$itemUser];
				} else {
					$value = 'Yes';
				}
			} else {
				if ($this->getOption(1) == 'date') {
					$value = '';
				} else {
					$value = 'No';
				}
			}
		}
		
		if ($this->getOption(1) === 'date') {
			if (!empty($value)) {
				return TikiLib::lib('tiki')->get_short_date($value);
			}
		} else {
			return tra($value);
		}
	}
}

