<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
		return $this->renderOutput($context);		
	}
	
	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/usergroups.tpl', $context);
	}
}
