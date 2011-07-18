<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for InGroup
 * 
 * Letter key: ~N~
 *
 */
class Tracker_Field_InGroup extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'N' => array(
				'name' => tr('In Group'),
				'description' => tr('Indicates if the user associated to the item is member of a specified group.'),
				'readonly' => true,
				'params' => array(
					'groupName' => array(
						'name' => tr('Group Name'),
						'description' => tr('Name of the group to verify'),
						'filter' => 'groupname'
					),
					'type' => array(
						'name' => tr('Display'),
						'description' => tr('How to display the result'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('Yes/No'),
							'date' => tr('Join date'),
						),
					),
				),
			),
		);
	}

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

