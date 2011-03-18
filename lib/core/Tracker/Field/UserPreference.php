<?php

/**
 * Handler class for User preference
 * 
 * Letter key: ~p~
 *
 */
class Tracker_Field_UserPreference extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		
		if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];
		} else {
			global $trklib, $userlib;
	
			$value = '';
			$itemId = $this->getItemId();
			
			if ($itemId) {
				$itemUser = $this->getTrackerDefinition()->getItemUser($itemId);
		
				if (!empty($itemUser)) {
					if ($this->getOption(0) == 'email') {
						$value = $userlib->get_user_email($itemUser);
					} else {
						$value = $userlib->get_user_preference($itemUser, $this->getOption(0));
					}
				}
			}
		}
					
		return array('value' => $value);
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/userpreference.tpl', $context);
	}
}

