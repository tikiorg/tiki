<?php

/**
 * Handler class for UserGroups
 * 
 * Letter key: ~usergroups~
 *
 */
class Tracker_Field_UserGroups extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		
		if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];
		} else {
			$itemId = $this->getItemId();
			
			if ($itemId) {
				$itemUser = $this->getTrackerDefinition()->getItemUser($itemId);
				
				if (!empty($itemUser)) {
					global $tikilib;
					$value = array_diff($tikilib->get_user_groups($itemUser), array('Registered', 'Anonymous'));
				}
			}
		}
		
		return array('value' => $value);
	}
	
	function renderInput($context = array())
	{
		return null;		
	}
	
	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/usergroups.tpl', $context);
	}
}