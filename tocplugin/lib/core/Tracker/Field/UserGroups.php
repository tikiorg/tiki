<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	public static function getTypes()
	{
		return array(
			'usergroups' => array(
				'name' => tr('User Groups'),
				'description' => tr('Displays the list of groups for the user associated with the tracker items.'),
				'help' => 'User Groups',
				'prefs' => array('trackerfield_usergroups'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$itemId = $this->getItemId();

		$value = array();
		
		if ($itemId) {
			$itemUser = $this->getTrackerDefinition()->getItemUser($itemId);
			
			if (!empty($itemUser)) {
				$tikilib = TikiLib::lib('tiki');
				$value = array_diff($tikilib->get_user_groups($itemUser), array('Registered', 'Anonymous'));
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

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		$data = $this->getFieldData();
		$listtext = implode(' ', $data['value']);

		return array(
			$baseKey => $typeFactory->multivalue($data['value']),
			"{$baseKey}_text" => $typeFactory->plaintext($listtext),
		);
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return array($baseKey);
	}

	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();
		return array($baseKey => true);
	}
}
